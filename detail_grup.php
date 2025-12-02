<?php
session_start();
require_once("class/Grup.php");
require_once("class/Event.php");

if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
if (!isset($_GET['id'])) { die("ID Grup tidak ditemukan."); }

$idgrup = $_GET['id'];
$username = $_SESSION['user'];

$grupObj = new Grup();
$eventObj = new Event();

$grup = $grupObj->getGrupById($idgrup);
if(!$grup) die("Grup tidak ditemukan.");

// --- PERMISSION CHECK ---
$isPembuat = ($grup['username_pembuat'] == $username);
$isDosen = $grupObj->isDosen($username);
$isMember = $grupObj->isMember($idgrup, $username); // Cek apakah dia member (bukan owner)

// Hak Akses Mengelola Event: Pembuat ATAU (Member DAN Dosen)
$canManageEvent = ($isPembuat || ($isMember && $isDosen));

// --- LOGIC: UPDATE INFO GRUP (Owner Only) ---
if ($isPembuat && isset($_POST['btnUpdateGrup'])) {
    $grupObj->updateGrup($idgrup, $_POST['nama'], $_POST['deskripsi'], $_POST['jenis']);
    echo "<script>alert('Info grup diperbarui!'); window.location.href='detail_grup.php?id=$idgrup';</script>";
}

// --- LOGIC: TAMBAH EVENT (Owner & Dosen Member) ---
if ($canManageEvent && isset($_POST['btnTambahEvent'])) {
    $eventObj->addEvent($idgrup, $_POST['judul'], $_POST['tanggal'], $_POST['keterangan']);
    header("Location: detail_grup.php?id=$idgrup");
}

// --- LOGIC: KELUAR GRUP (Member Only - Dosen/Mhs) ---
if (isset($_GET['action']) && $_GET['action'] == 'leave' && !$isPembuat) {
    $grupObj->removeMember($idgrup, $username);
    echo "<script>alert('Anda keluar dari grup.'); window.location.href='index.php';</script>";
}

// --- LOGIC: KICK MEMBER (Owner Only) ---
if ($isPembuat && isset($_GET['kick_user'])) {
    $grupObj->removeMember($idgrup, $_GET['kick_user']);
    header("Location: detail_grup.php?id=$idgrup");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail: <?= htmlspecialchars($grup['nama']) ?></title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .row { display: flex; gap: 20px; margin-top:20px; }
        .col { flex: 1; padding: 15px; background: #f9f9f9; border-radius: 8px; border: 1px solid #eee; }
        .btn { padding: 8px 12px; border-radius: 4px; color: white; border: none; cursor: pointer; text-decoration:none; display:inline-block; font-size:14px; }
        .btn-back { background: #7f8c8d; }
        .btn-save { background: #2ecc71; }
        .btn-kick { background: #e74c3c; font-size: 0.8em; }
        .btn-leave { background: #c0392b; float:right; }
        input, select, textarea { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #3498db; color: white; }
        .edit-box { background: #fff8e1; padding: 15px; border: 1px solid #ffe082; border-radius: 5px; margin-bottom: 20px; }
    </style>
    <script src="jquery-3.7.1.js"></script>
</head>
<body>

<div class="container">
    <div style="margin-bottom:15px; overflow:hidden;">
        <a href="index.php" class="btn btn-back">Kembali</a>
        <?php if(!$isPembuat): ?>
            <a href="?id=<?= $idgrup ?>&action=leave" class="btn btn-leave" onclick="return confirm('Keluar grup?')">🚪 Keluar Grup</a>
        <?php endif; ?>
    </div>

    <?php if ($isPembuat): ?>
        <div class="edit-box">
            <h3>✏️ Pengaturan Grup</h3>
            <form method="POST" style="display:flex; gap:10px; align-items:flex-end;">
                <div style="flex:2">
                    <label>Nama Grup</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($grup['nama']) ?>" required>
                </div>
                <div style="flex:1">
                    <label>Jenis</label>
                    <select name="jenis">
                        <option value="Publik" <?= $grup['jenis']=='Publik'?'selected':'' ?>>Publik</option>
                        <option value="Privat" <?= $grup['jenis']=='Privat'?'selected':'' ?>>Privat</option>
                    </select>
                </div>
                <div style="flex:2">
                    <label>Deskripsi</label>
                    <input type="text" name="deskripsi" value="<?= htmlspecialchars($grup['deskripsi']) ?>">
                </div>
                <button type="submit" name="btnUpdateGrup" class="btn btn-save" style="height:35px; margin-bottom:10px;">Simpan</button>
            </form>
            <p style="margin:5px 0 0 0;">Kode Join: <strong style="background:#fff; padding:3px;"><?= $grup['kode_pendaftaran'] ?></strong></p>
        </div>
    <?php else: ?>
        <h1><?= htmlspecialchars($grup['nama']) ?></h1>
        <p>Pengampu: <b><?= $grup['username_pembuat'] ?></b> | <?= htmlspecialchars($grup['deskripsi']) ?></p>
    <?php endif; ?>

    <div class="row">
        <div class="col">
            <h3>Agenda / Event</h3>
            <?php if ($canManageEvent): ?>
                <form method="POST">
                    <input type="text" name="judul" placeholder="Judul Event" required>
                    <input type="date" name="tanggal" required>
                    <textarea name="keterangan" placeholder="Keterangan"></textarea>
                    <button type="submit" name="btnTambahEvent" class="btn btn-save" style="width:100%;">Tambah Event</button>
                </form>
                <hr>
            <?php endif; ?>
            
            <table>
                <tr><th>Tanggal</th><th>Event</th></tr>
                <?php
                $resEvent = $eventObj->getEventsByGrup($idgrup);
                if($resEvent->num_rows > 0){
                    while($e = $resEvent->fetch_assoc()) echo "<tr><td>{$e['tanggal']}</td><td><b>{$e['judul']}</b><br><small>{$e['keterangan']}</small></td></tr>";
                } else { echo "<tr><td colspan='2'>Kosong.</td></tr>"; }
                ?>
            </table>
        </div>

        <div class="col">
            <h3>👥 Anggota Grup</h3>
            
            <?php if ($isPembuat): ?>
                <div style="background:#e8f4fd; padding:10px; margin-bottom:10px;">
                    <b>Tambah Mahasiswa:</b>
                    <input type="text" id="keyword" placeholder="Cari Nama/NRP..." style="margin-top:5px;">
                    <div id="search-result" style="background:white; max-height:150px; overflow-y:auto; display:none;"></div>
                </div>
            <?php endif; ?>

            <table>
                <tr><th>NRP</th><th>Nama</th><?php if($isPembuat) echo "<th>Aksi</th>"; ?></tr>
                <?php
                // Menggunakan fungsi baru getMembers yang sudah disesuaikan query-nya
                $resM = $grupObj->getMembers($idgrup);
                if($resM->num_rows > 0){
                    while($m = $resM->fetch_assoc()){
                        // Visualisasi beda role
                        $roleBadge = ($m['role'] == 'Dosen') ? " <span style='background:gold; padding:2px; font-size:10px;'>Dosen</span>" : "";
                        
                        echo "<tr>
                            <td>{$m['id_nomor']}</td>
                            <td>{$m['nama_lengkap']}$roleBadge</td>";
                            
                            if($isPembuat){
                                echo "<td><a href='?id=$idgrup&kick_user={$m['username']}' class='btn btn-kick' onclick='return confirm(\"Keluarkan?\")'>Kick</a></td>";
                            }
                        echo "</tr>";
                    }
                } else { echo "<tr><td colspan='3'>Belum ada anggota.</td></tr>"; }
                ?>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#keyword').keyup(function(){
        let key = $(this).val();
        let idgrup = <?= $idgrup ?>;
        if(key.length > 2){
            $.ajax({
                url: 'ajax_search_mhs.php',
                method: 'POST',
                data: {keyword: key, idgrup: idgrup},
                success: function(data){ $('#search-result').html(data).show(); }
            });
        } else { $('#search-result').hide(); }
    });
    $(document).on('click', '.add-btn-ajax', function(){
        let user = $(this).data('user');
        let idgrup = <?= $idgrup ?>;
        $.post('ajax_add_member.php', {username: user, idgrup: idgrup}, function(res){
            if(res.trim() == 'ok'){ alert('Berhasil ditambahkan!'); location.reload(); } 
        });
    });
});
</script>
</body>
</html>