<?php
require_once("../class/mahasiswa.php");
$mahasiswa = new Mahasiswa();

if (!isset($_GET['nrp'])) {
    die("NRP tidak ditemukan.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}

$nrp_to_delete = $_GET['nrp'];

$delete_mahasiswa= $mahasiswa->deleteMahasiswa($nrp_to_delete);

$mysqli->close();
?>