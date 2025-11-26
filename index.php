<?php
session_start();
require_once("class/Grup.php");

// 1. Cek Login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// 2. Cek Admin (Redirect jika admin)
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: adminhome.php");
    exit();
}

$username = $_SESSION['user'];
$grupObj = new Grup();

// 3. Cek Role (Dosen atau Mahasiswa)
$isDosen = $grupObj->isDosen($username);
$pesan = "";

// --- LOGIC DOSEN: TAMBAH GRUP ---
if ($isDosen && isset($_POST['btnSimpanGrup'])) {
    $nama = $_POST['nama_grup'];
    $deskripsi = $_POST['deskripsi'];
    $jenis = $_POST['jenis']; // Public/Private

    $kode_baru = $grupObj->createGrup($username, $nama, $deskripsi, $jenis);
    
    if ($kode_baru) {
        $pesan = "<script>alert('Grup Berhasil Dibuat! Kode Join: $kode_baru'); window.location.href='index.php';</script>";
    } else {
        $pesan = "<script>alert('Gagal membuat grup.');</script>";
    }
}

// --- LOGIC DOSEN: HAPUS GRUP ---
if ($isDosen && isset($_GET['hapus_grup'])) {
    $id_hapus = $_GET['hapus_grup'];
    if($grupObj->deleteGrup($id_hapus, $username)){
         $pesan = "<script>alert('Grup berhasil dihapus!'); window.location.href='index.php';</script>";
    } else {
         $pesan = "<script>alert('Gagal menghapus grup.');</script>";
    }
}

// --- LOGIC MAHASISWA: JOIN GRUP ---
if (!$isDosen && isset($_POST['btnJoin'])) {
    $kode_input = $_POST['kode_join'];
    if($grupObj->joinGrup($username, $kode_input)){
        $pesan = "<script>alert('Berhasil bergabung ke grup!'); window.location.href='index.php';</script>";
    } else {
        $pesan = "<script>alert('Gagal bergabung. Kode salah atau Anda sudah terdaftar.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Utama</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1100px; /* Diperlebar sedikit agar tabel lega */
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1, h2, h3 {
            color: #2c3e50;
        }

        /* Tombol */
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-logout { background-color: #e74c3c; float: right; padding: 10px 20px; }
        .btn-logout:hover { background-color: #c0392b; }
        
        .btn-save { background-color: #2ecc71; width: 100%; padding: 12px; font-size: 16px; }
        .btn-save:hover { background-color: #27ae60; }
        
        .btn-kelola { background-color: #3498db; }
        .btn-kelola:hover { background-color: #2980b9; }

        .btn-view { background-color: #f39c12; }

        /* Form Styling */
        .form-box {
            background: #eef2f5;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 30px;
        }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #555; }
        
        input[type="text"], select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed; /* Agar lebar kolom konsisten */
        }
        
        th {
            background-color: #3498db;
            color: white;
            padding: 12px;
            text-align: left;
            text-transform: uppercase;
            font-size: 0.85em;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            color: #333;
            vertical-align: middle;
            word-wrap: break-word; /* Agar teks panjang turun ke bawah */
        }
        
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }

        /* Badge Kode */
        .badge-code {
            background-color: #2c3e50;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 1.1em;
            letter-spacing: 1px;
            display: inline-block;
        }
    </style>
</head>
<body>
    
    <?= $pesan ?>

    <div class="container">
        <a href="proses_logout.php" class="btn btn-logout">Logout</a>

        <?php if ($isDosen): ?>
            
            <h1>Dashboard Dosen</h1>
            <p>Selamat datang, <b><?= htmlspecialchars($username) ?></b>. Kelola kelas Anda di sini.</p>
            <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 20px;">

            <div class="form-box">
                <h3 style="margin-top:0;">+ Buat Grup Baru</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Nama Grup / Mata Kuliah</label>
                        <input type="text" name="nama_grup" required placeholder="Contoh: Pemrograman Web A">
                    </div>
                    
                    <div class="form-group">
                        <label>Jenis Grup</label>
                        <select name="jenis">
                            <option value="Public">Public</option>
                            <option value="Private">Private</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" rows="2" placeholder="Deskripsi singkat tentang grup ini..."></textarea>
                    </div>

                    <button type="submit" name="btnSimpanGrup" class="btn btn-save">Buat Grup Sekarang</button>
                </form>
            </div>

            <h3>Daftar Grup Saya</h3>
            <table>
                <thead>
                    <tr>
                        <th width="5%">NO</th>
                        <th width="20%">NAMA GRUP</th>
                        <th width="15%">KODE JOIN</th>
                        <th width="35%">DESKRIPSI</th>
                        <th width="25%" style="text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil data grup milik dosen ini
                    $res = $grupObj->getGrupByDosen($username);

                    if ($res->num_rows > 0) {
                        $no = 1;
                        while ($row = $res->fetch_assoc()) {
                            echo "<tr>";
                            // 1. No
                            echo "<td>" . $no++ . "</td>";
                            
                            // 2. Nama Grup
                            echo "<td><b>" . htmlspecialchars($row['nama']) . "</b></td>";
                            
                            // 3. Kode Join
                            echo "<td><span class='badge-code'>" . $row['kode_pendaftaran'] . "</span></td>";
                            
                            // 4. Deskripsi
                            echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                            
                            // 5. Aksi (Kelola & Hapus)
                            echo "<td style='text-align: center;'>";
                                echo "<a href='detail_grup.php?id=" . $row['idgrup'] . "' class='btn btn-kelola'>⚙️ Kelola</a> ";
                                echo "<a href='index.php?hapus_grup=" . $row['idgrup'] . "' 
                                         class='btn btn-logout' 
                                         style='padding:8px 10px; font-size:14px; float:none;' 
                                         onclick='return confirm(\"Yakin ingin membubarkan grup ini? Semua data event dan member di dalamnya akan terhapus.\")'>
                                         🗑️ Hapus
                                      </a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding: 20px; color: gray;'>Belum ada grup yang dibuat. Silakan buat grup baru di atas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

        <?php else: ?>

            <h1>Dashboard Mahasiswa</h1>
            <p>Halo, <b><?= htmlspecialchars($username) ?></b>. Bergabunglah dengan kelas dosen Anda.</p>
            <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 20px;">

            <div class="form-box">
                <h3 style="margin-top:0;">🔑 Gabung ke Grup</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Masukkan Kode Pendaftaran</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" name="kode_join" placeholder="Contoh: X7Z9A2" required maxlength="6" style="text-transform: uppercase;">
                            <button type="submit" name="btnJoin" class="btn btn-save" style="width: auto; padding: 0 30px;">Join</button>
                        </div>
                        <small style="color: gray;">Kode pendaftaran bisa didapatkan dari Dosen pengampu.</small>
                    </div>
                </form>
            </div>

            <h3>Grup yang Diikuti</h3>
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Nama Grup</th>
                        <th width="20%">Dosen Pembuat</th>
                        <th width="35%">Deskripsi</th>
                        <th width="15%" style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil data grup yang sudah di-join mahasiswa
                    $res = $grupObj->getGrupByMahasiswa($username);

                    if ($res->num_rows > 0) {
                        $no = 1;
                        while ($row = $res->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td><b>" . htmlspecialchars($row['nama']) . "</b></td>";
                            echo "<td>" . htmlspecialchars($row['username_pembuat']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                            
                            // Tombol Lihat -> Menuju detail_grup.php?id=... (Mode View Only untuk Mhs)
                            echo "<td style='text-align: center;'>";
                            echo "<a href='detail_grup.php?id=" . $row['idgrup'] . "' class='btn btn-view'>👁️ Lihat</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding: 20px; color: gray;'>Anda belum bergabung ke grup manapun.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

        <?php endif; ?>
    </div>

</body>
</html>