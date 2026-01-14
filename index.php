<?php
session_start();
require_once("class/Grup.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: admin/adminhome.php");
    exit();
}

$username = $_SESSION['user'];
$grupObj = new Grup();
$isDosen = $grupObj->isDosen($username);
$pesan = "";

if ($isDosen && isset($_POST['btnSimpanGrup'])) {
    $kode_baru = $grupObj->createGrup($username, $_POST['nama_grup'], $_POST['deskripsi'], $_POST['jenis']);
    if ($kode_baru) $pesan = "<script>alert('Grup Dibuat! Kode: $kode_baru'); window.location.href='index.php';</script>";
}

if ($isDosen && isset($_GET['hapus_grup'])) {
    if ($grupObj->deleteGrup($_GET['hapus_grup'], $username)) $pesan = "<script>alert('Grup dihapus!'); window.location.href='index.php';</script>";
}

if (isset($_POST['btnJoin'])) {
    $status = $grupObj->joinGrup($username, $_POST['kode_join']);

    if ($status == "SUCCESS") {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        :root {
            --bg-body: #f0f2f5;
            --bg-container: #fff;
            --text-main: #2c3e50;
            --text-secondary: #333;
            --form-bg: #eef2f5;
            --form-border: #ddd;
            --input-bg: #fff;
            --input-text: #000;
            --table-hover: #f2f2f2;
            --shadow: rgba(0, 0, 0, 0.1);
            --yellow-box: #fff8e1;
            --modal-bg: #ffffff;
            --modal-text: #2c3e50;
            --modal-overlay: rgba(0, 0, 0, 0.5);

        }

        body.dark-mode {
            --bg-body: #18191a;
            --bg-container: #242526;
            --text-main: #e4e6eb;
            --text-secondary: #b0b3b8;
            --form-bg: #3a3b3c;
            --form-border: #555;
            --input-bg: #3a3b3c;
            --input-text: #e4e6eb;
            --table-hover: #3a3b3c;
            --shadow: rgba(255, 255, 255, 0.1);
            --yellow-box: #4a4218;
            --modal-bg: #242526;
            --modal-text: #e4e6eb;
            --modal-overlay: rgba(0, 0, 0, 0.7);

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
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px var(--shadow);
            transition: background 0.3s;
        }

        h1,
        h2,
        h3 {
            color: var(--text-main);
        }

        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }

        .header-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn-logout {
            background: #e74c3c;
        }

        .btn-change-password {
            background: #8e44ad;
        }

        .btn-save {
            background: #2ecc71;
            width: 100%;
            padding: 10px;
        }

        .btn-kelola {
            background: #3498db;
        }

        .btn-view {
            background: #f39c12;
        }

        .form-box {
            background: var(--form-bg);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid var(--form-border);
        }

        .form-group {
            margin-bottom: 10px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--form-border);
            border-radius: 4px;
            background-color: var(--input-bg);
            color: var(--input-text);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 30px;
            table-layout: fixed;
        }

        th {
            background: #3498db;
            color: white;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid var(--form-border);
            word-wrap: break-word;
            color: var(--text-secondary);
        }

        tr:hover {
            background-color: var(--table-hover);
        }

        .badge-code {
            background: var(--text-main);
            color: var(--bg-container);
            padding: 3px 6px;
            border-radius: 3px;
            font-family: monospace;
        }

        body.dark-mode .form-box[style*="background"] {
            background: var(--yellow-box) !important;
            border-color: #665c26;
        }

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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: transform 0.2s;
        }

        .theme-toggle-btn:hover {
            transform: scale(1.1);
        }

        .flex-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .flex-col {
            flex: 1;
            min-width: 300px;
        }
     
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: var(--modal-overlay);
            z-index: 999;
        }

        .modal-box {
            background: var(--modal-bg);
            color: var(--modal-text);
            width: 90%;
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px var(--shadow);
            position: relative;
            transition: background 0.3s, color 0.3s;
        }

        .modal-box h3,
        .modal-box p,
        .modal-box small {
            color: var(--modal-text);
        }


        @media screen and (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
                width: 100%;
            }

            .header-buttons {
                flex-direction: row;
                justify-content: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .btn-change-password,
            .btn-logout {
                width: auto;
            }

            .flex-row {
                flex-direction: column;
            }

            .flex-col {
                width: 100%;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
                table-layout: auto;
            }

            th,
            td {
                min-width: 120px;
            }

            .btn-kelola,
            .btn-logout,
            .btn-view,
            .btn-change-password {
                padding: 6px 10px;
                font-size: 14px;
                margin-bottom: 2px;
            }
        }
    </style>

    <script src="js/jquery-3.7.1.js"></script>
</head>

<body>
    <?= $pesan ?>

    <button class="theme-toggle-btn" id="themeToggle" title="Ganti Tema">🌓</button>

    <div class="container">
        <?php if ($isDosen): ?>
            <h1 style="clear:both;">Dashboard Dosen</h1>

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
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Grup</th>
                            <th width="15%">Kode</th>
                            <th width="35%">Deskripsi</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $grupObj->getGrupByDosen($username);
                        if ($res->num_rows > 0) {
                            $no = 1;
                            while ($r = $res->fetch_assoc()) {
                                $labelJenis = ($r['jenis'] == 'Privat') ? "<span style='background:red; color:white; padding:2px 4px; font-size:10px; border-radius:3px;'>PRIVAT</span>" : "";

                                echo "<tr>
                                <td>" . $no++ . "</td>
                                <td><b>" . htmlspecialchars($r['nama']) . "</b> $labelJenis</td>
                                <td><span class='badge-code'>" . $r['kode_pendaftaran'] . "</span></td>
                                <td>" . htmlspecialchars($r['deskripsi']) . "</td>
                                <td>
                                    <a href='detail_grup.php?id=" . $r['idgrup'] . "' class='btn btn-kelola'>Kelola</a>
                                    <a href='index.php?hapus_grup=" . $r['idgrup'] . "' class='btn btn-logout' style='float:none; padding:8px; background:#e74c3c;' onclick='return confirm(\"Hapus grup?\")'>Hapus</a>
                                </td>
                            </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Belum ada grup.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <hr style="border:0; border-top:1px dashed #ccc; margin:30px 0;">

            <h3>Grup yang Saya Ikuti (Tim Dosen)</h3>
            <div style="overflow-x:auto;">
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
            </div>

            <h3>Grup Publik (Dosen Lain)</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Grup</th>
                            <th width="20%">Dosen</th>
                            <th width="30%">Deskripsi</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resPub = $grupObj->getAvailablePublicGroups($username);
                        if ($resPub->num_rows > 0) {
                            $no = 1;
                            while ($r = $resPub->fetch_assoc()) {
                                echo "<tr>
                                <td>" . $no++ . "</td>
                                <td><b>" . htmlspecialchars($r['nama']) . "</b></td>
                                <td>" . htmlspecialchars($r['username_pembuat']) . "</td>
                                <td>" . htmlspecialchars($r['deskripsi']) . "</td>
                                <td>
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='kode_join' value='" . $r['kode_pendaftaran'] . "'>
                                        <button type='submit' name='btnJoin' class='btn btn-save' style='padding:5px 10px; width:auto;'>Gabung</button>
                                    </form>
                                </td>
                            </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Tidak ada grup publik baru.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <h1 style="clear:both;">Dashboard Mahasiswa</h1>

            <h3>Grup yang Saya Ikuti</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Grup</th>
                            <th width="20%">Dosen</th>
                            <th width="35%">Deskripsi</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $grupObj->getJoinedGroups($username);
                        if ($res->num_rows > 0) {
                            $no = 1;
                            while ($r = $res->fetch_assoc()) {
                                echo "<tr>
                                <td>" . $no++ . "</td>
                                <td><b>" . htmlspecialchars($r['nama']) . "</b></td>
                                <td>" . htmlspecialchars($r['username_pembuat']) . "</td>
                                <td>" . htmlspecialchars($r['deskripsi']) . "</td>
                                <td><a href='detail_grup.php?id=" . $r['idgrup'] . "' class='btn btn-view'>Lihat</a></td>
                            </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Belum join grup.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <h3>Jelajahi Grup Publik</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Grup</th>
                            <th width="20%">Dosen</th>
                            <th width="35%">Deskripsi</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resPub = $grupObj->getAvailablePublicGroups($username);
                        if ($resPub->num_rows > 0) {
                            $no = 1;
                            while ($r = $resPub->fetch_assoc()) {
                                echo "<tr>
                                <td>" . $no++ . "</td>
                                <td><b>" . htmlspecialchars($r['nama']) . "</b></td>
                                <td>" . htmlspecialchars($r['nama_dosen']) . "</td>
                                <td>" . htmlspecialchars($r['deskripsi']) . "</td>
                                <td>
                                    <button type='button' class='btn btn-save' 
                                            style='padding:5px 10px; width:auto; font-size:12px;'
                                            onclick='openJoinModal(\"" . htmlspecialchars($r['nama']) . "\", \"" . htmlspecialchars($r['nama_dosen']) . "\")'>
                                    Gabung
                                    </button>
                                </td>
                            </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada grup publik baru.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
        <div class="header-buttons">
            <a href="change_password.php" class="btn btn-change-password">Change Password</a>
            <a href="process/proses_logout.php" class="btn btn-logout">Logout</a>
        </div>
    </div>

    <div id="modalJoin" class="modal-overlay">
        <div class="modal-box">
            <h3 style="margin-top:0;">Masukkan Kode Grup</h3>
            <p>Anda akan bergabung ke grup: <br><b id="modalGrupName" style="color:#2c3e50;">-</b></p>
            <p><small>Dosen: <span id="modalDosenName">-</span></small></p>

            <form method="POST">
                <div class="form-group">
                    <input type="text" name="kode_join" id="inputKode" placeholder="Ketik Kode Pendaftaran..." required autocomplete="off" style="font-size:16px; text-transform:uppercase; text-align:center; letter-spacing:2px;">
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="button" onclick="closeJoinModal()" class="btn" style="background:#95a5a6; flex:1;">Batal</button>
                    <button type="submit" name="btnJoin" class="btn btn-save" style="flex:1;">Gabung Sekarang</button>
                </div>
            </form>

            <button onclick="closeJoinModal()" style="position:absolute; top:10px; right:10px; border:none; background:none; font-size:18px; cursor:pointer;">&times;</button>
        </div>
    </div>

    <script>
        function openJoinModal(namaGrup, namaDosen) {
            $('#modalGrupName').text(namaGrup);
            $('#modalDosenName').text(namaDosen);
            $('#inputKode').val('');
            $('#modalJoin').show();
            $('#inputKode').focus();
        }

        function closeJoinModal() {
            $('#modalJoin').hide();
        }

        $(window).on('click', function(event) {
            if ($(event.target).is('#modalJoin')) {
                $('#modalJoin').hide();
            }
        });

        (function() {
            const savedTheme = document.cookie.split('; ').find(row => row.startsWith('theme='));
            if (savedTheme) {
                const theme = savedTheme.split('=')[1];
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark-mode');
                }
            }
        })();

        $(document).ready(function() {
            const $themeBtn = $('#themeToggle');
            const $body = $('body');
            const $html = $('html');

            if ($html.hasClass('dark-mode')) {
                $body.addClass('dark-mode');
                $html.removeClass('dark-mode');
                $themeBtn.text('☀️');
            } else {
                $themeBtn.text('🌙');
            }

            $themeBtn.on('click', function() {
                $body.toggleClass('dark-mode');
                if ($body.hasClass('dark-mode')) {
                    setCookie('theme', 'dark', 365);
                    $(this).text('☀️');
                } else {
                    setCookie('theme', 'light', 365);
                    $(this).text('🌙');
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