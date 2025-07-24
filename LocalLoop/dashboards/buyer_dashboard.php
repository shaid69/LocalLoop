<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buyer Dashboard</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="dashboard">
<?php
session_start();
if ($_SESSION['user']['user_type'] !== 'buyer') exit("<div class='alert'>Access Denied</div>");
include('../config/config.php');
$id = $_SESSION['user']['id'];
?>
<h2>Buyer Dashboard</h2>
<?php include('../includes/nav.php'); ?>
<h3>My Bookings</h3>
<table>
  <thead>
    <tr>
      <th>Service</th>
      <th>Status</th>
      <th>Booking Time</th>
      <th>Comment</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
<?php
// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_comment'], $_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);
    $comment = trim($_POST['booking_comment']);
    // Only allow comment if booking belongs to this buyer
    $check = $conn->prepare("SELECT id FROM bookings WHERE id=? AND customer_id=?");
    $check->bind_param("ii", $booking_id, $id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE bookings SET comment=? WHERE id=?");
        $stmt->bind_param("si", $comment, $booking_id);
        $stmt->execute();
        $stmt->close();
        echo "<div class='alert'>Comment saved.</div>";
    }
    $check->close();
}

$sql = "SELECT b.id, b.status, b.booking_time, s.title, b.comment FROM bookings b JOIN services s ON b.service_id = s.id WHERE b.customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
  while ($row = $res->fetch_assoc()) {
      echo "<tr>";
      echo "<td>" . htmlspecialchars($row['title']) . "</td>";
      echo "<td>" . htmlspecialchars($row['status']) . "</td>";
      echo "<td>" . htmlspecialchars($row['booking_time']) . "</td>";
      echo "<td>";
      echo htmlspecialchars($row['comment']);
      echo "</td>";
      echo "<td>";
      // Allow comment if booking is completed or confirmed
      if (in_array(strtolower($row['status']), ['confirmed', 'completed'])) {
        echo '<form method="POST" style="display:inline;">
                <input type="hidden" name="booking_id" value="' . intval($row['id']) . '">
                <input type="text" name="booking_comment" value="' . htmlspecialchars($row['comment']) . '" placeholder="Add comment" style="width:120px;">
                <button type="submit">Save</button>
              </form>';
      } else {
        echo '-';
      }
      echo "</td>";
      echo "</tr>";
  }
} else {
  echo "<tr><td colspan='5'>No bookings found.</td></tr>";
}
$stmt->close();
?>
  </tbody>
</table>
</div>
</body>
</html>