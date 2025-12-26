<?php
// header.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Classify - Modern Classifieds</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <div class="navbar">
    <div class="logo"><a href="index.php" style="color:white;text-decoration:none">Classify</a></div>
    <div class="nav-links">
      <a href="index.php">Home</a>
      <a href="search.php">Search</a>
      <a href="add_listing.php">Add Listing</a>
      <a href="contact.php">Contact</a>
      <?php if(!empty($_SESSION['user_id'])): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php" style="background:rgba(255,255,255,0.2);">Logout</a>
      <?php else: ?>
        <a href="register.php">Register</a>
        <a href="login.php" style="background:white;color:#ff6b6b">Login</a>
      <?php endif; ?>
    </div>
  </div>
</div>
