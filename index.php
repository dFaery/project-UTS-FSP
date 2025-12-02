<?php
session_start();
require_once("class/Grup.php");

if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) { header("Location: adminhome.php"); exit(); }

$username = $_SESSION['user'];
$grupObj = new Grup();
$isDosen = $grupObj->isDosen($username); // Saya asumsi method ini ada di class Grup
$pesan = "";

// LOGIC 1: BUAT GRUP & HAPUS GRUP (Dosen Only)
if ($isDosen && isset($_POST['btnSimpanGrup'])) {
    $kode_baru = $grupObj->createGrup($username, $_POST['nama_grup'], $_POST['deskripsi'], $_POST['jenis']);
    if ($kode_baru) $pesan = "<script>alert('Grup Dibuat! Kode: $kode_baru'); window.location.href='index.php';</script>";
}
if ($isDosen && isset($_GET['hapus_grup'])) {
    if($grupObj->deleteGrup($_GET['hapus_grup'], $username)) $pesan = "<script>alert('Grup dihapus!'); window.location.href='index.php';</script>";
}

// LOGIC 2: JOIN GRUP
if (isset($_POST['btnJoin'])) {
    $status = $grupObj->joinGrup($username, $_POST['kode_join']);
    
    if($status == "SUCCESS"){
        $pesan = "<script>alert('Berhasil bergabung ke grup!'); window.location.href='index.php';</script>";
    } else if ($status == "PRIVAT") {
        $pesan = "<script>alert('GAGAL: Grup ini bersifat PRIVAT. Anda tidak diizinkan masuk sendiri. Silahkan hubungi Dosen PJMK.');</script>";
    } else if ($status == "ALREADY_MEMBER") {
        $pesan = "<script>alert('Anda sudah bergabung di grup ini.');</script>";
    } else if ($status == "OWNER") {
        $pesan = "<script>alert('Anda adalah pemilik grup ini.');</script>";
    } else {
        $pesan = "<script>alert('Grup tidak ditemukan.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1100px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #2c3e50; }
        .btn { padding: 8px 12px; border: none; border-radius: 4px; color: white; cursor: pointer; text-decoration: none; font-weight: bold; }
        .btn-logout { background: #e74c3c; float: right; }
        .btn-save { background: #2ecc71; width: 100%; padding: 10px; }
        .btn-kelola { background: #3498db; }
        .btn-view { background: #f39c12; }
        .form-box { background: #eef2f5; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd; }
        .form-group { margin-bottom: 10px; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 30px; table-layout: fixed; }
        th { background: #3498db; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; word-wrap: break-word; }
        .badge-code { background: #2c3e50; color: #fff; padding: 3px 6px; border-radius: 3px; font-family: monospace; }
        .flex-row { display: flex; gap: 20px; }
        .flex-col { flex: 1; }
    </style>
</head>
<body>
    <?= $pesan ?>
    <div class="container">
        <a href="proses_logout.php" class="btn btn-logout">Logout</a>

        <?php if ($isDosen): ?>
            <h1>Dashboard Dosen</h1>
            
            <div class="flex-row">
                <div class="flex-col form-box">
                    <h3>Buat Grup Baru</h3>
                    <form method="POST">
                        <div class="form-group"><input type="text" name="nama_grup" required placeholder="Nama Mata Kuliah"></div>
                        <div class="form-group">
                            <select name="jenis">
                                <option value="Publik">Publik</option>
                                <option value="Privat">Privat</option>
                            </select>
                        </div>
                        <div class="form-group"><textarea name="deskripsi" rows="1" placeholder="Deskripsi"></textarea></div>
                        <button type="submit" name="btnSimpanGrup" class="btn btn-save">Buat Grup</button>
                    </form>
                </div>
                
                <div class="flex-col form-box" style="background: #fff8e1;">
                    <h3>Join Grup Dosen Lain</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Kode Pendaftaran:</label>
                            <input type="text" name="kode_join" required placeholder="Contoh: X7Z9A2">
                        </div>
                        <button type="submit" name="btnJoin" class="btn btn-save" style="background:#f39c12;">Gabung Kelas</button>
                    </form>
                </div>
            </div>

            <h3>Grup Saya (Pemilik)</h3>
            <table>
                <thead><tr><th width="5%">No</th><th width="25%">Nama Grup</th><th width="15%">Kode</th><th width="35%">Deskripsi</th><th width="20%">Aksi</th></tr></thead>
                <tbody>
                    <?php
                    $res = $grupObj->getGrupByDosen($username); 
                    if ($res->num_rows > 0) {
                        $no = 1;
                        while ($r = $res->fetch_assoc()) {
                            $labelJenis = ($r['jenis'] == 'Privat') ? "<span style='background:red; color:white; padding:2px 4px; font-size:10px; border-radius:3px;'>PRIVAT</span>" : "";
                            
                            echo "<tr>
                                <td>".$no++."</td>
                                <td><b>".htmlspecialchars($r['nama'])."</b> $labelJenis</td>
                                <td><span class='badge-code'>".$r['kode_pendaftaran']."</span></td>
                                <td>".htmlspecialchars($r['deskripsi'])."</td>
                                <td>
                                    <a href='detail_grup.php?id=".$r['idgrup']."' class='btn btn-kelola'>Kelola</a>
                                    <a href='index.php?hapus_grup=".$r['idgrup']."' class='btn btn-logout' style='float:none; padding:8px; background:#e74c3c;' onclick='return confirm(\"Hapus grup?\")'>Hapus</a>
                                </td>
                            </tr>";
                        }
                    } else { echo "<tr><td colspan='5'>Belum ada grup.</td></tr>"; }
                    ?>
                </tbody>
            </table>

            </table> <hr style="border:0; border-top:1px dashed #ccc; margin:30px 0;">

            <h3>Grup yang Saya Ikuti (Tim Dosen)</h3>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Nama Grup</th>
                        <th width="20%">Dosen Pemilik</th>
                        <th width="35%">Deskripsi</th>
                        <th width="15%" style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $resJoined = $grupObj->getJoinedGroups($username);

                    if ($resJoined->num_rows > 0) {
                        $no = 1;
                        while ($row = $resJoined->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td><b>" . htmlspecialchars($row['nama']) . "</b></td>";
                            echo "<td>" . htmlspecialchars($row['username_pembuat']) . "</td>"; 
                            echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                            
                            echo "<td style='text-align: center;'>";
                            echo "<a href='detail_grup.php?id=" . $row['idgrup'] . "' class='btn btn-save' style='padding:5px 10px; width:auto; background: #f39c12;'>Lihat</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; color:gray;'>Anda belum bergabung dengan grup dosen lain.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <h3>Grup Publik (Dosen Lain)</h3>
            <table>
                <thead><tr><th width="5%">No</th><th width="25%">Nama Grup</th><th width="20%">Dosen</th><th width="30%">Deskripsi</th><th width="20%">Aksi</th></tr></thead>
                <tbody>
                    <?php
                    $resPub = $grupObj->getAvailablePublicGroups($username); 
                    if ($resPub->num_rows > 0) {
                        $no = 1;
                        while ($r = $resPub->fetch_assoc()) {
                            echo "<tr>
                                <td>".$no++."</td>
                                <td><b>".htmlspecialchars($r['nama'])."</b></td>
                                <td>".htmlspecialchars($r['username_pembuat'])."</td>
                                <td>".htmlspecialchars($r['deskripsi'])."</td>
                                <td>
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='kode_join' value='".$r['kode_pendaftaran']."'>
                                        <button type='submit' name='btnJoin' class='btn btn-save' style='padding:5px 10px; width:auto;'>Gabung</button>
                                    </form>
                                </td>
                            </tr>";
                        }
                    } else { echo "<tr><td colspan='5'>Tidak ada grup publik baru.</td></tr>"; }
                    ?>
                </tbody>
            </table>

        <?php else: ?>
            
            <h1>Dashboard Mahasiswa</h1>
            
            <div class="form-box">
                <h3>Gabung ke Grup via Kode</h3>
                <form method="POST">
                    <div style="display:flex; gap:10px;">
                        <input type="text" name="kode_join" placeholder="Kode Pendaftaran" required>
                        <button type="submit" name="btnJoin" class="btn btn-save" style="width:auto;">Join</button>
                    </div>
                    <small style="color:red;">* Grup Privat hanya bisa dimasukkan manual oleh Dosen.</small>
                </form>
            </div>

            <h3>Grup yang Saya Ikuti</h3>
            <table>
                <thead><tr><th width="5%">No</th><th width="25%">Nama Grup</th><th width="20%">Dosen</th><th width="35%">Deskripsi</th><th width="15%">Aksi</th></tr></thead>
                <tbody>
                    <?php
                    $res = $grupObj->getJoinedGroups($username);
                    if ($res->num_rows > 0) {
                        $no = 1;
                        while ($r = $res->fetch_assoc()) {
                            echo "<tr>
                                <td>".$no++."</td>
                                <td><b>".htmlspecialchars($r['nama'])."</b></td>
                                <td>".htmlspecialchars($r['username_pembuat'])."</td>
                                <td>".htmlspecialchars($r['deskripsi'])."</td>
                                <td><a href='detail_grup.php?id=".$r['idgrup']."' class='btn btn-view'>Lihat</a></td>
                            </tr>";
                        }
                    } else { echo "<tr><td colspan='5'>Belum join grup.</td></tr>"; }
                    ?>
                </tbody>
            </table>

            <h3>Jelajahi Grup Publik</h3>
            <table>
                <thead><tr><th width="5%">No</th><th width="25%">Nama Grup</th><th width="20%">Dosen</th><th width="35%">Deskripsi</th><th width="15%">Aksi</th></tr></thead>
                <tbody>
                    <?php
                    $resPub = $grupObj->getAvailablePublicGroups($username); 
                    if ($resPub->num_rows > 0) {
                        $no = 1;
                        while ($r = $resPub->fetch_assoc()) {
                            echo "<tr>
                                <td>".$no++."</td>
                                <td><b>".htmlspecialchars($r['nama'])."</b></td>
                                <td>".htmlspecialchars($r['username_pembuat'])."</td>
                                <td>".htmlspecialchars($r['deskripsi'])."</td>
                                <td>
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='kode_join' value='".$r['kode_pendaftaran']."'>
                                        <button type='submit' name='btnJoin' class='btn btn-save' style='padding:5px 10px; width:auto;'>Gabung</button>
                                    </form>
                                </td>
                            </tr>";
                        }
                    } else { echo "<tr><td colspan='5'>Tidak ada grup publik baru yang tersedia.</td></tr>"; }
                    ?>
                </tbody>
            </table>

        <?php endif; ?>
    </div>
</body>
</html>