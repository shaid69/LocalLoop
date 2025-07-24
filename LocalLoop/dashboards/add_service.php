<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Service</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="dashboard">
<?php
session_start();
if ($_SESSION['user']['user_type'] !== 'seller') exit("<div class='alert'>Access Denied</div>");
include('../config/config.php');
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_SESSION['user']['id'];
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $loc = trim($_POST['location']);
    $cat = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $avail = trim($_POST['availability']);
    $img_path = '';

    // Handle image upload
    if (isset($_FILES['service_img']) && $_FILES['service_img']['error'] === UPLOAD_ERR_OK) {
        $img_name = uniqid('service_', true) . '.' . pathinfo($_FILES['service_img']['name'], PATHINFO_EXTENSION);
        $img_dest = '../assets/img/' . $img_name;
        if (move_uploaded_file($_FILES['service_img']['tmp_name'], $img_dest)) {
            $img_path = 'assets/img/' . $img_name;
        }
    }

    if (!$title || !$loc || !$cat || !$avail || !$price) {
        $message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO services (user_id, title, description, location, category, price, availability, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssdss", $uid, $title, $desc, $loc, $cat, $price, $avail, $img_path);
        if ($stmt->execute()) {
            $message = "Service added!";
        } else {
            $message = "Error adding service.";
        }
        $stmt->close();
    }
}
?>
<h2>Add New Service</h2>
<?php if ($message): ?>
  <div class="alert"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<form method="POST" enctype="multipart/form-data">
  <label for="title">Service Title:</label>
  <input id="title" name="title" placeholder="Service Title" required>

  <label for="description">Description:</label>
  <textarea id="description" name="description" placeholder="Describe your service in detail, including what is included, any specialties, and why customers should choose you." required></textarea>

  <label for="category">Category:</label>
  <input id="category" name="category" placeholder="e.g. Plumbing, Tutoring, Cleaning" required>

  <label for="location">Location:</label>
  <input id="location" name="location" placeholder="City or Area you serve" required>

  <label for="availability">Availability:</label>
  <input id="availability" name="availability" placeholder="e.g. Mon-Fri 9am-6pm, Weekends, 24/7" required>

  <label for="price">Price (in ₹):</label>
  <input id="price" name="price" placeholder="e.g. 500" required type="number" step="0.01" min="0">

  <label for="service_img">Service Image:</label>
  <input id="service_img" name="service_img" type="file" accept="image/*">

  <div style="margin-top:10px;">
    <strong>Tips for a relevant service listing:</strong>
    <ul style="margin:8px 0 0 18px;font-size:0.98em;color:#b3e5fc;">
      <li>Be specific about what you offer and your expertise.</li>
      <li>Mention any certifications, experience, or unique selling points.</li>
      <li>State your service area clearly.</li>
      <li>Include your working hours or days.</li>
      <li>Upload a clear, relevant image (no stock photos if possible).</li>
    </ul>
  </div>
  <button type="submit">Add</button>
</form>
<!-- Loader for automation -->
<div class="loader" style="display:none;"></div>
</div>
</body>
</html>