<?php
require 'config.php';
if(!is_logged_in()){ header("Location: login.php"); exit; }
$uid = current_user_id();
$lid = intval($_GET['id'] ?? 0);

$stmt = $mysqli->prepare("SELECT id FROM favourites WHERE user_id=? AND listing_id=?");
$stmt->bind_param('ii',$uid,$lid); $stmt->execute();
if($stmt->get_result()->num_rows){
  $del = $mysqli->prepare("DELETE FROM favourites WHERE user_id=? AND listing_id=?");
  $del->bind_param('ii',$uid,$lid); $del->execute();
} else {
  $ins = $mysqli->prepare("INSERT IGNORE INTO favourites (user_id,listing_id) VALUES (?,?)");
  $ins->bind_param('ii',$uid,$lid); $ins->execute();
}
header("Location: view_listing.php?id=".$lid);
exit;
