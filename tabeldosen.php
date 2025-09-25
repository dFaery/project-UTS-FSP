<?php
require_once("class/dosen.php");

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

$dosen = new Dosen();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabel Dosen</title>
    <style>
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
        }

        .btn-back {
            display: inline-block;
            padding: 10px 15px;
            background-color: #7f8c8d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Tabel Dosen</h1>
        <a href="adminhome.php" class="btn-back">Kembali</a>
        <a href="tambahdosen.php" class="btn-add">Tambah Dosen Baru</a>
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
                $result = $dosen->getDosen();

                // display data to table
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $npk = $row['npk'];
                        $nama = $row['nama'];
                        $foto_ext = $row['foto_extension'];

                        echo "<tr>";
                        echo "<td>";
                        $foto_path = "images/" . $npk . "." . $foto_ext;
                        if (file_exists($foto_path) && !empty($foto_ext)) {
                            echo "<img src='" . htmlspecialchars($foto_path) . "' alt='Foto " . htmlspecialchars($nama) . "' class='photo-thumbnail'>";
                        } else {
                            echo "Foto tidak tersedia";
                        }
                        echo "</td>";

                        echo "<td>" . htmlspecialchars($npk) . "</td>";
                        echo "<td>" . htmlspecialchars($nama) . "</td>";
                        echo "<td>";
                        echo "<a href='editdosen.php?npk=" . htmlspecialchars($npk) . "' class='aksi-btn edit-btn'>Edit</a> | ";
                        echo "<a href='hapusdosen.php?npk=" . htmlspecialchars($npk) . "' class='aksi-btn delete-btn' onclick=\"return confirm('Apakah Anda yakin ingin menghapus data ini?');\">Hapus</a>";
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
    </div>
</body>

</html>