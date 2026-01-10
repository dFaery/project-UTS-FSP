<?php   
session_start();
require_once("../class/Thread.php");

// 1. Cek Login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$threadObj = new Thread();

$idthread = $_POST['idThread'];
$newStatus = $_POST['status'];
$username = $_SESSION['user'];

$threadObj->updateThreadStatus($idthread, $newStatus);

echo $newStatus;
?>