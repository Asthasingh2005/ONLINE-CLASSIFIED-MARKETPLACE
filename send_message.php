<?php
require 'config.php';
//require 'auth_check.php';

if (!is_logged_in()) exit;

$user_id = $_SESSION['user']['id'];
$message = trim($_POST['message'] ?? '');
$listing_id = intval($_POST['listing_id'] ?? 0);
$seller_id = intval($_POST['seller_id'] ?? 0);

if ($message == "") exit;

// Decide receiver
$receiver_id = ($user_id == $seller_id) ? $_POST['buyer_id'] : $seller_id;

$stmt = $mysqli->prepare("INSERT INTO user_messages (listing_id, sender_id, receiver_id, message) VALUES (?,?,?,?)");
$stmt->bind_param("iiis", $listing_id, $user_id, $receiver_id, $message);
$stmt->execute();
?>
