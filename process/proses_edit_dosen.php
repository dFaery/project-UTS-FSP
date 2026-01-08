<?php
require_once("../class/dosen.php");
$dosen = new Dosen();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['npk_asli'])) {
    die("Akses tidak sah.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}

if (isset($_POST['update-dosen'])) {
    $npk = $_POST['npk'];
    $nama = $_POST['nama'];
    $nama_lama = $_POST['nama_lama'];

    // proses delete foto lama
    if(!empty($foto_extension)){
        $path_foto_lama = "images/" . $npk . '_' . $nama_lama . '.' . $foto_extension;
        if (file_exists($path_foto_lama)) {
            unlink($path_foto_lama);
        }
    }

    // ini buat insert PP yang baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_info = pathinfo($_FILES['foto']['name']);
        $extension = strtolower($file_info['extension']);

        if (in_array($extension, $allowed_extensions)) {
            $foto_extension = $extension;
            $target_file = "../images/" . $npk . '_' . $nama . '.' . $foto_extension;

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                die("Gagal mengupload file.");
            }
        }
    }
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
