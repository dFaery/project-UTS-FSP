<?php
session_start();
require_once("../class/akun.php");
$akun = new Akun();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak sah.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}


if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $akun->login($username, $password);
    if ($result === false) {
        echo "Account not found ";        
        echo "<br><a href='../login.php'>Kembali ke Halaman Login</a>";
    } else {
        $_SESSION['user'] = $result['username'];
        $_SESSION['is_admin'] = $result['isadmin'];

        if ($result['isadmin'] == 1) {
            header("Location: ../admin/adminhome.php");
        } else {
            header("Location: ../index.php");
        }
    }
}
