<?php
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabel Mahasiswa</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px; /* Ubah nilai ini */
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
        .table th, .table td {
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
        <h1>Tabel Mahasiswa</h1>
        <a href="adminhome.php" class="btn-back">Kembali</a>
        <a href="tambahmahasiswa.php" class="btn-add">Tambah Mahasiswa Baru</a>
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
                    $stmt = $mysqli->prepare("SELECT * FROM mahasiswa");
                    
                    if ($stmt) {
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $nrp = $row['nrp'];
                                $nama = $row['nama'];
                                $gender = $row['gender'];
                                $tgllahir = $row['tanggal_lahir'];
                                $angkatan = $row['angkatan'];
                                $foto_ext = $row['foto_extention'];

                                echo "<tr>";
                                echo "<td>";
                                $foto_path = "images/" . $nrp . "." . $foto_ext;
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
                                echo "<a href='editmahasiswa.php?nrp=" . htmlspecialchars($nrp) . "' class='aksi-btn edit-btn'>Edit</a> | ";
                                echo "<a href='hapusmahasiswa.php?nrp=" . htmlspecialchars($nrp) . "' class='aksi-btn delete-btn' onclick=\"return confirm('Apakah Anda yakin ingin menghapus data ini?');\">Hapus</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Tidak ada data mahasiswa.</td></tr>";
                        }
                        $stmt->close();
                    } else {
                        echo "<tr><td colspan='4'>Error: " . $mysqli->error . "</td></tr>";
                    }

                    $mysqli->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>