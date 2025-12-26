<?php
// config.php
if (session_status() === PHP_SESSION_NONE) session_start();

$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'classifieds_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die("DB Connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");

// SAFE esc() FIX — duplicate error kabhi nahi aayega
if (!function_exists('esc')) {
    function esc($s) {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
    }
}

function is_logged_in() {
   return !empty($_SESSION['user_id']);
}
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}
?>
