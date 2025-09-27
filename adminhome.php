<?php
session_start(); 
if(isset($_SESSION['user'])){
    $username = $_SESSION['user'];
    if($username != "admin"){
        header("Location: login.php");
    }
}
else{
    header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Admin</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #333;
        }
        .container {
            text-align: center;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
            transition: transform 0.3s ease;
        }
        .container:hover {
            transform: translateY(-5px);
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        p {
            color: #7f8c8d;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .btn {
            padding: 15px 25px;
            font-size: 1.1rem;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            background-color: #3498db;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn:hover {
            background-color: #2980b9;
            transform: scale(1.02);
        }
        .btn.mahasiswa {
            background-color: #2ecc71;
        }
        .btn.mahasiswa:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <?php

    ?>
    <div class="container">
        <h1>Selamat Datang, Admin!</h1>
        <p>Silakan pilih tabel yang ingin Anda kelola.</p>
        <div class="button-group">
            <a href="tabeldosen.php" class="btn">Kelola Tabel Dosen</a>
            <a href="tabelmahasiswa.php" class="btn mahasiswa">Kelola Tabel Mahasiswa</a>
        </div>
    </div>
</body>
</html>