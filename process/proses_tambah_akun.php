<?php
session_start();
require_once("class/akun.php");
require_once("../class/dosen.php");
require_once("../class/mahasiswa.php");
echo "<script src='../js/jquery-3.7.1.js'></script>";

$akun = new Akun();
$username = $_POST['username'];
$password = $_POST['password'];

if (isset($_POST['submit_dosen'])) {
    $dosen = new Dosen();

    $npk = $_POST['npk'];
    $nama = $_POST['nama'];
    $foto_extension = $_POST['foto_extension'];

    try {
        $dosen->insertDosen($npk, $nama, $foto_extension);
        $akun->insertAkunDosen($username, $password, $npk);
        header("Location: tabeldosen.php?dstatus=success");
        exit;
    } catch (Exception $e) {
        header("Location: tabeldosen.php?dstatus=fail");
        exit;
    }
} else if (isset($_POST['submit_mahasiswa'])) {
    $mahasiswa = new Mahasiswa();

    $nrp = $_POST['nrp'];
    $nama = $_POST['nama'];
    $gender = $_POST['gender'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $angkatan = $_POST['angkatan'];
    $foto_extension = $_POST['foto_extension'];

    try {

        $mahasiswa->insertMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension);
        $akun->insertAkunMahasiswa($username, $password, $nrp);
        header("Location: tabelmahasiswa.php?mstatus=success");
        exit;
    } catch (Exception $e) {
        header("Location: tabelmahasiswa.php?mstatus=fail");
        exit;
    }
}
