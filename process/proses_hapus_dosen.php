<?php
require_once("../class/dosen.php");

$dosen = new Dosen();
if (!isset($_GET['npk'])) {
    die("NPK tidak ditemukan.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}

$npk_to_delete = $_GET['npk'];

$delete_dosen = $dosen->deleteDosen($npk_to_delete);
$mysqli->close();
?>