<?php
if (!isset($_GET['nrp'])) {
    die("NRP tidak ditemukan.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}

$nrp_to_delete = $_GET['nrp'];

$stmt_select = $mysqli->prepare("SELECT foto_extention FROM mahasiswa WHERE nrp = ?");
$stmt_select->bind_param("s", $nrp_to_delete);
$stmt_select->execute();
$result = $stmt_select->get_result();
if ($result->num_rows > 0) {
    $mahasiswa = $result->fetch_assoc();
    $foto_extension = $mahasiswa['foto_extention'];

    if (!empty($foto_extension)) {
        $file_path = "images/" . $nrp_to_delete . "." . $foto_extension;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
}
$stmt_select->close();

$stmt_delete = $mysqli->prepare("DELETE FROM mahasiswa WHERE nrp = ?");
$stmt_delete->bind_param("s", $nrp_to_delete);

if ($stmt_delete->execute()) {
    header("Location: tabelmahasiswa.php");
    exit();
} else {
    echo "Error deleting record: " . $mysqli->error;
}

$stmt_delete->close();
$mysqli->close();
?>