<?php
session_start();
require_once("class/akun.php");
require_once("class/dosen.php");
require_once("class/mahasiswa.php");

$akun = new Akun();
$username = $_POST['username'];
$password = $_POST['password'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak sah.");
}
if (isset($_POST['submit_dosen'])) {
    $dosen = new Dosen();

    $npk = $_POST['npk'];
    $nama = $_POST['nama'];
    $foto_extension = $_POST['foto_extension'];

    $dosen->insertDosen($npk, $nama, $foto_extension);
    $akun->insertAkunDosen($username, $password, $npk);

    header("Location: tabeldosen.php");
} else if (isset($_POST['submit_mahasiswa'])) {
    $mahasiswa = new Mahasiswa();
    
    $nrp = $_POST['nrp'];
    $nama = $_POST['nama'];
    $gender = $_POST['gender'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $angkatan = $_POST['angkatan'];
    $foto_extension = $_POST['foto_extension'];
    
    $mahasiswa->insertMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension);
    $akun->insertAkunMahasiswa($username, $password, $nrp);
    
    header("Location: tabelmahasiswa.php");
}
