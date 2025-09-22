<?php
if (!isset($_GET['npk'])) {
    die("NPK tidak ditemukan.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}

$npk_to_delete = $_GET['npk'];

$stmt_select = $mysqli->prepare("SELECT foto_extension FROM dosen WHERE npk = ?");
$stmt_select->bind_param("s", $npk_to_delete);
$stmt_select->execute();
$result = $stmt_select->get_result();
if ($result->num_rows > 0) {
    $dosen = $result->fetch_assoc();
    $foto_extension = $dosen['foto_extension'];

    if (!empty($foto_extension)) {
        $file_path = "images/" . $npk_to_delete . "." . $foto_extension;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
}
$stmt_select->close();

$stmt_delete = $mysqli->prepare("DELETE FROM dosen WHERE npk = ?");
$stmt_delete->bind_param("s", $npk_to_delete);

if ($stmt_delete->execute()) {
    header("Location: tabeldosen.php");
    exit();
} else {
    echo "Error deleting record: " . $mysqli->error;
}

$stmt_delete->close();
$mysqli->close();
?>