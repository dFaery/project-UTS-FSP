<?php
session_start();
require_once("class/Grup.php");
require_once("class/Event.php");
require_once("class/Thread.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("ID Grup tidak ditemukan.");
}

$idgrup = $_GET['id'];
$username = $_SESSION['user'];

$grupObj = new Grup();
$eventObj = new Event();
$threadObj = new Thread();

$grup = $grupObj->getGrupById($idgrup);
if (!$grup) die("Grup tidak ditemukan.");

$isPembuat = ($grup['username_pembuat'] == $username);
$isMember = $grupObj->isMember($idgrup, $username);
$isDosen = $grupObj->isDosen($username);

if (!$isPembuat && !$isMember) {
    echo "<script>alert('Akses Ditolak: Anda bukan anggota grup ini atau telah dikeluarkan.'); window.location.href='index.php';</script>";
    exit();
}

$canManageEvent = ($isPembuat || ($isMember && $isDosen));

if ($isPembuat && isset($_POST['btnUpdateGrup'])) {
    $grupObj->updateGrup($idgrup, $_POST['nama'], $_POST['deskripsi'], $_POST['jenis']);
    echo "<script>alert('Info grup diperbarui!'); window.location.href='detail_grup.php?id=$idgrup';</script>";
}

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
        /* --- THEME VARIABLES --- */
        :root {
            --bg-body: #f0f2f5;
            --bg-container: #fff;
            --text-main: #2c3e50;
            --text-secondary: #333;
            --col-bg: #f9f9f9;
            --col-border: #eee;
            --input-bg: #fff;
            --input-text: #000;
            --table-border: #ddd;
            --table-head-text: #fff;
            --shadow: rgba(0, 0, 0, 0.1);
            --modal-bg: #fff;
            --yellow-box: #fff8e1;
            --yellow-border: #ffe082;
        }

        body.dark-mode {
            --bg-body: #18191a;
            --bg-container: #242526;
            --text-main: #e4e6eb;
            --text-secondary: #b0b3b8;
            --col-bg: #3a3b3c;
            --col-border: #555;
            --input-bg: #4e4f50;
            --input-text: #e4e6eb;
            --table-border: #555;
            --table-head-text: #e4e6eb;
            --shadow: rgba(255, 255, 255, 0.1);
            --modal-bg: #242526;
            --yellow-box: #4a4218;
            --yellow-border: #665c26;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background: var(--bg-body);
            color: var(--text-secondary);
            padding: 20px;
            margin: 0;
            transition: background 0.3s, color 0.3s;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: var(--bg-container);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px var(--shadow);
            transition: background 0.3s;
        }

        h1, h3, h4, p {
            color: var(--text-main);
        }

        /* --- LAYOUT UTAMA: VERTIKAL STACK --- */
        .row {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
        }

        .col {
            width: 100%;
            padding: 15px;
            background: var(--col-bg);
            border-radius: 8px;
            border: 1px solid var(--col-border);
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
            border: 1px solid var(--table-border);
            border-radius: 4px;
            background-color: var(--input-bg);
            color: var(--input-text);
        }

        /* TABLES */
        .table-responsive {
            width: 100%;
            overflow-x: auto; 
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-container);
            min-width: 600px;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid var(--table-border);
            text-align: left;
            color: var(--text-secondary);
        }

        th {
            background: #3498db;
            color: var(--table-head-text);
        }

        /* HEADER GRUP */
        .edit-box {
            background: var(--yellow-box);
            padding: 15px;
            border: 1px solid var(--yellow-border);
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
            background: var(--col-bg); /* Mengikuti tema kolom */
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid var(--table-border);
        }

        #search-result {
            display: none;
            position: absolute;
            z-index: 1000;
            width: calc(100% - 30px);
            background: var(--bg-container);
            border: 1px solid var(--table-border);
            border-top: none;
            box-shadow: 0 4px 6px var(--shadow);
            max-height: 200px;
            overflow-y: auto;
            border-radius: 0 0 5px 5px;
        }

        .search-item {
            padding: 10px;
            border-bottom: 1px solid var(--table-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-secondary);
        }
        .search-item:hover { background-color: var(--col-bg); }

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
            background: var(--modal-bg);
            width:90%; max-width:400px; 
            margin:100px auto; padding:20px; 
            border-radius:8px; 
            box-shadow:0 4px 10px rgba(0,0,0,0.2); 
            position:relative;
            color: var(--text-main);
        }

        /* Overrides inside modal for inputs */
        .modal-content input[readonly] {
            background-color: var(--col-bg) !important;
        }

        /* Toggle Button */
        .theme-toggle-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--text-main);
            color: var(--bg-container);
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: transform 0.2s;
        }
        .theme-toggle-btn:hover { transform: scale(1.1); }

        /* --- MEDIA QUERY SMARTPHONE --- */
        @media screen and (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 15px; }

            .btn-leave, .btn-back {
                float: none;
                display: block;
                width: 100%;
                text-align: center;
                margin-top: 10px;
            }
            .btn-back { margin-top: 0; }

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
                border-top: 1px solid var(--table-border);
            }
        }
    </style>
    <script src="js/jquery-3.7.1.js"></script>
</head>

<body>
    <button class="theme-toggle-btn" id="themeToggle" title="Ganti Tema">🌓</button>

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
                <p style="margin:5px 0 0 0;">Kode Join: <strong class="badge-code" style="color:var(--text-main); background:var(--input-bg); padding:3px;"><?= $grup['kode_pendaftaran'] ?></strong></p>
            </div>
        <?php else: ?>
            <h1><?= htmlspecialchars($grup['nama']) ?></h1>
            <p>Pengampu: <b><?= $grup['username_pembuat'] ?></b> | <?= htmlspecialchars($grup['deskripsi']) ?></p>
        <?php endif; ?>

        <div class="row">
            
            <div class="col">
                <h3>Agenda / Event</h3>

                <?php if ($canManageEvent): ?>
                    <div style="background: var(--bg-container); padding: 15px; border: 1px solid var(--table-border); margin-bottom: 15px; border-radius: 5px;">
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
                        <label style="display:block; margin-bottom:5px; font-weight:bold; color:var(--text-secondary);">
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
                                    $roleBadge = ($m['role'] == 'Dosen') ? " <span style='background:gold; padding:2px; font-size:10px; color:#000;'>Dosen</span>" : "";

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

        </div> 
        
        <div id="modalTambahThread" class="modal-bg">
            <div class="modal-content">

                <h3 style="margin-top:0;">Buat Thread Baru</h3>

                <form method="POST">
                    <div class="form-group" style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px;">Nama Pembuat</label>
                        <input type="text" value="<?= htmlspecialchars($_SESSION['user']) ?>" readonly>
                        <input type="hidden" name="username_pembuat" value="<?= htmlspecialchars($_SESSION['user']) ?>">
                    </div>

                    <div class="form-group" style="margin-bottom:15px;">
                        <label style="display:block; margin-bottom:5px;">ID Grup</label>
                        <input type="text" value="<?= $idgrup ?>" readonly>
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
                <button onclick="closeTambahThreadModal()" style="position:absolute; top:10px; right:10px; border:none; background:none; font-size:18px; cursor:pointer; color:var(--text-main);">&times;</button>
            </div>
        </div>

    </div> 
    
    <script>
        function openTambahThreadModal() {
            $('#modalTambahThread').fadeIn(200);
        }

        function closeTambahThreadModal() {
            $('#modalTambahThread').fadeOut(200);
        }

        (function() {
            const savedTheme = document.cookie.split('; ').find(row => row.startsWith('theme='));
            if (savedTheme && savedTheme.split('=')[1] === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();

        $(document).ready(function() {
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

            const themeToggleBtn = document.getElementById('themeToggle');
            const body = document.body;
            const html = document.documentElement;

            if (html.classList.contains('dark-mode')) {
                body.classList.add('dark-mode');
                html.classList.remove('dark-mode');
                themeToggleBtn.textContent = '☀️';
            } else {
                themeToggleBtn.textContent = '🌙';
            }

            themeToggleBtn.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                if (body.classList.contains('dark-mode')) {
                    setCookie('theme', 'dark', 365);
                    themeToggleBtn.textContent = '☀️';
                } else {
                    setCookie('theme', 'light', 365);
                    themeToggleBtn.textContent = '🌙';
                }
            });

            function setCookie(name, value, days) {
                var expires = "";
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }
        });
    </script>
</body>
</html>