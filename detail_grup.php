<?php
session_start();
require_once("class/Grup.php");
require_once("class/Event.php"); // Load class Event

if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
if (!isset($_GET['id'])) { die("ID Grup tidak ditemukan."); }

$idgrup = $_GET['id'];
$username = $_SESSION['user'];

$grupObj = new Grup();
$eventObj = new Event(); // Instance Event

// Ambil Data Grup
$grup = $grupObj->getGrupById($idgrup);
if(!$grup) die("Grup tidak ditemukan.");

// Cek Permission
$isPembuat = ($grup['username_pembuat'] == $username);

// tambah event
if ($isPembuat && isset($_POST['btnTambahEvent'])) {
    $eventObj->addEvent($idgrup, $_POST['judul'], $_POST['tanggal'], $_POST['keterangan']);
    header("Location: detail_grup.php?id=$idgrup"); // Refresh
}

// hapus member
if ($isPembuat && isset($_GET['kick_user'])) {
    $grupObj->removeMember($idgrup, $_GET['kick_user']);
    header("Location: detail_grup.php?id=$idgrup"); // Refresh
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail: <?= $grup['nama'] ?></title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header-grup { border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .row { display: flex; gap: 20px; }
        .col { flex: 1; padding: 15px; background: #f9f9f9; border-radius: 8px; border: 1px solid #eee; }
        .btn { padding: 8px 12px; border-radius: 4px; text-decoration: none; color: white; border: none; cursor: pointer; }
        .btn-back { background: #7f8c8d; }
        .btn-add { background: #2ecc71; width:100%; margin-top:5px; }
        .btn-kick { background: #e74c3c; font-size: 0.8em; }
        input, textarea { width: 100%; margin-bottom: 10px; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top:10px; }
        td, th { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; font-size: 0.9em; }
        th { background: #3498db; color: white; }
    </style>
    <script src="jquery-3.7.1.js"></script>
</head>
<body>

<div class="container">
    <div class="header-grup">
        <a href="index.php" class="btn btn-back">Kembali</a>
        <h1><?= htmlspecialchars($grup['nama']) ?></h1>
        <?php if($isPembuat): ?>
            <p>Kode Join: <strong style="font-size:1.2em; background:#eee; padding:5px;"><?= $grup['kode_pendaftaran'] ?></strong></p>
        <?php else: ?>
            <p>Dosen Pengampu: <strong><?= $grup['username_pembuat'] ?></strong></p>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col">
            <h3>📅 Agenda / Event</h3>
            <?php if ($isPembuat): ?>
                <form method="POST">
                    <input type="text" name="judul" placeholder="Judul Event" required>
                    <input type="date" name="tanggal" required>
                    <textarea name="keterangan" placeholder="Keterangan"></textarea>
                    <button type="submit" name="btnTambahEvent" class="btn btn-add">Tambah Event</button>
                </form>
            <?php endif; ?>
            
            <table>
                <tr><th>Tanggal</th><th>Event</th></tr>
                <?php
                $resEvent = $eventObj->getEventsByGrup($idgrup);
                
                if($resEvent->num_rows > 0){
                    while($e = $resEvent->fetch_assoc()) {
                        echo "<tr><td>{$e['tanggal']}</td><td><b>{$e['judul']}</b><br><small>{$e['keterangan']}</small></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>Belum ada event.</td></tr>";
                }
                ?>
            </table>
        </div>

        <div class="col">
            <h3>👥 Anggota Grup</h3>
            
            <?php if ($isPembuat): ?>
                <div style="background:#e8f4fd; padding:10px; border-radius:5px; margin-bottom:10px;">
                    <b>Cari & Tambah Mahasiswa:</b>
                    <input type="text" id="keyword" placeholder="Ketik Nama atau NRP..." style="margin-top:5px;">
                    <div id="search-result" style="background:white; max-height:150px; overflow-y:auto; border:1px solid #ccc; display:none;"></div>
                </div>
            <?php endif; ?>

            <table>
                <tr><th>NRP</th><th>Nama</th><?php if($isPembuat) echo "<th>Aksi</th>"; ?></tr>
                <?php
                $resM = $grupObj->getMembers($idgrup);
                
                if($resM->num_rows > 0){
                    while($m = $resM->fetch_assoc()){
                        echo "<tr>
                            <td>{$m['nrp']}</td>
                            <td>{$m['nama']}</td>";
                            if($isPembuat){
                                echo "<td><a href='?id=$idgrup&kick_user={$m['username']}' class='btn btn-kick' onclick='return confirm(\"Keluarkan dari grup?\")'>Hapus</a></td>";
                            }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Belum ada anggota.</td></tr>";
                }
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
            if(res.trim() == 'ok'){ alert('Mahasiswa berhasil ditambahkan!'); location.reload(); } 
            else { alert('Gagal menambahkan.'); }
        });
    });
});
</script>
</body>
</html>