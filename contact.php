<?php
require 'config.php';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $name = trim($_POST['name']); $email = trim($_POST['email']); $subject = trim($_POST['subject']); $message = trim($_POST['message']);
  $stmt = $mysqli->prepare("INSERT INTO messages (name,email,subject,message) VALUES (?,?,?,?)");
  $stmt->bind_param('ssss',$name,$email,$subject,$message); $stmt->execute();
  $msg = "Message sent. We'll contact you soon.";
}
require 'header.php';
?>
<div class="container">
  <div class="form-card">
    <h2 style="font-size:22px">Contact Us</h2>
    <?php if($msg): ?><div class="message"><?=esc($msg)?></div><?php endif; ?>
    <form method="post" style="margin-top:12px">
      <label>Name</label><input name="name" required>
      <label>Email</label><input name="email" type="email" required>
      <label>Subject</label><input name="subject" required>
      <label>Message</label><textarea name="message" rows="6" required></textarea>
      <button class="search-btn" style="margin-top:12px">Send Message</button>
    </form>
  </div>
</div>
<?php require 'footer.php'; ?>
