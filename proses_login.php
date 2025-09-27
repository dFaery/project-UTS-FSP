<?php
session_start();
require_once("class/akun.php");
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

    if ($result->num_rows > 0) {
        $_SESSION['user'] = $username;
        if($username == "admin"){
            header("Location: adminhome.php");
        }else{
            header("Location: home.php");
        }
        echo "Account $username Found";
    } else {
        echo "Account not found";
    }
}
