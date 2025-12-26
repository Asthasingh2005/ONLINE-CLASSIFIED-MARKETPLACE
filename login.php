<?php
require 'config.php';

$errors = [];

// GET redirect param agar listing se aaya ho
$redirect_to = $_GET['redirect'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    // POST me bhi redirect catch karo
    $redirect_to = $_POST['redirect'] ?? null;

    $stmt = $mysqli->prepare("SELECT id,name,password FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if ($res && password_verify($pass, $res['password'])) {

      // login success
      $_SESSION['user_id'] = $res['id'];
      $_SESSION['user_name'] = $res['name'];

      // Chat system ke liye proper user array
      $_SESSION['user'] = [
          'id' => $res['id'],
          'name' => $res['name']
      ];

      // If redirect param exists → go back to that page
      if ($redirect_to) {

          // prevent external URLs
          if (strpos($redirect_to, 'http://') === false &&
              strpos($redirect_to, 'https://') === false) {

              header("Location: " . $redirect_to);
              exit;
          }
      

      // fallback default
      header("Location: dashboard.php");
      exit;
}

        
    } 
    else {
        $errors[] = "Invalid email or password.";
    }
}

require 'header.php';
?>

<div class="container">
  <div class="form-card">
    <h2 style="font-size:28px">Welcome back</h2>

    <?php foreach($errors as $e): ?>
      <div class="message" style="background:#fff4f4;color:#9b1c1c">
        <?= esc($e) ?>
      </div>
    <?php endforeach; ?>

    <form method="post" style="margin-top:14px">

      <!-- hidden redirect value -->
      <?php if($redirect_to): ?>
        <input type="hidden" name="redirect" value="<?= esc($redirect_to) ?>">
      <?php endif; ?>

      <label>Email</label>
      <input name="email" type="email" required>

      <label>Password</label>
      <input name="password" type="password" required>

      <button class="search-btn" style="margin-top:12px">Login</button>
    </form>
  </div>
</div>

<?php require 'footer.php'; ?>
