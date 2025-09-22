<?php
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
$stmt = $mysqli->prepare("INSERT INTO dosen (npk, nama, foto_extension) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $npk, $nama, $foto_extension);

if ($stmt->execute()) {
    // Jika berhasil, redirect kembali ke halaman tabel dosen
    header("Location: tabeldosen.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>