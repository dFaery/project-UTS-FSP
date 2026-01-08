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
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1, h2, h3 {
            color: #2c3e50;
        }

        /* --- BUTTON STYLES --- */
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

        .btn-logout {
            background: #e74c3c;
        }

        .btn-change-password {
            background: #3498db;
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

        /* --- FORM STYLES --- */
        .form-box {
            background: #eef2f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .form-group {
            margin-bottom: 10px;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* --- TABLE STYLES --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 30px;
            table-layout: fixed; /* Fixed layout agar rapi di desktop */
        }

        th {
            background: #3498db;
            color: white;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            word-wrap: break-word;
        }

        .badge-code {
            background: #2c3e50;
            color: #fff;
            padding: 3px 6px;
            border-radius: 3px;
            font-family: monospace;
        }

        /* --- LAYOUT UTAMA --- */
        .flex-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .flex-col {
            flex: 1;
            min-width: 300px;
        }

        /* --- RESPONSIVE CSS (MEDIA QUERY) --- */
        @media screen and (max-width: 768px) {
            
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
                width: 100%;
            }

            .flex-row {
                flex-direction: column;
            }

            .flex-col {
                width: 100%;
            }

            .btn-change-password, .btn-logout {
                float: none;
                display: block;
                width: fit-content; 
                margin: 0 auto 15px auto; 
                text-align: center;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
                table-layout: auto;
            }

            th, td {
                min-width: 120px; 
            }
            
            .btn-kelola, .btn-logout, .btn-view, .btn-change-password {
                padding: 6px 10px;
                font-size: 14px;
                margin-bottom: 2px;
            }
        }
    </style>
</head>

<body>
    <?= $pesan ?>
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
        <a href="change_password.php" class="btn btn-change-password">Change Password</a>
        <a href="process/proses_logout.php" class="btn btn-logout">Logout</a>
    </div>

    <div id="modalJoin" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
        <div style="background:white; width:90%; max-width:400px; margin:100px auto; padding:20px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.2); position:relative;">

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

    <script src="js/jquery-3.7.1.js"></script>
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
    </script>


</body>

</html>