<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LocalLoop Home</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="dashboard">
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
        $stmt->close();
    } else {
        $res = $conn->query("SELECT * FROM services");
    }
    ?>
    <form method="GET" class="search-form">
      <input id="searchInput" name="search" placeholder="Search services..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit">Search</button>
      <button type="button" id="locateBtn" style="margin-left:8px;">Use My Location</button>
    </form>
    <script>
    // Live location tracking for search
    document.getElementById('locateBtn').onclick = function() {
      var btn = this;
      btn.disabled = true;
      btn.textContent = "Locating...";
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var lat = position.coords.latitude;
          var lon = position.coords.longitude;
          // Use OpenStreetMap Nominatim API for reverse geocoding
          fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
            .then(response => response.json())
            .then(data => {
              let city = '';
              if (data.address && (data.address.city || data.address.town || data.address.village)) {
                city = data.address.city || data.address.town || data.address.village;
              } else if (data.display_name) {
                city = data.display_name.split(',')[0];
              }
              if (city) {
                document.getElementById('searchInput').value = city;
              } else {
                alert('Could not determine your city.');
              }
              btn.disabled = false;
              btn.textContent = "Use My Location";
            })
            .catch(() => {
              alert('Could not fetch location info.');
              btn.disabled = false;
              btn.textContent = "Use My Location";
            });
        }, function() {
          alert('Location access denied.');
          btn.disabled = false;
          btn.textContent = "Use My Location";
        });
      } else {
        alert('Geolocation not supported.');
        btn.disabled = false;
        btn.textContent = "Use My Location";
      }
    };
    </script>
    <div class="service-list">
    <?php if ($res && $res->num_rows > 0): ?>
      <?php while ($row = $res->fetch_assoc()): ?>
        <div class="service-card">
          <?php if (!empty($row['image'])): ?>
            <img src="../<?= htmlspecialchars($row['image']) ?>" alt="Service Image" class="service-img">
          <?php endif; ?>
          <div class="service-info">
            <h4><?= htmlspecialchars($row['title']) ?></h4>
            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            <span><?= htmlspecialchars($row['location']) ?></span> |
            <span>₹<?= htmlspecialchars($row['price']) ?></span>
            <br>
            <a class="button-link" href="book_service.php?id=<?= urlencode($row['id']) ?>">Book</a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No services found.</p>
    <?php endif; ?>
    </div>
  </div>
</body>
</html>
