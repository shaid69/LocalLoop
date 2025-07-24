<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | LocalLoop</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="container">
    <?php
    include('../config/config.php');
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $type = $_POST['user_type'];
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $nid_front = '';
        $nid_back = '';
        $service_details = trim($_POST['service_details'] ?? '');

        // Handle NID uploads for seller
        if ($type === 'seller') {
            if (isset($_FILES['nid_front']) && $_FILES['nid_front']['error'] === UPLOAD_ERR_OK) {
                $nid_front_name = uniqid('nidf_', true) . '.' . pathinfo($_FILES['nid_front']['name'], PATHINFO_EXTENSION);
                $nid_front_dest = '../assets/img/' . $nid_front_name;
                if (move_uploaded_file($_FILES['nid_front']['tmp_name'], $nid_front_dest)) {
                    $nid_front = 'assets/img/' . $nid_front_name;
                }
            }
            if (isset($_FILES['nid_back']) && $_FILES['nid_back']['error'] === UPLOAD_ERR_OK) {
                $nid_back_name = uniqid('nidb_', true) . '.' . pathinfo($_FILES['nid_back']['name'], PATHINFO_EXTENSION);
                $nid_back_dest = '../assets/img/' . $nid_back_name;
                if (move_uploaded_file($_FILES['nid_back']['tmp_name'], $nid_back_dest)) {
                    $nid_back = 'assets/img/' . $nid_back_name;
                }
            }
        }

        if (!$name || !$email || !$password || !in_array($type, ['buyer', 'seller', 'admin'])) {
            $message = "All fields are required.";
        } elseif ($type === 'seller' && (!$phone || !$address || !$nid_front || !$nid_back || !$service_details)) {
            $message = "All seller fields are required.";
        } else {
            $pass = password_hash($password, PASSWORD_BCRYPT);
            if ($type === 'seller') {
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type, phone, address, nid_front, nid_back, service_details, verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
                $stmt->bind_param("sssssssss", $name, $email, $pass, $type, $phone, $address, $nid_front, $nid_back, $service_details);
            } else {
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $pass, $type);
            }
            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $message = "Registration failed. Email may already exist.";
            }
            $stmt->close();
        }
    }
    ?>
    <h2>Register</h2>
    <?php if ($message): ?>
      <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
      <label for="name">Name:</label>
      <input id="name" name="name" placeholder="Name" required>
      <label for="email">Email:</label>
      <input id="email" name="email" type="email" placeholder="Email" required>
      <label for="password">Password:</label>
      <input id="password" name="password" type="password" placeholder="Password" required>
      <label for="user_type">User Type:</label>
      <select id="user_type" name="user_type" required onchange="toggleSellerFields(this.value)">
        <option value="buyer">Buyer</option>
        <option value="seller">Seller</option>
        <option value="admin">Admin</option>
      </select>
      <div id="sellerFields" style="display:none;">
        <label for="phone">Phone Number:</label>
        <input id="phone" name="phone" placeholder="Phone Number">
        <label for="address">Address:</label>
        <input id="address" name="address" placeholder="Address">
        <label for="nid_front">NID Front Image:</label>
        <input id="nid_front" name="nid_front" type="file" accept="image/*">
        <label for="nid_back">NID Back Image:</label>
        <input id="nid_back" name="nid_back" type="file" accept="image/*">
        <label for="service_details">What service do you sell? (Details):</label>
        <textarea id="service_details" name="service_details" placeholder="Describe your service"></textarea>
      </div>
      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a class="button-link" href="login.php">Login</a></p>
  </div>
  <script>
    function toggleSellerFields(val) {
      document.getElementById('sellerFields').style.display = (val === 'seller') ? 'block' : 'none';
      // Set required attribute dynamically
      var req = (val === 'seller');
      document.getElementById('phone').required = req;
      document.getElementById('address').required = req;
      document.getElementById('nid_front').required = req;
      document.getElementById('nid_back').required = req;
      document.getElementById('service_details').required = req;
    }
    // On page load, set seller fields if needed
    document.addEventListener('DOMContentLoaded', function() {
      toggleSellerFields(document.getElementById('user_type').value);
    });
  </script>
</body>
</html>