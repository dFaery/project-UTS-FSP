<?php 
session_start();
require_once("class/Grup.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: adminhome.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
</head>
<body>
    
</body>
</html>