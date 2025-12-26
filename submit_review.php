<?php
require 'config.php';

if (!is_logged_in()) {
    $back = $_POST['listing_id'] ?? ($_GET['listing_id'] ?? null);
    if ($back) {
        header("Location: login.php?redirect=" . urlencode("view_listing.php?id=" . intval($back)));
    } else {
        header("Location: login.php");
    }
    exit;
}

$listing_id = intval($_POST['listing_id'] ?? 0);
$rating     = intval($_POST['rating'] ?? 0);
$review_txt = trim($_POST['review'] ?? '');
$uid        = current_user_id();

if ($listing_id <= 0 || $rating < 1 || $rating > 5) {
    header("Location: view_listing.php?id=" . $listing_id);
    exit;
}

// check if user already reviewed
$chk = $mysqli->prepare("SELECT id FROM reviews WHERE listing_id=? AND user_id=? LIMIT 1");
$chk->bind_param("ii", $listing_id, $uid);
$chk->execute();
$res = $chk->get_result();

if ($res && $res->num_rows) {
    // UPDATE existing
    $row = $res->fetch_assoc();
    $rid = intval($row['id']);

    $u = $mysqli->prepare("UPDATE reviews SET rating=?, review=?, updated_at=NOW() WHERE id=?");
    $u->bind_param("isi", $rating, $review_txt, $rid);
    $u->execute();
} else {
    // INSERT new
    $ins = $mysqli->prepare("INSERT INTO reviews (listing_id, user_id, rating, review) VALUES (?, ?, ?, ?)");
    $ins->bind_param("iiis", $listing_id, $uid, $rating, $review_txt);
    $ins->execute();
}

header("Location: view_listing.php?id=" . $listing_id . "&r=ok");
exit;
