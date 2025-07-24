<?php
include('../config/config.php');
session_start();
if (!isset($_SESSION['user'])) header("Location: login.php");
$service_id = intval($_GET['id'] ?? 0);
$customer_id = intval($_SESSION['user']['id']);
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $time = $_POST['booking_time'];
    if (!$time) {
        $message = "Please select a booking time.";
    } else {
        $stmt = $conn->prepare("INSERT INTO bookings (service_id, customer_id, booking_time) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $service_id, $customer_id, $time);
        if ($stmt->execute()) {
            $message = "Booked!";
        } else {
            $message = "Booking failed.";
        }
        $stmt->close();
    }
}
?>
<?php if ($message): ?>
  <div class="alert"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<form method="POST">
  <label for="booking_time">Booking Time:</label>
  <input id="booking_time" name="booking_time" type="datetime-local" required>
  <button type="submit">Confirm Booking</button>
</form>