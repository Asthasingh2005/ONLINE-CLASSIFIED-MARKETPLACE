<?php
require 'config.php';

$id = intval($_GET['id'] ?? 0);

$stmt = $mysqli->prepare("
SELECT 
    l.*, 
    u.name AS seller, 
    u.email AS seller_email, 
    u.phone AS seller_phone,
    u.location AS seller_location,
    c.name AS cat 
FROM listings l 
LEFT JOIN users u ON l.user_id = u.id 
LEFT JOIN categories c ON l.category_id = c.id 
WHERE l.id = ? 
LIMIT 1
");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "Listing not found!";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($row['title']) ?></title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #001f3f, #003c7a);
            color: white;
        }

        .container {
            width: 90%;
            margin: 30px auto;
            display: flex;
            gap: 30px;
        }

        /* Left Image Box */
        .image-box {
            flex: 1;
            background: #00254d;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0px 0px 20px rgba(0,255,255,0.4);
        }

        .image-box img {
            width: 100%;
            height: auto;
            border-radius: 12px;
        }

        /* Right Details Box */
        .details-box {
            flex: 1;
            padding: 20px;
            background: #002b55;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(0,255,255,0.4);
        }

        .price {
            color: #00eaff;
            font-size: 28px;
            font-weight: bold;
        }

        button {
            background: #00bfff;
            border: none;
            padding: 10px 18px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 15px;
            font-weight: bold;
        }

        button:hover {
            background: #00eaff;
        }

        /* Seller Box */
        .seller-box {
            margin-top: 25px;
            background: #001d3d;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 0px 15px rgba(0,255,255,0.4);
        }

        .seller-title {
            font-size: 22px;
            color: #00eaff;
            margin-bottom: 10px;
        }

        .seller-box p {
            margin: 5px 0;
            font-size: 17px;
        }

        .email-btn {
            display: inline-block;
            background: #00aaff;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            margin-top: 10px;
            text-decoration: none;
        }

        .email-btn:hover {
            background: #00ddff;
        }

    </style>

</head>
<body>

<div class="container">

    <!-- IMAGE -->
    <div class="image-box">
        <img src="uploads/<?= $row['image'] ?>" alt="Image">
    </div>

    <!-- LISTING DETAILS -->
    <div class="details-box">
        <h1><?= htmlspecialchars($row['title']) ?></h1>

        <p class="price">₹<?= number_format($row['price']) ?></p>

        <p><b>Category:</b> <?= $row['cat'] ?></p>
        <p><b>Seller:</b> <?= $row['seller'] ?></p>

        <button onclick="alert('Login feature coming soon!')">
            Login to favourite
        </button>

        <button onclick="document.getElementById('sellerDiv').scrollIntoView();">
            Contact Seller
        </button>

        <!-- SELLER DETAILS -->
        <div class="seller-box" id="sellerDiv">
            <div class="seller-title">Seller Details</div>

            <p><b>Name:</b> <?= $row['seller'] ?></p>
            <p><b>Email:</b> <?= $row['seller_email'] ?></p>
            <p><b>Phone:</b> <?= $row['seller_phone'] ?: 'Not Provided' ?></p>
            <p><b>Location:</b> <?= $row['seller_location'] ?: 'Not Provided' ?></p>

            <a class="email-btn" href="mailto:<?= $row['seller_email'] ?>">Send Email</a>
        </div>

    </div>
</div>

</body>
</html>
