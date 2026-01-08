<?php
require_once("class/mahasiswa.php");
$mahasiswa = new Mahasiswa();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['nrp_asli'])) {
    die("Akses tidak sah.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}

try {
    // Ambil data dari form
    $nrp_asli = $_POST['nrp_asli'];
    $nama = $_POST['nama'];
    $gender = $_POST['gender'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $angkatan = $_POST['angkatan'];

    $upate_mahasiswa = $mahasiswa->updateMahasiswa($nrp_asli, $nama, $gender, $tanggal_lahir, $angkatan);
} catch (Exception $e) {
    echo "ERROR MESSAGE: " . $e->getMessage();
    echo "<a href='tabelmahasiswa.php'>Kembali</a>";
}
$mysqli->close();
