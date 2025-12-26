<?php
require 'config.php';
if(!is_logged_in()){ header("Location: login.php"); exit; }
$id = intval($_GET['id'] ?? 0);

$stmt = $mysqli->prepare("SELECT * FROM listings WHERE id=? AND user_id=? LIMIT 1");
$stmt->bind_param('ii',$id, $_SESSION['user_id']);
$stmt->execute();
$listing = $stmt->get_result()->fetch_assoc();
if(!$listing){ echo "Listing not found."; exit; }

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = intval($_POST['category'] ?? 0);

    // Keep old image unless new one uploaded
    $filename = $listing['image'];

    // Handle image upload
    if(!empty($_FILES['image']['name'])){
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if(in_array($ext, $allowed)){
            $filename = time().'_'.bin2hex(random_bytes(5)).'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/uploads/'.$filename);
        } else {
            $errors[] = "Invalid image type.";
        }
    }

    if(empty($errors)){
        // Corrected Update Query
        $upd = $mysqli->prepare("UPDATE listings 
                                 SET title=?, description=?, price=?, category_id=?, image=? 
                                 WHERE id=? AND user_id=?");

        // Correct bind_param types → s s d i s i i
        $upd->bind_param('ssdissi',
            $title,
            $description,
            $price,
            $category,
            $filename,
            $id,
            $_SESSION['user_id']
        );

        if($upd->execute()){
            header("Location: dashboard.php?msg=Updated");
            exit;
        } else {
            $errors[] = "DB error on update.";
        }
    }
}

$cats = $mysqli->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);
require 'header.php';
?>

<div class="container">
  <div class="form-card">
    <h2 style="font-size:26px">Edit Listing</h2>

    <?php foreach($errors as $e): ?>
      <div class="message" style="background:#fff4f4;color:#9b1c1c">
        <?= esc($e) ?>
      </div>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data">
      <label>Title</label>
      <input name="title" value="<?= esc($listing['title']) ?>" required>

      <label>Description</label>
      <textarea name="description" rows="5"><?= esc($listing['description']) ?></textarea>

      <label>Category</label>
      <select name="category">
        <option value="0">Select category</option>
        <?php foreach($cats as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $c['id']==$listing['category_id'] ? 'selected':'' ?>>
            <?= esc($c['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Price (INR)</label>
      <input name="price" type="number" step="0.01" value="<?= esc($listing['price']) ?>" required>

      <label>Image (choose to replace)</label>
      <input type="file" name="image" accept="image/*">

      <div style="margin-top:10px">
        <button class="search-btn">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<?php require 'footer.php'; ?>
