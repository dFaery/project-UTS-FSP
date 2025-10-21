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

require_once("class/mahasiswa.php");
$mahasiswa = new Mahasiswa();

if (!isset($_GET['nrp'])) {
    die("NRP tidak ditemukan.");
}
$nrp_to_edit = $_GET['nrp'];

$result = $mahasiswa->getMahasiswa($nrp_to_edit);

if ($result->num_rows === 0) {
    die("Data mahasiswa tidak ditemukan.");
}
$mahasiswa = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Mahasiswa</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; padding: 20px; }
        .container { max-width: 500px; margin: auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #2c3e50; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .current-photo { max-width: 100px; border-radius: 5px; margin-top: 10px; }
        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { padding: 10px 15px; border: none; border-radius: 5px; text-decoration: none; color: white; cursor: pointer; text-align: center; }
        .btn-save { background-color: #f39c12; flex-grow: 1; }
        .btn-back { background-color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Form Edit Mahasiswa</h1>
        <form action="proses_edit_mahasiswa.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="nrp_asli" value="<?php echo htmlspecialchars($mahasiswa['nrp']); ?>">
            
            <div class="form-group">
                <label for="nrp">NRP (Tidak dapat diubah)</label>
                <input type="text" id="nrp" name="nrp" value="<?php echo htmlspecialchars($mahasiswa['nrp']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($mahasiswa['nama']); ?>" required>
            </div>
             <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="Pria" <?php if ($mahasiswa['gender'] == 'Pria') echo 'selected'; ?>>Pria</option>
                    <option value="Pria" <?php if ($mahasiswa['gender'] == 'Wanita') echo 'selected'; ?>>Wanita</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($mahasiswa['tanggal_lahir']); ?>" required>
            </div>
            <div class="form-group">
                <label for="angkatan">Angkatan</label>
                <input type="number" id="angkatan" name="angkatan" value="<?php echo htmlspecialchars($mahasiswa['angkatan']); ?>" required min="1900" max="2099" step="1">
            </div>
            <div class="form-group">
                <label for="foto">Ganti Foto (Kosongkan jika tidak ingin ganti)</label>                
                <?php
                if (!empty($mahasiswa['foto_extension'])) {
                    $foto_path = "images/" . $mahasiswa['nrp'] . "." . $mahasiswa['foto_extension'];                    
                    echo "<img src='" . $foto_path . "' alt='Foto saat ini' class='current-photo'>";                                        
                }
                ?>
                <input type="file" id="foto" name="foto" accept="image/jpeg, image/png">
            </div>
            <div class="btn-group">
                <a href="tabelmahasiswa.php" class="btn btn-back">Batal</a>
                <button type="submit" class="btn btn-save">Update Data</button>
            </div>
        </form>
    </div>
</body>
</html>