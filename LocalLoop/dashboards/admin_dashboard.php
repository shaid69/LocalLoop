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
    include('../config/config.php');
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
    <!-- Add User Form -->
    <form method="POST" style="max-width:400px;margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;">
      <input name="add_name" placeholder="Name" required>
      <input name="add_email" placeholder="Email" type="email" required>
      <select name="add_type" required>
        <option value="">Type</option>
        <option value="buyer">Buyer</option>
        <option value="seller">Seller</option>
        <option value="admin">Admin</option>
      </select>
      <input name="add_password" placeholder="Password" type="password" required>
      <button type="submit" name="add_user">Add User</button>
    </form>
    <?php
    // Handle Add User
    if (isset($_POST['add_user'])) {
      $name = trim($_POST['add_name']);
      $email = trim($_POST['add_email']);
      $type = $_POST['add_type'];
      $password = password_hash($_POST['add_password'], PASSWORD_DEFAULT);
      if ($name && $email && $type && $_POST['add_password']) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, user_type, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $type, $password);
        if ($stmt->execute()) {
          echo "<div class='alert'>User added successfully.</div>";
        } else {
          echo "<div class='alert'>Failed to add user.</div>";
        }
        $stmt->close();
      }
    }
    // Handle Delete User
    if (isset($_POST['delete_user_id'])) {
      $delete_id = intval($_POST['delete_user_id']);
      // Prevent admin from deleting themselves
      if ($delete_id !== $_SESSION['user']['id']) {
        $conn->query("DELETE FROM users WHERE id = $delete_id");
        echo "<div class='alert'>User deleted.</div>";
      } else {
        echo "<div class='alert'>You cannot delete yourself.</div>";
      }
    }
    ?>
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
          <th>Action</th>
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
          <td>
            <?php if ($row['id'] != $_SESSION['user']['id']): ?>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="delete_user_id" value="<?= intval($row['id']) ?>">
                <button type="submit" onclick="return confirm('Delete this user?')">Delete</button>
              </form>
            <?php else: ?>
              <span style="color:#888;">Current Admin</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>

    <h3>Services</h3>
    <form method="GET" style="max-width:300px;margin-bottom:16px;">
      <input name="service_search" placeholder="Search services..." value="<?= htmlspecialchars($_GET['service_search'] ?? '') ?>">
      <button>Search</button>
    </form>
    <table style="width:100%;margin-bottom:32px;">
      <thead>
        <tr style="background:#232526;color:#00c6ff;">
          <th>ID</th>
          <th>Title</th>
          <th>Category</th>
          <th>Location</th>
          <th>Price</th>
          <th>Verified</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $service_search = trim($_GET['service_search'] ?? '');
      if ($service_search) {
        $sql = "SELECT id, title, category, location, price, verified FROM services WHERE title LIKE ? OR location LIKE ?";
        $param = "%$service_search%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $param, $param);
        $stmt->execute();
        $services = $stmt->get_result();
        $stmt->close();
      } else {
        $services = $conn->query("SELECT id, title, category, location, price, verified FROM services");
      }
      while ($row = $services->fetch_assoc()):
      ?>
        <tr style="background:#414345;">
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= htmlspecialchars($row['category']) ?></td>
          <td><?= htmlspecialchars($row['location']) ?></td>
          <td>₹<?= htmlspecialchars($row['price']) ?></td>
          <td><?= $row['verified'] ? 'Yes' : 'No' ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>

    <h3>Bookings</h3>
    <table style="width:100%;margin-bottom:32px;">
      <thead>
        <tr style="background:#232526;color:#00c6ff;">
          <th>ID</th>
          <th>Service</th>
          <th>Customer</th>
          <th>Status</th>
          <th>Time</th>
          <th>Comment</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $sql = "SELECT b.id, s.title as service, u.name as customer, b.status, b.booking_time, b.comment
              FROM bookings b 
              JOIN services s ON b.service_id = s.id 
              JOIN users u ON b.customer_id = u.id";
      $bookings = $conn->query($sql);
      while ($row = $bookings->fetch_assoc()):
      ?>
        <tr style="background:#414345;">
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['service']) ?></td>
          <td><?= htmlspecialchars($row['customer']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td><?= htmlspecialchars($row['booking_time']) ?></td>
          <td><?= htmlspecialchars($row['comment']) ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>

    <h3>Reviews</h3>
    <table style="width:100%;margin-bottom:32px;">
      <thead>
        <tr style="background:#232526;color:#00c6ff;">
          <th>ID</th>
          <th>Service</th>
          <th>Customer</th>
          <th>Rating</th>
          <th>Comment</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $sql = "SELECT r.id, s.title as service, u.name as customer, r.rating, r.comment 
              FROM reviews r 
              JOIN services s ON r.service_id = s.id 
              JOIN users u ON r.customer_id = u.id";
      $reviews = $conn->query($sql);
      while ($row = $reviews->fetch_assoc()):
      ?>
        <tr style="background:#414345;">
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['service']) ?></td>
          <td><?= htmlspecialchars($row['customer']) ?></td>
          <td><?= htmlspecialchars($row['rating']) ?></td>
          <td><?= htmlspecialchars($row['comment']) ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>

    <h3>Seller Reviews</h3>
    <table style="width:100%;margin-bottom:32px;">
      <thead>
        <tr style="background:#232526;color:#00c6ff;">
          <th>Seller</th>
          <th>Average Rating</th>
          <th>Total Reviews</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $sql = "SELECT u.name as seller, AVG(r.rating) as avg_rating, COUNT(r.id) as total_reviews
              FROM reviews r
              JOIN services s ON r.service_id = s.id
              JOIN users u ON s.user_id = u.id
              GROUP BY s.user_id";
      $seller_reviews = $conn->query($sql);
      while ($row = $seller_reviews->fetch_assoc()):
      ?>
        <tr style="background:#414345;">
          <td><?= htmlspecialchars($row['seller']) ?></td>
          <td><?= number_format($row['avg_rating'], 2) ?></td>
          <td><?= htmlspecialchars($row['total_reviews']) ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>

    <h3>Pending Seller Verifications</h3>
    <table style="width:100%;margin-bottom:32px;">
      <thead>
        <tr style="background:#232526;color:#00c6ff;">
          <th>Name</th>
          <th>Phone</th>
          <th>Address</th>
          <th>NID Front</th>
          <th>NID Back</th>
          <th>Service Details</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php
      // Fetch sellers who are not verified
      $sql = "SELECT id, name, phone, address, nid_front, nid_back, service_details FROM users WHERE user_type='seller' AND (verified IS NULL OR verified=0)";
      $pending = $conn->query($sql);
      while ($row = $pending->fetch_assoc()):
      ?>
        <tr style="background:#414345;">
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= htmlspecialchars($row['address']) ?></td>
          <td>
            <?php if (!empty($row['nid_front'])): ?>
              <a href="../<?= htmlspecialchars($row['nid_front']) ?>" target="_blank">View</a>
            <?php else: ?>
              N/A
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($row['nid_back'])): ?>
              <a href="../<?= htmlspecialchars($row['nid_back']) ?>" target="_blank">View</a>
            <?php else: ?>
              N/A
            <?php endif; ?>
          </td>
          <td><?= nl2br(htmlspecialchars($row['service_details'])) ?></td>
          <td>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="verify_seller_id" value="<?= intval($row['id']) ?>">
              <button type="submit" name="verify_seller" onclick="return confirm('Verify this seller?')">Approve</button>
            </form>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="reject_seller_id" value="<?= intval($row['id']) ?>">
              <button type="submit" name="reject_seller" onclick="return confirm('Reject this seller?')">Reject</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
    <?php
    // Handle seller verification
    if (isset($_POST['verify_seller_id'])) {
      $sid = intval($_POST['verify_seller_id']);
      $conn->query("UPDATE users SET verified=1 WHERE id=$sid");
      echo "<div class='alert'>Seller verified.</div>";
    }
    if (isset($_POST['reject_seller_id'])) {
      $sid = intval($_POST['reject_seller_id']);
      $conn->query("UPDATE users SET verified=0 WHERE id=$sid");
      echo "<div class='alert'>Seller rejected.</div>";
    }
    ?>
  </div>
</body>
</html>