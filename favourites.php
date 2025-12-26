<?php
require 'config.php';
if(!is_logged_in()){ header("Location: login.php"); exit; }
$uid = current_user_id();
$stmt = $mysqli->prepare("SELECT l.* FROM listings l JOIN favourites f ON f.listing_id=l.id WHERE f.user_id=? ORDER BY f.created_at DESC");
$stmt->bind_param('i',$uid); $stmt->execute();
$listings = $stmt->get_result();
require 'header.php';
?>
<div class="container">
  <h2 class="main-title">Your Favourites</h2>
  <div class="listing-grid">
    <?php while($l = $listings->fetch_assoc()): ?>
      <div class="card">
        <img src="<?= $l['image'] ? 'uploads/'.esc($l['image']) : 'assets/no-image.png' ?>">
        <div class="card-title"><?= esc($l['title']) ?></div>
        <div class="card-footer">
          <div class="price">₹ <?= number_format($l['price'],2) ?></div>
          <a class="btn-outline" href="view_listing.php?id=<?= $l['id'] ?>">View</a>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>
<?php require 'footer.php'; ?>
