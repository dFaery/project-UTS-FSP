<?php
session_start();
require_once("../class/Chat.php");

$idThread   = (int)$_POST['idThread'];
$lastChatId = (int)$_POST['lastChatId'];
$username   = $_SESSION['user'];

$chatObj = new Chat();
$resChat = $chatObj->getChatAfterId($idThread, $lastChatId);

while ($chat = $resChat->fetch_assoc()) {

    if ($chat['username_pembuat'] === $username) {
        echo "<div class='message outgoing' data-id='{$chat['idchat']}'>";
    } else {
        echo "<div class='message incoming' data-id='{$chat['idchat']}'>";
    }

    echo "<p class='msg-sender'><b>{$chat['username_pembuat']}</b></p>";
    echo "<p class='msg-item'>{$chat['isi']}</p>";
    echo "<p class='msg-send-time'>{$chat['tanggal_pembuatan']}</p>";
    echo "</div>";
}
