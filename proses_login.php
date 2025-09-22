<?php

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

    $sql_login = "SELECT username, password FROM akun WHERE username=? AND password=?;";
    $stmt_login = $mysqli->prepare($sql_login);
    $stmt_login->bind_param("ss", $username, $password);
    $stmt_login->execute();
    $result = $stmt_login->get_result();

    if ($result->num_rows > 0) {
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
