<?php
require_once("class/dosen.php");
$dosen = new Dosen();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['npk_asli'])) {
    die("Akses tidak sah.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}

try {
    // Ambil data dari form
    $npk_asli = $_POST['npk_asli'];
    $nama = $_POST['nama'];
    
    $update_dosen = $dosen->updateDosen($npk_asli, $nama);
    
} catch (Exception $e) {
    echo "ERROR MESSAGE: " . $e->getMessage();
    echo "<a href='tabeldosen.php'>Kembali</a>";
}

$mysqli->close();
?>