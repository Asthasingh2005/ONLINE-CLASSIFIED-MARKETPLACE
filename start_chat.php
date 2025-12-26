<?php
require 'config.php';

// User login check
if (!is_logged_in()) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$listing_id = intval($_GET['listing_id'] ?? 0);

// Fetch listing to get seller
$stmt = $mysqli->prepare("SELECT user_id FROM listings WHERE id=? LIMIT 1");
$stmt->bind_param("i", $listing_id);
$stmt->execute();
$listing = $stmt->get_result()->fetch_assoc();

if (!$listing) {
    die("Listing not found!");
}

$seller_id = $listing['user_id'];
$buyer_id = $_SESSION['user_id'];  // FIXED HERE

// If same user -> cannot message yourself
if ($seller_id == $buyer_id) {
    die("You cannot message yourself.");
}

// Redirect to chat room
header("Location: chat_room.php?listing_id=$listing_id&seller_id=$seller_id");
exit;
?>
