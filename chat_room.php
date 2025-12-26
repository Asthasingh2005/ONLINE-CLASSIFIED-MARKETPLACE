<?php
require 'config.php';
//require 'auth_check.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$listing_id = intval($_GET['listing_id'] ?? 0);
$seller_id  = intval($_GET['seller_id'] ?? 0);
$user_id    = $_SESSION['user']['id'];

?>
<!DOCTYPE html>
<html>
<head>
<title>Chat</title>
<style>
body {
    background:#0a0f24;
    color:white;
    font-family:Arial;
}
.chat-box {
    width:100%;
    max-width:600px;
    margin:auto;
    background:#111a33;
    padding:20px;
    border-radius:15px;
}
.messages-area {
    height:400px;
    overflow-y:auto;
    background:#0d1426;
    padding:15px;
    border-radius:10px;
}
.msg {
    margin-bottom:12px;
    padding:10px;
    border-radius:10px;
    max-width:70%;
}
.me {
    background:#00eaff;
    color:black;
    margin-left:auto;
}
.them {
    background:#233557;
}
.input-area {
    margin-top:15px;
    display:flex;
    gap:10px;
}
input {
    flex:1;
    padding:12px;
    border-radius:10px;
    border:none;
}
button {
    padding:12px 20px;
    border-radius:10px;
    background:#00eaff;
    border:none;
    font-weight:bold;
}
</style>
</head>
<body>

<div class="chat-box">
    <h2>💬 Chat</h2>

    <div id="messages" class="messages-area"></div>

    <div class="input-area">
        <input type="text" id="msg" placeholder="Type message...">
        <button onclick="sendMsg()">Send</button>
    </div>
</div>

<script>
let listing_id = <?= $listing_id ?>;
let seller_id  = <?= $seller_id ?>;

function loadMessages(){
    fetch("get_messages.php?listing_id=" + listing_id + "&seller_id=" + seller_id)
        .then(res => res.text())
        .then(data => {
            document.getElementById("messages").innerHTML = data;
        });
}
setInterval(loadMessages, 1000);
loadMessages();

function sendMsg(){
    let msg = document.getElementById("msg").value;
    if(msg.trim() === "") return;

    fetch("send_message.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "listing_id=" + listing_id + "&seller_id=" + seller_id + "&message=" + encodeURIComponent(msg)
    }).then(() => {
        document.getElementById("msg").value = "";
    });
}
</script>

</body>
</html>
