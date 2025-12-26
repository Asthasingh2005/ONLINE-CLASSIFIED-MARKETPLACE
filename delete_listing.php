<?php
require 'config.php';
if(!is_logged_in()){ header("Location: login.php"); exit; }
$id = intval($_GET['id'] ?? 0);
$del = $mysqli->prepare("DELETE FROM listings WHERE id=? AND user_id=?");
$del->bind_param('ii',$id,$_SESSION['user_id']);
if($del->execute()){
  header("Location: dashboard.php?msg=Deleted");
  exit;
} else {
  echo "Error deleting.";
}
