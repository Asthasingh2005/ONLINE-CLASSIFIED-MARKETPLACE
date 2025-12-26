<?php
require 'config.php';
$q = trim($_GET['q'] ?? '');
$cat = intval($_GET['category'] ?? 0);

$sql = "SELECT l.*, c.name as category FROM listings l LEFT JOIN categories c ON l.category_id=c.id WHERE 1=1 ";
$params=[]; $types='';
if($q!==''){
  $sql .= " AND (l.title LIKE CONCAT('%',?,'%') OR l.description LIKE CONCAT('%',?,'%')) ";
  $types .= 'ss'; $params[] = $q; $params[] = $q;
}
if($cat){
  $sql .= " AND l.category_id = ? ";
  $types .= 'i'; $params[] = $cat;
}
$sql .= " ORDER BY l.created_at DESC";

$stmt = $mysqli->prepare($sql);
if($types){
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$listings = $stmt->get_result();
require 'header.php';
$cats = $mysqli->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);
?>
<div class="container">
  <div class="form-card">
    <h2 style="font-size:22px">Search Listings</h2>
    <form method="get" style="display:flex;gap:8px;margin-top:12px">
      <input name="q" class="input" value="<?= esc($q) ?>" placeholder="Keywords...">
      <select name="category" style="padding:12px;border-radius:10px">
        <option value="0">All Categories</option>
        <?php foreach($cats as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $c['id']==$cat ? 'selected':'' ?>><?= esc($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="search-btn">Search</button>
    </form>
  </div>

  <div class="listing-grid" style="margin-top:18px">
    <?php while($l = $listings->fetch_assoc()): ?>
      <div class="card">
        <img src="<?= $l['image'] ? 'uploads/'.esc($l['image']) : 'assets/no-image.png' ?>">
        <div class="card-title"><?= esc($l['title']) ?></div>
        <div class="card-desc"><?= esc(substr($l['description'],0,100)) ?>...</div>
        <div class="card-footer">
          <div class="price">₹ <?= number_format($l['price'],2) ?></div>
          <a class="btn-outline" href="view_listing.php?id=<?= $l['id'] ?>">View</a>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

</div>
<?php require 'footer.php'; ?>
