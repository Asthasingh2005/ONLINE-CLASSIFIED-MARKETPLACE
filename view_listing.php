<?php   
    
require 'config.php';  
    
$id = intval($_GET['id'] ?? 0);  
    
$stmt = $mysqli->prepare("    
    SELECT l.*,     
           c.name as category,     
           u.name as seller,     
           u.email as seller_email,    
           l.phone as seller_phone,    
           l.location as seller_location,    
           l.latitude,    
           l.longitude    
    FROM listings l    
    LEFT JOIN categories c ON l.category_id=c.id    
    LEFT JOIN users u ON l.user_id=u.id    
    WHERE l.id=? LIMIT 1    
");  
    
$stmt->bind_param('i', $id);  
$stmt->execute();  
$l = $stmt->get_result()->fetch_assoc();  
    
if(!$l){ header("Location: index.php"); exit; }  
    
require 'header.php';  
?>

<style>
/* -------------------- GLOBAL BEAUTIFUL EFFECT --------------------- */
body {
    background: #060b16;
}
/* -------------------- CARD --------------------- */
.card {
    background: rgba(255,255,255,0.07);  
    border: 1px solid rgba(0,255,255,0.2);  
    backdrop-filter: blur(10px);  
    border-radius: 20px;  
    box-shadow: 0 0 25px rgba(0,255,255,0.15);
}
/* -------------------- SELLER BOX --------------------- */
.seller-box {
    margin-top:20px;  
    padding:18px;  
    background:rgba(0,20,40,0.9);  
    color:white;  
    border-radius:15px;  
    display:none;  
    border:1px solid #00eaff;  
    box-shadow:0 0 15px rgba(0,255,255,0.4);
}
/* -------------------- MAP --------------------- */
.map-box {
    width: 100%;  
    height: 320px;  
    margin-top: 18px;  
    border-radius: 14px;  
    box-shadow:0 0 20px rgba(0,255,255,0.25);
}
/* -------------------- BUTTON --------------------- */
.contact-btn {
    background:linear-gradient(135deg,#00eaff,#00bcd4);  
    padding:10px 18px;  
    border-radius:10px;  
    color:black;  
    font-weight:700;  
    border:none;  
    box-shadow:0 0 15px rgba(0,255,255,0.5);
}
/* -------------------- REVIEW SECTION --------------------- */
.review-box {
    margin-top:30px;  
    padding:25px;  
    border-radius:18px;
    background: linear-gradient(135deg,#081120,#0d1b2e,#081120);  
    border:1px solid #00eaff;  
    box-shadow:0 0 35px rgba(0,255,255,0.4);
}
.review-title {
    font-size:28px;  
    margin-bottom:20px;  
    font-weight:800;  
    color:#00eaff;  
    text-shadow:0 0 12px #00eaff;
}
.review-card {
    padding:18px;  
    margin-bottom:16px;  
    border-radius:14px;  
    background: rgba(255,255,255,0.10);  
    border-left:5px solid #00eaff;  
    box-shadow:0 0 20px rgba(0,255,255,0.18);
}
.reviewer-name {
    color:#00eaff;  
    font-size:19px;  
    font-weight:800;
}
.star-line {
    font-size:22px;  
    margin:6px 0;  
    color:#ffd700;
}
.review-text {
    color:#d0d7e2;  
    margin-top:6px;  
    line-height:1.6;
}
.rating-select {
    width:200px;  
    padding:12px;  
    border-radius:12px;  
    background:#0d1729;  
    color:#00eaff;  
    border:1px solid #00eaff;  
    margin-bottom:12px;  
    font-weight:600;
}
.review-input {
    width:100%;  
    background:#0d1729;  
    border:1px solid #00eaff;  
    border-radius:12px;  
    padding:12px;  
    height:110px;  
    margin-top:10px;  
    color:white;
}
.review-btn {
    margin-top:12px;  
    padding:12px 22px;  
    border-radius:12px;  
    background:linear-gradient(135deg,#00eaff,#00bcd4);  
    font-weight:900;  
    color:black;  
    border:none;  
    cursor:pointer;  
    font-size:16px;  
    box-shadow:0 0 20px rgba(0,255,255,0.7);
}
</style>

<div class="container">

<div class="card" style="padding:20px">

<img src="<?= $l['image'] ? 'uploads/'.esc($l['image']) : 'assets/no-image.png' ?>"
style="height:380px;border-radius:16px;object-fit:cover;width:100%" alt="">

<h2 style="margin-top:14px;font-size:30px;color:#00eaff"><?= esc($l['title']) ?></h2>

<div style="color:#9db3c9;margin-top:8px">
  Category: <?= esc($l['category']) ?> • Seller: <?= esc($l['seller']) ?>
</div>

<div style="margin-top:12px;font-size:22px;font-weight:900;color:#00eaff">
  ₹ <?= number_format($l['price'],2) ?>
</div>

<p style="margin-top:14px;line-height:1.7;color:#c4d2e3">
  <?= nl2br(esc($l['description'])) ?>
</p>

<!-- ⭐⭐⭐ REVIEW BOX ⭐⭐⭐ -->
<div class="review-box">

<div class="review-title">⭐ Ratings & Reviews</div>

<?php  
$rev = $mysqli->prepare("
    SELECT r.*, u.name 
    FROM reviews r 
    LEFT JOIN users u ON r.user_id=u.id 
    WHERE r.listing_id=? 
    ORDER BY r.id DESC
");
$rev->bind_param("i", $id);
$rev->execute();
$rev_res = $rev->get_result();
?>

<?php if($rev_res->num_rows == 0): ?>
<p style="color:#9aa7b8;font-size:15px;">No reviews yet.</p>
<?php endif; ?>

<?php while($rw = $rev_res->fetch_assoc()): ?>
<div class="review-card">
    <div class="reviewer-name">👤 <?= esc($rw['name']) ?></div>
    <div class="star-line"><?= str_repeat("⭐", intval($rw['rating'])) ?></div>
    <div class="review-text"><?= nl2br(esc($rw['review'])) ?></div>
</div>
<?php endwhile; ?>

<hr style="margin:20px 0;border-color:#1b2a3a">

<h3 style="color:#00eaff;font-size:22px;margin-bottom:10px">Write a Review</h3>

<?php if(!is_logged_in()): ?>
<p style="color:#ff6961;">
    Please <a href="login.php?redirect=view_listing.php?id=<?= $id ?>" style="color:#00eaff;font-weight:700">login</a> to review.
</p>
<?php else: ?>

<form action="submit_review.php" method="POST">
    <input type="hidden" name="listing_id" value="<?= $id ?>">
    <select name="rating" class="rating-select" required>
        <option value="">✨ Select Rating</option>
        <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
        <option value="4">⭐⭐⭐⭐ Good</option>
        <option value="3">⭐⭐⭐ Average</option>
        <option value="2">⭐⭐ Poor</option>
        <option value="1">⭐ Very Bad</option>
    </select>
    <textarea name="review" class="review-input" placeholder="Write your review..."></textarea>
    <button class="review-btn">Submit Review</button>
</form>

<?php endif; ?>
</div>

<!-- MAP -->
<h3 style="margin-top:20px;color:#00eaff;font-size:24px">📍🏡🏠 Location on Map</h3>
<div id="map" class="map-box"></div>

<div style="margin-top:16px;display:flex;gap:10px">

<?php if(is_logged_in()): ?>
<a class="contact-btn" href="toggle_fav.php?id=<?= $l['id'] ?>">♡ Favourite</a>
<?php else: ?>
<a class="contact-btn" href="login.php">Login to favourite</a>
<?php endif; ?>

<button class="contact-btn" onclick="document.getElementById('seller-info').style.display='block'">
🤳🤙 Contact Seller
</button>

<a class="contact-btn"
href="start_chat.php?listing_id=<?= $l['id'] ?>&redirect=view_listing.php?id=<?= $l['id'] ?>">
💬 Message Seller
</a>

</div>

<div id="seller-info" class="seller-box">
<h3>Seller Details👀👀👀</h3>
<p><b>Name:</b> <?= esc($l['seller']) ?></p>
<p><b>Email:</b> <?= esc($l['seller_email']) ?></p>
<p><b>Phone:</b> <?= esc($l['seller_phone']) ?></p>
<p><b>Location:</b> <?= esc($l['seller_location']) ?></p>

<a class="contact-btn" style="margin-top:10px;display:inline-block"
href="mailto:<?= esc($l['seller_email']) ?>">Send Email</a>
</div>

</div>
</div>

<!-- ⭐⭐ FIXED MAP SCRIPT ⭐⭐ -->
<script>
function initMap() {
    let lat = parseFloat("<?= $l['latitude'] ?>");
    let lng = parseFloat("<?= $l['longitude'] ?>");

    // ❗ Seller ne location nahi diya
    if (isNaN(lat) || isNaN(lng) || lat === 0 || lng === 0) {
        document.getElementById("map").innerHTML =
            "<div style='color:#00eaff;padding:20px;font-size:18px;text-align:center'>❗ Seller has not added their location.</div>";
        return;
    }

    const position = { lat: lat, lng: lng };

    const map = new google.maps.Map(document.getElementById("map"), {
        center: position,
        zoom: 15
    });

    new google.maps.Marker({
        position: position,
        map: map,
        title: "Seller Location"
    });
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=add your google api key dear &callback=initMap"></script>


<?php require 'footer.php'; ?>  
