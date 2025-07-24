<?php
include('../config/config.php');
session_start();
if (!isset($_SESSION['user'])) {
    echo "You must be logged in to submit a review.";
    exit;
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_id = intval($_POST['service_id']);
    $customer_id = intval($_SESSION['user']['id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    if ($rating < 1 || $rating > 5 || empty($comment)) {
        $message = "Invalid rating or comment.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (service_id, customer_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $service_id, $customer_id, $rating, $comment);
        if ($stmt->execute()) {
            $message = "Review submitted!";
        } else {
            $message = "Error submitting review.";
        }
        $stmt->close();
    }
}
?>
<?php if ($message): ?>
  <div class="alert"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<form method="POST">
  <input type="hidden" name="service_id" value="<?= htmlspecialchars($_GET['service_id'] ?? '') ?>">
  <label for="rating">Rating (1-5):</label>
  <input id="rating" type="number" name="rating" min="1" max="5" required>
  <label for="comment">Comment:</label>
  <textarea id="comment" name="comment" placeholder="Your review..." required></textarea>
  <button type="submit">Submit Review</button>
</form>