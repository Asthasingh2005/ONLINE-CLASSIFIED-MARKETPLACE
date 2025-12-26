<?php
require 'config.php';
//require 'auth_check.php';

$user_id = $_SESSION['user']['id'];
$listing_id = intval($_GET['listing_id'] ?? 0);
$seller_id  = intval($_GET['seller_id'] ?? 0);

$q = $mysqli->prepare("
    SELECT * FROM user_messages 
    WHERE listing_id=? 
    ORDER BY id ASC
");
$q->bind_param("i", $listing_id);
$q->execute();
$res = $q->get_result();

while($row = $res->fetch_assoc()):
    $class = ($row['sender_id'] == $user_id) ? "me" : "them";
?>
    <div class="msg <?= $class ?>">
        <?= nl2br(htmlspecialchars($row['message'])) ?>
    </div>

<?php endwhile; ?>
