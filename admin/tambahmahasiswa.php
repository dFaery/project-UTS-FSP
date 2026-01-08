<?php
session_start();
if(isset($_SESSION['user'])){
    $username = $_SESSION['user'];
    $isadmin = $_SESSION['is_admin'];
    if($isadmin != 1){
        header("Location: ../login.php");
    }
}
else{
    header("Location: ../login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; padding: 20px; }
        .container { max-width: 500px; margin: auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #2c3e50; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { padding: 10px 15px; border: none; border-radius: 5px; text-decoration: none; color: white; cursor: pointer; text-align: center; }
        .btn-save { background-color: #2ecc71; flex-grow: 1; }
        .btn-back { background-color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Form Tambah Mahasiswa</h1>
        <form action="tambahakun.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nrp">NRP</label>
                <input type="text" id="nrp" name="nrp" required maxlength="9">
            </div>
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="Pria">Pria</option>
                    <option value="Wanita">Wanita</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>
            </div>
            <div class="form-group">
                <label for="angkatan">Angkatan</label>
                <input type="number" id="angkatan" name="angkatan" required min="1900" max="2099" step="1">
            </div>
            <div class="form-group">
                <label for="foto">Foto</label>
                <input type="file" id="foto" name="foto" accept="image/jpeg, image/png">
            </div>
            <div class="btn-group">
                <a href="tabelmahasiswa.php" class="btn btn-back">Batal</a>
                <button type="submit" class="btn btn-save" name="submit-mahasiswa">Simpan Data</button>
            </div>
        </form>
    </div>
</body>
</html>