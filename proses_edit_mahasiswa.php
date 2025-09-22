<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['nrp_asli'])) {
    die("Akses tidak sah.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}

// Ambil data dari form
$nrp_asli = $_POST['nrp_asli'];
$nama = $_POST['nama'];
$gender = $_POST['gender'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$angkatan = $_POST['angkatan'];

// Ambil data foto lama
$stmt_select = $mysqli->prepare("SELECT foto_extention FROM mahasiswa WHERE nrp = ?");
$stmt_select->bind_param("s", $nrp_asli);
$stmt_select->execute();
$result = $stmt_select->get_result();
$mahasiswa_lama = $result->fetch_assoc();
$stmt_select->close();

$foto_extension = $mahasiswa_lama['foto_extention'];

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    if (!empty($foto_extension)) {
        $path_foto_lama = "images/" . $nrp_asli . "." . $foto_extension;
        if (file_exists($path_foto_lama)) {
            unlink($path_foto_lama);
        }
    }

    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $file_info = pathinfo($_FILES['foto']['name']);
    $extension = strtolower($file_info['extension']);
    if (in_array($extension, $allowed_extensions)) {
        $foto_extension = $extension;
        $target_file = "images/" . $nrp_asli . '.' . $foto_extension;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);
    }
}

$stmt_update = $mysqli->prepare("UPDATE mahasiswa SET nama = ?, gender = ?, tanggal_lahir = ?, angkatan = ?, foto_extention = ? WHERE nrp = ?");
$stmt_update->bind_param("sssiss", $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension, $nrp_asli);

if ($stmt_update->execute()) {
    header("Location: tabelmahasiswa.php");
    exit();
} else {
    echo "Error updating record: " . $mysqli->error;
}

$stmt_update->close();
$mysqli->close();
?>