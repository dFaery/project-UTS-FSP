<?php
session_start();
if (isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
    $isadmin = $_SESSION['is_admin'];
    if ($isadmin != 1) {
        header("Location: ../login.php");
    }
} else {
    header("Location: ../login.php");
}

if (isset($_GET['mstatus'])) {
    if ($_GET['mstatus'] == 'success') echo "<script>alert('Berhasil menambahkan akun Mahasiswa');</script>";
    if ($_GET['mstatus'] == 'fail') echo "<script>alert('Gagal menambahkan akun Mahasiswa, NRP sudah terdaftar');</script>";
}

require_once("../class/mahasiswa.php");

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

$mahasiswa = new Mahasiswa();

$PER_PAGE = 5;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabel Mahasiswa</title>
    <style>
        :root {
            --bg-body: #f0f2f5;
            --bg-container: #fff;
            --text-main: #2c3e50;
            --text-secondary: #333;
            --input-bg: #fff;
            --input-text: #000;
            --border-color: #ddd;
            --shadow: rgba(0, 0, 0, 0.1);
            --table-head-text: #fff;
            --table-row-even: #f2f2f2;
            --table-row-hover: #e9ecef;
        }

        body.dark-mode {
            --bg-body: #18191a;
            --bg-container: #242526;
            --text-main: #e4e6eb;
            --text-secondary: #b0b3b8;
            --input-bg: #3a3b3c;
            --input-text: #e4e6eb;
            --border-color: #555;
            --shadow: rgba(255, 255, 255, 0.1);
            --table-head-text: #e4e6eb;
            --table-row-even: #2c2c2c;
            --table-row-hover: #3e4042;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background-color: var(--bg-body);
            margin: 0;
            padding: 20px;
            transition: background 0.3s;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: var(--bg-container);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px var(--shadow);
        }

        h1 {
            text-align: center;
            color: var(--text-main);
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-secondary);
        }

        .table th {
            background-color: #3498db;
            color: var(--table-head-text);
            text-transform: uppercase;
            white-space: nowrap;
        }

        .table tbody tr:nth-child(even) {
            background-color: var(--table-row-even);
        }

        .table tbody tr:hover {
            background-color: var(--table-row-hover);
        }

        .aksi-btn {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            font-size: 14px;
            display: inline-block;
            margin: 2px 0;
        }

        .edit-btn {
            background-color: #f39c12;
        }

        .delete-btn {
            background-color: #e74c3c;
        }

        .photo-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid var(--border-color);
        }

        .btn-add {
            display: inline-block;
            padding: 10px 15px;
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .btn-back {
            display: inline-block;
            padding: 10px 15px;
            background-color: #7f8c8d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-group {
            display: flex;
            gap: 10px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background-color: var(--input-bg);
            color: var(--input-text);
        }

        .form-group button {
            padding: 10px 15px;
            width: fit-content;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 5px;
        }

        .btn-page {
            text-decoration: none;
            height: fit-content;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 12px;
            transition: background-color 0.3s ease;
        }

        .btn-page:hover {
            background-color: #3498db;
            border-color: #3498db;
            color: white;
        }

        .btn-next,
        .btn-previous,
        .btn-next-disabled,
        .btn-previous-disabled {
            text-decoration: none;
            padding: 8px 12px;
            font-weight: 500;
            border-radius: 4px;
        }

        .btn-next,
        .btn-previous {
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        .btn-next:hover,
        .btn-previous:hover {
            color: #3498db;
        }

        .btn-next-disabled,
        .btn-previous-disabled {
            color: #aaa;
            cursor: not-allowed;
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

        @media screen and (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
                width: 100%;
            }

            .top-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .form-group {
                width: 100%;
            }

            .btn-group {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .btn-add,
            .btn-back {
                width: 100%;
                margin-bottom: 5px;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .photo-thumbnail {
                width: 50px;
                height: 50px;
            }

            .btn-page,
            .btn-next,
            .btn-previous,
            .btn-next-disabled,
            .btn-previous-disabled {
                padding: 6px 10px;
                font-size: 14px;
            }
        }
    </style>
    <script src="../js/jquery-3.7.1.js"></script>
</head>

<body>
    <button class="theme-toggle-btn" id="themeToggle" title="Ganti Tema">🌓</button>

    <div class="container">
        <h1>Tabel Mahasiswa</h1>

        <div class="top-bar">
            <form action="" method="get" style="flex: 1;">
                <div class="form-group">
                    <input type="text" name="cari" id="" placeholder="Cari NRP atau Nama" value="<?php echo isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
                    <button type="submit">Cari</button>
                </div>
            </form>
            <div class="btn-group">
                <a href="tambahmahasiswa.php" class="btn-add">Tambah Mahasiswa</a>
                <a href="adminhome.php" class="btn-back">Kembali</a>
            </div>
        </div>

        <?php
        $cari = isset($_GET['cari']) ? $_GET['cari'] : "";
        $cari_persen = "%" . $cari . "%";
        ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>NRP</th>
                    <th>Nama</th>
                    <th>Gender</th>
                    <th>Tanggal Lahir</th>
                    <th>Angkatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $offset = isset($_GET["start"]) ? (int)$_GET["start"] : 0;
                $result = $mahasiswa->getMahasiswa($cari_persen, null, $offset, $PER_PAGE);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $nrp = $row['nrp'];
                        $nama = $row['nama'];
                        $gender = $row['gender'];
                        $tgllahir = $row['tanggal_lahir'];
                        $angkatan = $row['angkatan'];
                        $foto_ext = $row['foto_extention'];

                        echo "<tr>";
                        echo "<td>";
                        $foto_path = "../images/" . $nrp . "." . $foto_ext;
                        if (file_exists($foto_path) && !empty($foto_ext)) {
                            echo "<img src='" . htmlspecialchars($foto_path) . "' alt='Foto " . htmlspecialchars($nama) . "' class='photo-thumbnail'>";
                        } else {
                            echo "Foto tidak tersedia";
                        }
                        echo "</td>";

                        echo "<td>" . htmlspecialchars($nrp) . "</td>";
                        echo "<td>" . htmlspecialchars($nama) . "</td>";
                        echo "<td>" . htmlspecialchars($gender) . "</td>";
                        echo "<td>" . htmlspecialchars($tgllahir) . "</td>";
                        echo "<td>" . htmlspecialchars($angkatan) . "</td>";
                        echo "<td>";
                        echo "<a href='editmahasiswa.php?nrp=" . htmlspecialchars($nrp) . "' class='aksi-btn edit-btn'>Edit</a> ";
                        echo "<a href='../process/proses_hapus_mahasiswa.php?nrp=" . htmlspecialchars($nrp) . "' class='aksi-btn delete-btn' onclick=\"return confirm('Apakah Anda yakin ingin menghapus data ini?');\">Hapus</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data mahasiswa.</td></tr>";
                }

                $mysqli->close();
                ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php
            $res = $mahasiswa->getMahasiswa($cari_persen);

            if ($offset > 0) {
                $prev = $offset - $PER_PAGE;
                echo "<a href='?start=$prev&cari=$cari' class='btn-previous'>Previous </a>";
            } else {
                $prev = 0;
                echo "<a href='?start=$prev&cari=$cari' class='btn-previous-disabled'>Previous </a>";
            }

            $total_data = $res->num_rows;
            $maks_page = ceil($total_data / $PER_PAGE);
            for ($page = 1; $page <= $maks_page; $page++) {
                $offs = ($page - 1) * $PER_PAGE;
                echo "<a href='?start=$offs&cari=$cari' class='btn-page'>$page</a>";
            }

            if ($offset + $PER_PAGE < $total_data) {
                $next = $offset + $PER_PAGE;
                echo "<a href='?start=$next&cari=$cari' class='btn-next'>Next</a>";
            } else {
                $next = $offset;
                echo "<a href='?start=$next&cari=$cari' class='btn-next-disabled'>Next</a>";
            }
            ?>
        </div>
    </div>

    <script>
        (function() {
            const savedTheme = document.cookie.split('; ').find(row => row.startsWith('theme='));
            if (savedTheme && savedTheme.split('=')[1] === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();

        $(document).ready(function() {
            $('.btn-previous-disabled').removeAttr('href');
            $('.btn-next-disabled').removeAttr('href');

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