<?php
session_start();
require_once("class/Grup.php");
require_once("class/Event.php");
require_once("class/Thread.php");

// 1. Cek Login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// 2. Cek Parameter ID
if (!isset($_GET['id'])) {
    die("ID Grup tidak ditemukan.");
}

$idgrup = $_GET['id'];
$username = $_SESSION['user'];

$grupObj = new Grup();
$eventObj = new Event();
$threadObj = new Thread();

// 3. Ambil Data Grup
$grup = $grupObj->getGrupById($idgrup);
if (!$grup) die("Grup tidak ditemukan.");

// --- PERMISSION VARIABLES ---
$isPembuat = ($grup['username_pembuat'] == $username);
$isMember = $grupObj->isMember($idgrup, $username);
$isDosen = $grupObj->isDosen($username);

// --- SECURITY CHECK ---
if (!$isPembuat && !$isMember) {
    echo "<script>alert('Akses Ditolak: Anda bukan anggota grup ini atau telah dikeluarkan.'); window.location.href='index.php';</script>";
    exit();
}

// Hak Akses Mengelola Event
$canManageEvent = ($isPembuat || ($isMember && $isDosen));

// --- LOGIC: UPDATE INFO GRUP ---
if ($isPembuat && isset($_POST['btnUpdateGrup'])) {
    $grupObj->updateGrup($idgrup, $_POST['nama'], $_POST['deskripsi'], $_POST['jenis']);
    echo "<script>alert('Info grup diperbarui!'); window.location.href='detail_grup.php?id=$idgrup';</script>";
}

// --- LOGIC: EVENT ---
$eventEdit = null;
if ($canManageEvent && isset($_GET['edit_event'])) {
    $eventEdit = $eventObj->getEventById($_GET['edit_event']);
}

if ($canManageEvent && isset($_POST['btnSimpanEvent'])) {
    if (!empty($_POST['idevent_edit'])) {
        $eventObj->updateEvent($_POST['idevent_edit'], $_POST['judul'], $_POST['tanggal'], $_POST['keterangan']);
        echo "<script>alert('Event berhasil diperbarui!'); window.location.href='detail_grup.php?id=$idgrup';</script>";
    } else {
        $eventObj->addEvent($idgrup, $_POST['judul'], $_POST['tanggal'], $_POST['keterangan']);
        header("Location: detail_grup.php?id=$idgrup");
    }
}

if ($canManageEvent && isset($_GET['hapus_event'])) {
    $eventObj->deleteEvent($_GET['hapus_event']);
    header("Location: detail_grup.php?id=$idgrup");
}

// --- LOGIC: MEMBER ---
if (isset($_GET['action']) && $_GET['action'] == 'leave' && !$isPembuat) {
    $grupObj->removeMember($idgrup, $username);
    echo "<script>alert('Anda keluar dari grup.'); window.location.href='index.php';</script>";
}

if ($isPembuat && isset($_GET['kick_user'])) {
    $status = $grupObj->removeMember($idgrup, $_GET['kick_user']);
    if ($status == "SUCCESS") {
        echo "<script>alert('Member berhasil dikeluarkan.'); window.location.href='detail_grup.php?id=$idgrup';</script>";
    } else if ($status == "CANNOT_KICK_OWNER") {
        echo "<script>alert('GAGAL: Anda tidak bisa mengeluarkan diri sendiri (Pemilik Grup).'); window.location.href='detail_grup.php?id=$idgrup';</script>";
    } else {
        echo "<script>alert('Gagal menghapus member.'); window.location.href='detail_grup.php?id=$idgrup';</script>";
    }
}

// --- LOGIC: THREAD ---
if (isset($_POST['btnTambahThread'])) {
    $status = $_POST['status'];
    $threadObj->insertThreadByGroupId($username, $idgrup, $status);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail: <?= htmlspecialchars($grup['nama']) ?></title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background: #f0f2f5;
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* --- LAYOUT UTAMA: VERTIKAL STACK (ATAS-BAWAH) --- */
        .row {
            display: flex;
            flex-direction: column; /* Mengunci layout menjadi atas-bawah selamanya */
            gap: 20px;
            margin-top: 20px;
        }

        .col {
            width: 100%; /* Memastikan kolom mengambil lebar penuh */
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        /* BUTTONS */
        .btn {
            padding: 8px 12px;
            border-radius: 4px;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .btn-back { background: #7f8c8d; }
        .btn-save { background: #2ecc71; }
        .btn-kick { background: #e74c3c; font-size: 0.8em; }
        .btn-leave { background: #c0392b; float: right; }
        .btn-kelola { background:#f39c12; }

        /* FORMS */
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* TABLES - RESPONSIVE SCROLL */
        .table-responsive {
            width: 100%;
            overflow-x: auto; /* Scroll horizontal jika tabel kepanjangan di HP */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            /* Penting: mencegah tabel hancur di layar kecil */
            min-width: 600px; /* Minimal lebar tabel agar kolom tidak gepeng */
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #3498db;
            color: white;
        }

        /* HEADER GRUP */
        .edit-box {
            background: #fff8e1;
            padding: 15px;
            border: 1px solid #ffe082;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .form-header-grup {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        
        .form-header-grup > div {
            flex: 1;
            min-width: 200px;
        }

        /* SEARCH CONTAINER */
        .search-container {
            position: relative;
            background: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #b3e5fc;
        }

        #search-result {
            display: none;
            position: absolute;
            z-index: 1000;
            width: calc(100% - 30px);
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 200px;
            overflow-y: auto;
            border-radius: 0 0 5px 5px;
        }

        .search-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .search-item:hover { background-color: #f5f5f5; }

        .tambah-percakapan {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        /* MODAL */
        .modal-bg {
            display:none; 
            position:fixed; top:0; left:0; 
            width:100%; height:100%; 
            background:rgba(0,0,0,0.5); 
            z-index:999;
        }
        
        .modal-content {
            background:white; 
            width:90%; max-width:400px; 
            margin:100px auto; padding:20px; 
            border-radius:8px; 
            box-shadow:0 4px 10px rgba(0,0,0,0.2); 
            position:relative;
        }

        /* --- MEDIA QUERY SMARTPHONE --- */
        @media screen and (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 15px; }

            /* Tombol Header */
            .btn-leave {
                float: none;
                display: block;
                width: 100%;
                text-align: center;
                margin-top: 10px;
            }
            .btn-back {
                display: block;
                width: 100%;
                text-align: center;
            }

            /* Form Header menjadi stack penuh */
            .form-header-grup {
                flex-direction: column;
                align-items: stretch;
            }
            .form-header-grup button {
                width: 100%;
                margin-top: 5px;
            }
            
            #search-result {
                position: relative;
                width: 100%;
                box-shadow: none;
                border-top: 1px solid #ddd;
            }
        }
    </style>
    <script src="js/jquery-3.7.1.js"></script>
</head>

<body>

    <div class="container">
        <div style="margin-bottom:15px; overflow:hidden;">
            <a href="index.php" class="btn btn-back">Kembali</a>
            <?php if (!$isPembuat): ?>
                <a href="?id=<?= $idgrup ?>&action=leave" class="btn btn-leave" onclick="return confirm('Keluar grup?')">Keluar Grup</a>
            <?php endif; ?>
        </div>

        <?php if ($isPembuat): ?>
            <div class="edit-box">
                <h3>Pengaturan Grup</h3>
                <form method="POST" class="form-header-grup">
                    <div>
                        <label>Nama Grup</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($grup['nama']) ?>" required>
                    </div>
                    <div>
                        <label>Jenis</label>
                        <select name="jenis">
                            <option value="Publik" <?= $grup['jenis'] == 'Publik' ? 'selected' : '' ?>>Publik</option>
                            <option value="Privat" <?= $grup['jenis'] == 'Privat' ? 'selected' : '' ?>>Privat</option>
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
                    <div style="background: #fff; padding: 15px; border: 1px solid #ddd; margin-bottom: 15px; border-radius: 5px;">
                        <h4 style="margin-top:0;"><?= $eventEdit ? "Edit Event" : "Tambah Event Baru" ?></h4>
                        <form method="POST">
                            <input type="hidden" name="idevent_edit" value="<?= $eventEdit ? $eventEdit['idevent'] : '' ?>">

                            <input type="text" name="judul" placeholder="Judul Event" required
                                value="<?= $eventEdit ? htmlspecialchars($eventEdit['judul']) : '' ?>">

                            <input type="datetime-local" name="tanggal" required
                                value="<?= $eventEdit ? date('Y-m-d\TH:i', strtotime($eventEdit['tanggal'])) : '' ?>">

                            <textarea name="keterangan" placeholder="Keterangan" rows="2"><?= $eventEdit ? htmlspecialchars($eventEdit['keterangan']) : '' ?></textarea>

                            <div style="display:flex; gap:5px;">
                                <?php if ($eventEdit): ?>
                                    <a href="detail_grup.php?id=<?= $idgrup ?>" class="btn" style="background:#95a5a6; text-align:center;">Batal</a>
                                <?php endif; ?>
                                <button type="submit" name="btnSimpanEvent" class="btn btn-save" style="flex:1;">
                                    <?= $eventEdit ? "Simpan Perubahan" : "Tambahkan Event" ?>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th width="25%">Tanggal</th>
                                <th>Event</th>
                                <?php if ($canManageEvent) echo "<th width='20%'>Aksi</th>"; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $resEvent = $eventObj->getEventsByGrup($idgrup);
                            if ($resEvent->num_rows > 0) {
                                while ($e = $resEvent->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . date('Y-m-d H:i', strtotime($e['tanggal'])) . "</td>";
                                    echo "<td style='white-space:normal;'><b>" . htmlspecialchars($e['judul']) . "</b><br><small>" . htmlspecialchars($e['keterangan']) . "</small></td>";

                                    if ($canManageEvent) {
                                        echo "<td>
                                        <a href='detail_grup.php?id=$idgrup&edit_event={$e['idevent']}' class='btn btn-kelola' style='padding:5px; font-size:12px;'>Edit</a>
                                        <a href='detail_grup.php?id=$idgrup&hapus_event={$e['idevent']}' class='btn btn-kick' style='padding:5px; font-size:12px;' onclick='return confirm(\"Hapus event ini?\")'>Delete</a>
                                    </td>";
                                    }
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' style='text-align:center; color:gray;'>Belum ada agenda.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col">
                <h3>Anggota Grup</h3>

                <?php if ($isPembuat): ?>
                    <div class="search-container">
                        <label style="display:block; margin-bottom:5px; font-weight:bold; color:#2c3e50;">
                            Tambah Mahasiswa
                        </label>

                        <input type="text" id="keyword" placeholder="Ketik Nama atau NRP mahasiswa...">
                        <div id="search-result"></div>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>NRP</th>
                                <th>Nama</th>
                                <?php if ($isPembuat) echo "<th>Aksi</th>"; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $resM = $grupObj->getMembers($idgrup);
                            if ($resM->num_rows > 0) {
                                while ($m = $resM->fetch_assoc()) {
                                    $roleBadge = ($m['role'] == 'Dosen') ? " <span style='background:gold; padding:2px; font-size:10px;'>Dosen</span>" : "";

                                    echo "<tr>
                                        <td>{$m['id_nomor']}</td>
                                        <td>{$m['nama_lengkap']}$roleBadge</td>";

                                    if ($isPembuat) {
                                        echo "<td><a href='?id=$idgrup&kick_user={$m['username']}' class='btn btn-kick' onclick='return confirm(\"Keluarkan?\")'>Keluarkan</a></td>";
                                    }
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>Belum ada anggota.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col">
                <div class="tambah-percakapan">
                    <h3>Percakapan</h3>
                    <button class="btn btn-save" style="height: 35px;" onclick="openTambahThreadModal()">+ Tambah</button>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Pembuat Thread</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $resThread = $threadObj->getThreadByGroupId($idgrup);
                            if ($resThread->num_rows > 0) {
                                while ($t = $resThread->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $t['username_pembuat'] . "</td>";
                                    echo "<td>" . $t['status'] . "</td>";
                                    echo "<td><a href='chat.php?id=" . $t['idthread'] . "' class='btn btn-save'>Lihat</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' style='text-align:center; color:gray;'>Belum ada thread.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div> <div id="modalTambahThread" class="modal-bg">
            <div class="modal-content">

                <h3 style="margin-top:0;">Buat Thread Baru</h3>

                <form method="POST">
                    <div class="form-group" style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px;">Nama Pembuat</label>
                        <input type="text" value="<?= htmlspecialchars($_SESSION['user']) ?>" readonly style="background:#eee;">
                        <input type="hidden" name="username_pembuat" value="<?= htmlspecialchars($_SESSION['user']) ?>">
                    </div>

                    <div class="form-group" style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px;">ID Grup</label>
                        <input type="text" value="<?= $idgrup ?>" readonly style="background:#eee;">
                        <input type="hidden" name="idgrup" value="<?= $idgrup ?>">
                    </div>

                    <div class="form-group" style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px;">Status Thread</label>
                        <div style="display:flex; gap:25px;">
                            <label style="display: flex; align-items:center; gap:5px;">
                                <input type="radio" name="status" value="Open" required style="width:auto; margin:0;">
                                <span>Open</span>
                            </label>

                            <label style="display: flex; align-items:center; gap:5px;">
                                <input type="radio" name="status" value="Close" style="width:auto; margin:0;">
                                <span>Close</span>
                            </label>
                        </div>
                    </div>

                    <div style="display:flex; gap:10px; margin-top:20px;">
                        <button type="button" onclick="closeTambahThreadModal()" class="btn" style="background:#95a5a6; flex:1;">Batal</button>
                        <button type="submit" name="btnTambahThread" class="btn btn-save" style="flex:1;">Buat Thread</button>
                    </div>
                </form>
                <button onclick="closeTambahThreadModal()" style="position:absolute; top:10px; right:10px; border:none; background:none; font-size:18px; cursor:pointer;">&times;</button>
            </div>
        </div>

    </div> <script>
        function openTambahThreadModal() {
            $('#modalTambahThread').fadeIn(200);
        }

        function closeTambahThreadModal() {
            $('#modalTambahThread').fadeOut(200);
        }

        $(document).ready(function() {
            // Live Search Logic
            $('#keyword').keyup(function() {
                let key = $(this).val();
                let idgrup = <?= $idgrup ?>;
                if (key.length > 2) {
                    $.ajax({
                        url: 'process/ajax_search.php',
                        method: 'POST',
                        data: { keyword: key, idgrup: idgrup },
                        success: function(data) {
                            $('#search-result').html(data).slideDown(200);
                        }
                    });
                } else {
                    $('#search-result').slideUp(200);
                }
            });

            // Add Member Logic
            $(document).on('click', '.add-btn-ajax', function() {
                let username_mhs = $(this).data('user');
                let idgrup = <?= $idgrup ?>;
                let tombol = $(this);
                tombol.text('...').prop('disabled', true);
                $.post('process/ajax_add_member.php', { username: username_mhs, idgrup: idgrup }, function(response) {
                    if (response.trim() == 'ok') {
                        alert('Berhasil menambahkan mahasiswa!');
                        location.reload();
                    } else {
                        alert('Gagal menambahkan member.');
                        tombol.text('+').prop('disabled', false);
                    }
                });
            });

            $(window).on('click', function(e) {
                if ($(e.target).is('#modalTambahThread')) {
                    closeTambahThreadModal();
                }
            });
        });
    </script>
</body>
</html>