<?php
require_once("class/dosen.php");
$dosen = new Dosen();

// Ambil NPK dari URL
if (!isset($_GET['npk'])) {
    die("NPK tidak ditemukan.");
}
$npk_to_edit = $_GET['npk'];

// Ambil data dosen yang akan di-edit
$result = $dosen->getDosen($npk_to_edit);

if ($result->num_rows === 0) {
    die("Data dosen tidak ditemukan.");
}
$dosen = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Dosen</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f0f2f5;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .current-photo {
            max-width: 100px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            cursor: pointer;
            text-align: center;
        }

        .btn-save {
            background-color: #f39c12;
            flex-grow: 1;
        }

        .btn-back {
            background-color: #7f8c8d;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Form Edit Dosen</h1>
        <form action="proses_edit_dosen.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="npk_asli" value="<?php echo htmlspecialchars($dosen['npk']); ?>">

            <div class="form-group">
                <label for="npk">NPK (Tidak dapat diubah)</label>
                <input type="text" id="npk" name="npk" value="<?php echo htmlspecialchars($dosen['npk']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($dosen['nama']); ?>" required>
            </div>
            <div class="form-group">
                <label for="foto">Ganti Foto (Kosongkan jika tidak ingin ganti)</label>
                <?php
                if (!empty($dosen['foto_extension'])) {
                    $foto_path = "images/" . $dosen['npk'] . "." . $dosen['foto_extension'];
                    echo "<img src='" . $foto_path . "' alt='Foto saat ini' class='current-photo'>";
                }
                ?>
                <input type="file" id="foto" name="foto" accept="image/jpeg, image/png">
            </div>
            <div class="btn-group">
                <a href="tabeldosen.php" class="btn btn-back">Batal</a>
                <button type="submit" class="btn btn-save">Update Data</button>
            </div>
        </form>
    </div>
</body>

</html>