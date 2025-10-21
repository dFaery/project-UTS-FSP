<?php
session_start();
if(isset($_SESSION['user'])){
    $username = $_SESSION['user'];
    $isadmin = $_SESSION['is_admin'];
    if($isadmin != 1){
        header("Location: login.php");
    }
}
else{
    header("Location: login.php");
}

require_once("class/dosen.php");
require_once("class/mahasiswa.php");
$dosen = new Dosen();
$mahasiswa = new Mahasiswa();

// Validasi dasar
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak sah.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Gagal terhubung ke MySQL: " . $mysqli->connect_error);
}

$foto_extension = null;

if (isset($_POST['submit-dosen'])) {
    $npk = $_POST['npk'];
    $nama = $_POST['nama'];
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
} else if (isset($_POST['submit-mahasiswa'])) {
    $nrp = $_POST['nrp'];
    $nama = $_POST['nama'];
    $gender = $_POST['gender'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $angkatan = $_POST['angkatan'];
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
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun</title>
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

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
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
            background-color: #2ecc71;
            flex-grow: 1;
        }

        .btn-back {
            background-color: #7f8c8d;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Form Buat Akun</h1>
        <form action="proses_tambah_akun.php" method="POST" enctype="multipart/form-data">
            <?php
            if (isset($npk)) {
                $id = $npk;
            } else if (isset($nrp)) {
                $id = $nrp;
            } else {
                die("ID tidak ditemukan.");
            }
            ?>

            <div class="form-group">
                <label for="npk">NPK / NRP</label>
                <input type="text" id="npk" name="<?php if (isset($npk)) { echo 'npk'; } else if (isset($nrp)) { echo 'nrp';}?>" 
                value="<?php echo htmlspecialchars($id); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Data dosen & mahasiswa -->
            <input type="hidden" name="nama" value="<?php echo htmlspecialchars($nama); ?>">
            <input type="hidden" name="foto_extension" value="<?php echo htmlspecialchars($foto_extension); ?>">

            <!-- tambahan data mahasiswa -->
            <?php
            if (isset($nrp)) {
                echo '<input type="hidden" name="gender" value="' . htmlspecialchars($gender) . '">';
                echo '<input type="hidden" name="tanggal_lahir" value="' . htmlspecialchars($tanggal_lahir) . '">';
                echo '<input type="hidden" name="angkatan" value="' . htmlspecialchars($angkatan) . '">';
            }
            ?>
            <div class="btn-group">
                <button type="submit"
                    class="btn btn-save"
                    name="<?php
                            if (isset($npk)) {
                                echo 'submit_dosen';
                            } else if (isset($nrp)) {
                                echo 'submit_mahasiswa';
                            }
                            ?>">Register</button>
            </div>
        </form>
    </div>
</body>

</html>