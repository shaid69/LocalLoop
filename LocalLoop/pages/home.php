<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LocalLoop Home</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php
include('../config/config.php');
$search = trim($_GET['search'] ?? '');
if ($search) {
    $sql = "SELECT * FROM services WHERE title LIKE ? OR location LIKE ?";
    $param = "%$search%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $param, $param);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conn->query("SELECT * FROM services");
}
?>
<form method="GET">
  <input name="search" placeholder="Search services..." value="<?= htmlspecialchars($search) ?>">
  <button>Search</button>
</form>
<div class="service-list">
<?php while ($row = $res->fetch_assoc()): ?>
  <div class="service-card">
    <?php if (!empty($row['image'])): ?>
      <img src="../<?= htmlspecialchars($row['image']) ?>" alt="Service Image" class="service-img">
    <?php endif; ?>
    <div class="service-info">
      <h4><?= htmlspecialchars($row['title']) ?></h4>
      <p><?= htmlspecialchars($row['description']) ?></p>
      <span><?= htmlspecialchars($row['location']) ?></span> |
      <span>₹<?= htmlspecialchars($row['price']) ?></span>
      <br>
      <a class="button-link" href="book_service.php?id=<?= urlencode($row['id']) ?>">Book</a>
    </div>
  </div>
<?php endwhile; ?>
</div>
</body>
</html>