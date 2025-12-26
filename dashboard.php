<?php 
include 'header.php';
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

/* ===== SORTING LOGIC ===== */
$sort = $_GET['sort'] ?? 'latest';

switch($sort){
    case 'low':
        $order = "price ASC";
        break;
    case 'high':
        $order = "price DESC";
        break;
    case 'az':
        $order = "title ASC";
        break;
    case 'za':
        $order = "title DESC";
        break;
    default:
        $order = "id DESC"; // latest
}

$res = $mysqli->query("SELECT * FROM listings WHERE user_id=$uid ORDER BY $order");
?>

<!-- === DASHBOARD TITLE === -->
<h1 style="
    text-align:center;
    font-size:45px;
    margin-top:30px;
    font-weight:800;
    color:#ff6b6b;
    letter-spacing:1px;
">My Dashboard</h1>

<p style="
    text-align:center;
    font-size:22px;
    margin-top:5px;
    color:#666;
">Manage your listings, edit, delete or add new items.</p>

<!-- === ADD NEW & SORTING === -->
<div style="text-align:center; margin-top:25px;">

    <!-- Add new button -->
    <a href="add_listing.php" style="
        background:linear-gradient(90deg,#6a7cff,#8a5dff);
        padding:15px 30px;
        border-radius:15px;
        text-decoration:none;
        font-size:22px;
        font-weight:600;
        color:white;
        box-shadow:0 6px 20px rgba(0,0,0,0.2);
        transition:.3s;
    ">+ Add New Listing</a>

    <!-- Sorting Dropdown -->
    <form method="GET" style="margin-top:20px;">
        <select name="sort" onchange="this.form.submit()" style="
            padding:12px 20px;
            font-size:18px;
            border-radius:12px;
            border:2px solid #ccc;
            outline:none;
            cursor:pointer;
        ">
            <option value="latest" <?= $sort=='latest'?'selected':'' ?>>Sort: Latest First</option>
            <option value="low" <?= $sort=='low'?'selected':'' ?>>Price: Low → High</option>
            <option value="high" <?= $sort=='high'?'selected':'' ?>>Price: High → Low</option>
            <option value="az" <?= $sort=='az'?'selected':'' ?>>Title: A → Z</option>
            <option value="za" <?= $sort=='za'?'selected':'' ?>>Title: Z → A</option>
        </select>
    </form>

</div>

<!-- === LISTINGS GRID === -->
<div class="listing-container" style="margin-top:40px;">

<?php 
if($res->num_rows == 0){
    echo "<h2 style='text-align:center;color:#999;width:100%;font-size:28px;'>No listings added yet.</h2>";
}

while($row = $res->fetch_assoc()): 
    $img = $row['image'] ? 'uploads/'.$row['image'] : 'assets/no-image.png';
?>
    <div class="card" style="position:relative;">

        <!-- IMAGE -->
        <img src="<?= $img ?>" />

        <!-- TITLE -->
        <div class="card-title" style="font-size:24px;"><?= $row['title'] ?></div>

        <!-- PRICE -->
        <div class="price" style="
            font-size:22px;
            color:#ff3b5c;
            margin-top:8px;
        ">₹ <?= number_format($row['price']) ?></div>

        <!-- BUTTONS -->
        <div style="margin-top:15px;">
            <a href="edit_listing.php?id=<?= $row['id'] ?>" style="
                background:#6a7cff;
                padding:10px 18px;
                border-radius:10px;
                color:white;
                text-decoration:none;
                font-size:18px;
                margin-right:10px;
            ">✏ Edit</a>

            <a href="delete_listing.php?id=<?= $row['id'] ?>" style="
                background:#ff4757;
                padding:10px 18px;
                border-radius:10px;
                color:white;
                text-decoration:none;
                font-size:18px;
            " onclick="return confirm('Are you sure?')">🗑 Delete</a>
        </div>

    </div>

<?php endwhile; ?>
</div>

</body>
</html>
