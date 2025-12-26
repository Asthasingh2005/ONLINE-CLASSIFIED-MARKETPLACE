<?php
require 'config.php';
require 'header.php';
?>
<div class="container">
  <div class="hero">
    <div class="left">
      <h1>Find, Buy & Sell — Beautifully and Reliable </h1> 
      <h4>make it beautifull and comfortable with yr first use</h4>





      <h2>MADE BY ASTHA SINGH</h2>


<h4>online classified marketplace app--2025</h4>


      <p>Post free listings, browse categories and find the best local deals near you.</p>

      <div class="search-row">
        <form method="get" action="search.php" style="display:flex;width:100%;">
          <input class="input" name="q" placeholder="Search title or description..." />
          <button class="search-btn" type="submit">Search</button>
        </form>
      </div>
    </div>
    <div class="right">
      <!-- optional: hero image or CTA -->
    </div>
  </div>

  <h1 class="main-title">Latest Listings</h1>
  <h2>please enjoy the service dear--🤗☝️🫶🙏☺️☺️💕</h2>

  <div class="listing-grid">
    <?php
    $stmt = $mysqli->prepare("SELECT l.*, c.name as category FROM listings l LEFT JOIN categories c ON l.category_id=c.id ORDER BY l.created_at DESC LIMIT 12");
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()):
      $img = $row['image'] ? 'uploads/'.esc($row['image']) : 'assets/no-image.png';
    ?>
      <div class="card">
        <img src="<?= $img ?>" alt="">
        <div class="card-title"><?= esc($row['title']) ?></div>
        <div class="card-desc"><?= esc(substr($row['description'],0,120)) ?>...</div>
        <div class="card-footer">
          <div class="price">₹ <?= number_format($row['price'],2) ?></div>
          <div>
            <a class="btn-outline" href="view_listing.php?id=<?= $row['id'] ?>">View</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <?php require 'footer.php'; ?>
</div>
