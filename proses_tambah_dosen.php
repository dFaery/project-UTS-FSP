<?php
require_once("class/dosen.php");
$dosen = new Dosen();

// Validasi dasar
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['npk']) || empty($_POST['nama'])) {
    die("Akses tidak sah atau data tidak lengkap.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}

$npk = $_POST['npk'];
$nama = $_POST['nama'];
$foto_extension = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $file_info = pathinfo($_FILES['foto']['name']);
    $extension = strtolower($file_info['extension']);

    if (in_array($extension, $allowed_extensions)) {
        $foto_extension = $extension;
        $target_file = "images/" . $npk . '.' . $foto_extension;
        
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            die("Gagal mengupload file.");
        }
    }
}

// Gunakan prepared statement untuk memasukkan data
$result = $dosen->insertDosen($npk, $nama, $foto_extension);
?>