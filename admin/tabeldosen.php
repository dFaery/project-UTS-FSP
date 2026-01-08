<?php
session_start();
if(isset($_SESSION['user'])){
    $username = $_SESSION['user'];
    $isadmin = $_SESSION['is_admin'];
    if($isadmin != 1){
        header("Location: ../login.php");
    }
}
else{
    header("Location: ../login.php");
}
require_once("../class/dosen.php");

if(isset($_GET['dstatus'])){
    if($_GET['dstatus'] == 'success') echo "<script>alert('Berhasil menambahkan akun Dosen');</script>";
    if($_GET['dstatus'] == 'fail') echo "<script>alert('Gagal menambahkan akun Dosen, NPK sudah terdaftar');</script>";
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

$dosen = new Dosen();

$PER_PAGE = 5;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabel Dosen</title>
    <style>
        /* --- THEME VARIABLES --- */
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

        /* Dark Mode Override */
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
            max-width: 900px;
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

        /* --- TABLE STYLES --- */
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
        }

        .table tbody tr:nth-child(even) {
            background-color: var(--table-row-even);
        }

        .table tbody tr:hover {
            background-color: var(--table-row-hover);
        }

        /* --- BUTTON STYLES --- */
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

        /* --- LAYOUT UTAMA --- */
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

        /* --- PAGINATION --- */
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

        .btn-next, .btn-previous {
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        .btn-next:hover, .btn-previous:hover {
            color: #3498db;
        }

        .btn-next-disabled, .btn-previous-disabled {
            color: #aaa;
            cursor: not-allowed;
        }

        /* Toggle Button Style */
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
        .theme-toggle-btn:hover {
            transform: scale(1.1);
        }

        /* --- RESPONSIVE MEDIA QUERY (SMARTPHONE) --- */
        @media screen and (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 15px; width: 100%; }
            .top-bar { flex-direction: column; align-items: stretch; }
            .form-group { width: 100%; }
            .btn-group { display: flex; flex-direction: column; gap: 5px; }
            .btn-add, .btn-back { width: 100%; margin-bottom: 5px; }

            table { 
                width: 100%;
                display: table; 
            }
            
            th, td {
                white-space: normal;
                font-size: 14px;
                padding: 8px 5px; 
            }
            
            .photo-thumbnail { width: 40px; height: 40px; } /* Perkecil foto di HP */
            .btn-page, .btn-next, .btn-previous, .btn-next-disabled, .btn-previous-disabled { padding: 6px 10px; font-size: 14px; }
        }
    </style>
    <script>
        (function() {
            const savedTheme = document.cookie.split('; ').find(row => row.startsWith('theme='));
            if (savedTheme && savedTheme.split('=')[1] === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>
</head>

<body>
    <button class="theme-toggle-btn" id="themeToggle" title="Ganti Tema">🌓</button>

    <div class="container">
        <h1>Tabel Dosen</h1>
        <div class="top-bar">
            <form action="" method="get" style="flex: 1;">
                <div class="form-group">
                    <input type="text" name="cari" id="" placeholder="Cari NPK atau Nama" value="<?php echo isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
                    <button type="submit">Cari</button>
                </div>
            </form>
            <div class="btn-group">
                <a href="tambahdosen.php" class="btn-add">Tambah Dosen Baru</a>
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
                    <th>NPK</th>
                    <th>Nama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $offset = isset($_GET["start"]) ? (int)$_GET["start"] : 0;
                $result = $dosen->getDosen($cari_persen, null, $offset, $PER_PAGE);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $npk = $row['npk'];
                        $nama = $row['nama'];
                        $foto_ext = $row['foto_extension'];

                        echo "<tr>";
                        echo "<td>";
                        $foto_path = "../images/" . $npk . '_' . $nama . '.' . $foto_ext;;
                        if (file_exists($foto_path) && !empty($foto_ext)) {
                            echo "<img src='" . htmlspecialchars($foto_path) . "' alt='Foto " . htmlspecialchars($nama) . "' class='photo-thumbnail'>";
                        } else {
                            echo "<span style='font-size:12px; color:gray;'>No IMG</span>";
                        }
                        echo "</td>";

                        echo "<td>" . htmlspecialchars($npk) . "</td>";
                        echo "<td>" . htmlspecialchars($nama) . "</td>";
                        echo "<td>";
                        echo "<a href='editdosen.php?npk=" . htmlspecialchars($npk) . "' class='aksi-btn edit-btn'>Edit</a> ";
                        echo "<a href='../process/proses_hapus_dosen.php?npk=" . htmlspecialchars($npk) . "' class='aksi-btn delete-btn' onclick=\"return confirm('Apakah Anda yakin ingin menghapus data ini?');\">Hapus</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Tidak ada data dosen.</td></tr>";
                }
                $mysqli->close();
                ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php
            $res = $dosen->getDosen($cari_persen);

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
    
    <script src="../js/jquery-3.7.1.js"></script>
    
    <script>
        $(document).ready(function() {
            // Contoh disable tombol Previous
            $('.btn-previous-disabled').removeAttr('href');
            $('.btn-next-disabled').removeAttr('href');

            // --- DARK MODE LOGIC ---
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