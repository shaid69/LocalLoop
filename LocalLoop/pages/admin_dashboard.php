<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | LocalLoop</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="dashboard">
    <?php
    session_start();
    include('../config/config.php'); // fixed path
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') {
      echo "<div class='alert'>Access Denied</div>";
      exit;
    }
    ?>
    <h2>Admin Dashboard</h2>
    <nav>
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="../logout.php">Logout</a>
    </nav>
    <h3>Users</h3>
    <form method="GET" style="max-width:300px;margin-bottom:16px;">
      <input name="user_search" placeholder="Search users..." value="<?= htmlspecialchars($_GET['user_search'] ?? '') ?>">
      <button>Search</button>
    </form>
    <table style="width:100%;margin-bottom:32px;">
      <thead>
        <tr style="background:#232526;color:#00c6ff;">
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Type</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $user_search = trim($_GET['user_search'] ?? '');
      if ($user_search) {
        $sql = "SELECT id, name, email, user_type FROM users WHERE name LIKE ? OR email LIKE ?";
        $param = "%$user_search%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $param, $param);
        $stmt->execute();
        $users = $stmt->get_result();
        $stmt->close();
      } else {
        $users = $conn->query("SELECT id, name, email, user_type FROM users");
      }
      while ($row = $users->fetch_assoc()):
      ?>
        <tr style="background:#414345;">
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['user_type']) ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
    <!-- ...existing code for Services, Bookings, Reviews... -->
  </div>
</body>
</html>
