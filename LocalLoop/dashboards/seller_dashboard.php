<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Seller Dashboard</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="dashboard">
<?php
session_start();
if ($_SESSION['user']['user_type'] !== 'seller') exit("<div class='alert'>Access Denied</div>");
include('../config/config.php');
$id = $_SESSION['user']['id'];
?>
<h2>Seller Dashboard</h2>
<?php include('../includes/nav.php'); ?>
<h3>My Services</h3>
<a class="button-link" href="add_service.php">Add New Service</a>
<div class="service-list">
<?php
$stmt = $conn->prepare("SELECT title, location, price, image, description FROM services WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
  while ($row = $res->fetch_assoc()) {
      echo '<div class="service-card">';
      if (!empty($row['image'])) {
          echo '<img src="../' . htmlspecialchars($row['image']) . '" alt="Service Image" class="service-img">';
      }
      echo '<div class="service-info">';
      echo '<h4>' . htmlspecialchars($row['title']) . '</h4>';
      echo '<p>' . htmlspecialchars($row['description']) . '</p>';
      echo '<span>' . htmlspecialchars($row['location']) . '</span> | ';
      echo '<span>₹' . htmlspecialchars($row['price']) . '</span>';
      echo '</div></div>';
  }
} else {
  echo "<div class='alert'>You have not added any services yet.</div>";
}
$stmt->close();
?>
</div>

<h3>My Bookings</h3>
<?php
// Handle booking status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $booking_id = intval($_POST['booking_id']);
    $status = in_array($_POST['status'], ['Pending', 'Confirmed', 'Rejected']) ? $_POST['status'] : 'Pending';
    // Only update if the booking belongs to this seller
    $check = $conn->prepare("SELECT b.id FROM bookings b JOIN services s ON b.service_id = s.id WHERE b.id = ? AND s.user_id = ?");
    $check->bind_param("ii", $booking_id, $id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $update = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $update->bind_param("si", $status, $booking_id);
        $update->execute();
        $update->close();
        echo "<div class='alert'>Booking status updated.</div>";
    }
    $check->close();
}
?>
<table>
  <thead>
    <tr>
      <th>Service</th>
      <th>Customer</th>
      <th>Status</th>
      <th>Booking Time</th>
      <th>Comment</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
<?php
$sql = "SELECT b.id, s.title, u.name as customer, b.status, b.booking_time, b.comment
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users u ON b.customer_id = u.id
        WHERE s.user_id = ?
        ORDER BY b.id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
  while ($row = $res->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
    echo "<td>" . htmlspecialchars($row['customer']) . "</td>";
    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
    echo "<td>" . htmlspecialchars($row['booking_time']) . "</td>";
    echo "<td>" . htmlspecialchars($row['comment']) . "</td>";
    echo "<td>";
    if ($row['status'] === 'Pending') {
      echo '<form method="POST" style="display:inline;">
              <input type="hidden" name="booking_id" value="' . intval($row['id']) . '">
              <select name="status">
                <option value="Pending">Pending</option>
                <option value="Confirmed">Confirmed</option>
                <option value="Rejected">Rejected</option>
              </select>
              <button type="submit">Update</button>
            </form>';
    } else {
      echo '-';
    }
    echo "</td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='6'>No bookings yet.</td></tr>";
}
$stmt->close();
?>
  </tbody>
</table>

<h3>My Reviews</h3>
<table>
  <thead>
    <tr>
      <th>Service</th>
      <th>Customer</th>
      <th>Rating</th>
      <th>Comment</th>
    </tr>
  </thead>
  <tbody>
<?php
$sql = "SELECT s.title, u.name as customer, r.rating, r.comment
        FROM reviews r
        JOIN services s ON r.service_id = s.id
        JOIN users u ON r.customer_id = u.id
        WHERE s.user_id = ?
        ORDER BY r.id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
  while ($row = $res->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
    echo "<td>" . htmlspecialchars($row['customer']) . "</td>";
    echo "<td>" . htmlspecialchars($row['rating']) . "</td>";
    echo "<td>" . htmlspecialchars($row['comment']) . "</td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='4'>No reviews yet.</td></tr>";
}
$stmt->close();
?>
  </tbody>
</table>
</div>
</body>
</html>