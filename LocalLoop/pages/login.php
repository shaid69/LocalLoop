<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | LocalLoop</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="container">
    <?php
    include('../config/config.php');
    session_start();
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = trim($_POST['email']);
        $pass = $_POST['password'];
        if (!$email || !$pass) {
            $message = "Email and password required.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();
            $user = $res->fetch_assoc();
            if ($user && password_verify($pass, $user['password'])) {
                $_SESSION['user'] = $user;
                switch ($user['user_type']) {
                    case 'admin': header("Location: ../dashboards/admin_dashboard.php"); break;
                    case 'buyer': header("Location: ../dashboards/buyer_dashboard.php"); break;
                    case 'seller': header("Location: ../dashboards/seller_dashboard.php"); break;
                }
                exit;
            } else {
                $message = "Invalid login.";
            }
            $stmt->close();
        }
    }
    ?>
    <h2>Login</h2>
    <?php if ($message): ?>
      <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST">
      <label for="email">Email:</label>
      <input id="email" name="email" type="email" placeholder="Email" required>
      <label for="password">Password:</label>
      <input id="password" name="password" type="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a class="button-link" href="register.php">Register</a></p>
  </div>
</body>
</html>