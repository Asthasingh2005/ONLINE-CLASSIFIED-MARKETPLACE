<?php
require 'config.php';
$errors=[];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $pass = $_POST['password'];
  if(!$name || !$email || !$pass) $errors[] = "All fields required.";
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
  if(empty($errors)){
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
    $stmt->bind_param('sss',$name,$email,$hash);
    if($stmt->execute()){
      $_SESSION['user_id'] = $stmt->insert_id;
      $_SESSION['user_name'] = $name;
      header("Location: dashboard.php"); exit;
    } else {
      $errors[] = "Email already in use.";
    }
  }
}
require 'header.php';
?>
<div class="container">
  <div class="form-card">
    <h2 style="font-size:28px">Create your account</h2>
    <?php foreach($errors as $e): ?><div class="message" style="background:#fff4f4;color:#9b1c1c"><?=esc($e)?></div><?php endforeach; ?>
    <form method="post" style="margin-top:14px">
      <label>Name</label><input name="name" required>
      <label>Email</label><input name="email" type="email" required>
      <label>Password</label><input name="password" type="password" required>
      <button class="search-btn" style="margin-top:12px">Register</button>
    </form>
  </div>
</div>
<?php require 'footer.php'; ?>
