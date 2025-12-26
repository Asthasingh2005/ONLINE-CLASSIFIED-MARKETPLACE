<?php
// fetch existing reviews
$rid = intval($l['id']);
$rq = $mysqli->prepare("SELECT r.*, u.name FROM reviews r 
                        LEFT JOIN users u ON r.user_id=u.id
                        WHERE r.listing_id=? ORDER BY r.created_at DESC");
$rq->bind_param("i", $rid);
$rq->execute();
$reviews = $rq->get_result();
?>

<style>
/* ⭐ Main Review Container */
.review-container {
    margin-top:25px;
    padding:28px;
    background: linear-gradient(135deg, #0a0f1e, #112042, #0a0f1e);
    color:white;
    border-radius:18px;
    border:1px solid #00eaff;
    box-shadow: 0 0 25px rgba(0,255,255,0.3);
    animation: fadeIn 0.7s ease;
}

@keyframes fadeIn {
    from { opacity:0; transform: translateY(10px); }
    to   { opacity:1; transform: translateY(0); }
}

/* ⭐ Heading Glow */
.review-title {
    color:#00eaff;
    margin-bottom:15px;
    font-size:27px;
    font-weight:800;
    text-shadow:0 0 12px #00eaff;
    letter-spacing:1px;
}

/* ⭐ Rating Select Box */
.rating-select {
    padding:12px;
    border-radius:10px;
    width:200px;
    background:#091223;
    color:#00eaff;
    border:1px solid #00eaff;
    font-size:17px;
    font-weight:600;
    transition:0.3s;
}

.rating-select:hover {
    background:#0f1b33;
    box-shadow:0 0 10px rgba(0,255,255,0.4);
}

/* ⭐ Textarea */
.review-text {
    width:100%;
    margin-top:14px;
    padding:14px;
    border-radius:12px;
    height:110px;
    background:#0b1324;
    color:white;
    border:1px solid #00eaff;
    font-size:16px;
    transition:0.3s;
}

.review-text:focus {
    box-shadow:0 0 15px rgba(0,255,255,0.5);
}

/* ⭐ Submit Button */
.review-btn {
    background:linear-gradient(135deg,#00eaff,#0097c7);
    color:black;
    padding:12px 24px;
    border-radius:12px;
    font-weight:900;
    margin-top:15px;
    border:none;
    cursor:pointer;
    transition:0.3s;
    font-size:17px;
    box-shadow:0 0 12px rgba(0,255,255,0.6);
}

.review-btn:hover {
    transform:scale(1.07);
    box-shadow:0 0 20px rgba(0,255,255,0.9);
}

/* ⭐ Each Review Card */
.review-card {
    padding:18px;
    margin-bottom:15px;
    border-radius:12px;
    background:rgba(255,255,255,0.06);
    border-left:5px solid #00eaff;
    backdrop-filter: blur(3px);
    animation: fadeCard 0.5s ease;
}

@keyframes fadeCard {
    from { opacity:0; transform: translateX(-10px); }
    to   { opacity:1; transform: translateX(0); }
}

/* ⭐ Reviewer Name */
.reviewer-name {
    font-weight:700;
    color:#00eaff;
    font-size:18px;
}

/* ⭐ Stars */
.star-show {
    color:#ffd700;
    font-size:22px;
    margin-top:5px;
}

/* ⭐ Review Text */
.review-content {
    margin-top:10px;
    color:#e2e8f0;
    line-height:1.6;
    font-size:16px;
}
</style>

<div class="review-container">

    <h3 class="review-title">⭐ Ratings & Reviews</h3>

    <?php if(is_logged_in()): ?>
    <form action="submit_review.php" method="post" style="margin-bottom:20px;">
        <input type="hidden" name="listing_id" value="<?= $rid ?>">

        <label style="font-weight:700;color:#00eaff;">Your Rating</label><br>

        <select name="rating" required class="rating-select">
            <option value="">⭐ Select rating</option>
            <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
            <option value="4">⭐⭐⭐⭐ Good</option>
            <option value="3">⭐⭐⭐ Average</option>
            <option value="2">⭐⭐ Poor</option>
            <option value="1">⭐ Very Bad</option>
        </select>

        <textarea name="review" class="review-text" placeholder="Write your honest review..."></textarea>

        <button type="submit" class="review-btn">Submit Review</button>
    </form>
    <?php else: ?>
        <p style="color:#ccc;font-size:15px">Login to give rating and review.</p>
    <?php endif; ?>

    <hr style="border-color:#1e293b;margin:20px 0">

    <h4 style="margin-bottom:15px;color:#00eaff;font-size:22px;">All Reviews</h4>

    <?php if($reviews->num_rows == 0): ?>
        <p style="color:#ccc">No reviews yet.</p>
    <?php endif; ?>

    <?php while($rv = $reviews->fetch_assoc()): ?>
        <div class="review-card">
            <div class="reviewer-name"><?= esc($rv['name']) ?></div>

            <div class="star-show">
                <?= str_repeat("⭐", intval($rv['rating'])) ?>
            </div>

            <div class="review-content"><?= nl2br(esc($rv['review'])) ?></div>
        </div>
    <?php endwhile; ?>
</div>
