<?php
require_once("class/mahasiswa.php");
$mahasiswa = new Mahasiswa();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['nrp']) || empty($_POST['nama'])) {
    die("Akses tidak sah atau data tidak lengkap.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}

$nrp = $_POST['nrp'];
$nama = $_POST['nama'];
$gender = $_POST['gender'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$angkatan = $_POST['angkatan'];
$foto_extension = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $file_info = pathinfo($_FILES['foto']['name']);
    $extension = strtolower($file_info['extension']);

    if (in_array($extension, $allowed_extensions)) {
        $foto_extension = $extension;
        $target_file = "images/" . $nrp . '.' . $foto_extension;
        
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            die("Gagal mengupload file.");
        }
    }
}

$result = $mahasiswa->insertMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension);


?>