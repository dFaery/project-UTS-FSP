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
        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
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
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #3498db;
            color: white;
            text-transform: uppercase;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
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
            border: 2px solid #ddd;
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
            border: 1px solid #ccc;
            border-radius: 5px;
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
            color: #2c3e50;
            border: 1px solid #ccc;
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
            color: #2c3e50;
            transition: color 0.3s ease;
        }

        .btn-next:hover, .btn-previous:hover {
            color: #3498db;
        }

        .btn-next-disabled, .btn-previous-disabled {
            color: #aaa;
            cursor: not-allowed;
        }

        /* --- RESPONSIVE MEDIA QUERY (SMARTPHONE) --- */
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

            .btn-add, .btn-back {
                width: 100%;
                margin-bottom: 5px;
            }

            /* TABLE RESPONSIVE (SCROLL) */
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap; 
            }

            .photo-thumbnail {
                width: 50px; 
                height: 50px;
            }

            .pagination {
                gap: 2px;
            }
            
            .btn-page, .btn-next, .btn-previous, .btn-next-disabled, .btn-previous-disabled {
                padding: 6px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
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

                // display data to table
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
                            echo "Foto tidak tersedia";
                        }
                        echo "</td>";

                        echo "<td>" . htmlspecialchars($npk) . "</td>";
                        echo "<td>" . htmlspecialchars($nama) . "</td>";
                        echo "<td>";
                        echo "<a href='editdosen.php?npk=" . htmlspecialchars($npk) . "' class='aksi-btn edit-btn'>Edit</a> "; // Hilangkan pipe | agar rapi
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

            // // paging first
            // echo "<a href='?start=0&cari=$cari' class='btn-first'>First </a>";

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

            // // paging last
            // $last_page = ($maks_page - 1) * $PER_PAGE;
            // echo "<a href='?start=$last_page&cari=$cari' class='btn-last'> Last</a>";
            ?>

        </div>
    </div>
    <script src="jquery-3.7.1.js"></script>
    <script>
        $(document).ready(function() {
            // Contoh disable tombol Previous
            $('.btn-previous-disabled').removeAttr('href');
            $('.btn-next-disabled').removeAttr('href');
        })
    </script>

</body>

</html>