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

require_once("../class/mahasiswa.php");
$mahasiswaObj = new Mahasiswa(); // Rename variable to avoid conflict with array variable below

if (!isset($_GET['nrp'])) {
    die("NRP tidak ditemukan.");
}
$nrp_to_edit = $_GET['nrp'];

$result = $mahasiswaObj->getMahasiswa($nrp_to_edit);

if ($result->num_rows === 0) {
    die("Data mahasiswa tidak ditemukan.");
}
$mahasiswa = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <style>
        :root {
            --bg-body: #f0f2f5;
            --bg-container: #fff;
            --text-main: #2c3e50;
            --text-label: #000;
            --input-bg: #fff;
            --input-text: #000;
            --border-color: #ccc;
            --shadow: rgba(0,0,0,0.1);
        }

        body.dark-mode {
            --bg-body: #18191a;
            --bg-container: #242526;
            --text-main: #e4e6eb;
            --text-label: #b0b3b8;
            --input-bg: #3a3b3c;
            --input-text: #e4e6eb;
            --border-color: #555;
            --shadow: rgba(255,255,255,0.1);
        }

        body { 
            font-family: sans-serif; 
            background-color: var(--bg-body); 
            padding: 20px; 
            transition: background 0.3s;
            color: var(--text-label);
        }

        .container { 
            max-width: 500px; 
            margin: auto; 
            background-color: var(--bg-container); 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px var(--shadow); 
        }

        h1 { 
            text-align: center; 
            color: var(--text-main); 
        }

        .form-group { margin-bottom: 20px; }
        
        label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold; 
            color: var(--text-label);
        }

        input, select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid var(--border-color); 
            border-radius: 5px; 
            box-sizing: border-box; 
            background-color: var(--input-bg);
            color: var(--input-text);
        }

        .current-photo { 
            max-width: 100px; 
            border-radius: 5px; 
            margin-top: 10px; 
            border: 2px solid var(--border-color);
        }

        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        
        .btn { 
            padding: 10px 15px; 
            border: none; 
            border-radius: 5px; 
            text-decoration: none; 
            color: white; 
            cursor: pointer; 
            text-align: center; 
        }

        .btn-save { background-color: #f39c12; flex-grow: 1; }
        .btn-back { background-color: #7f8c8d; }

        .theme-toggle-btn {
            position: fixed; bottom: 20px; right: 20px;
            width: 50px; height: 50px; border-radius: 50%;
            background-color: var(--text-main); color: var(--bg-container);
            border: none; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            font-size: 24px; display: flex; align-items: center; justify-content: center;
            z-index: 1000; transition: transform 0.2s;
        }
        .theme-toggle-btn:hover { transform: scale(1.1); }
    </style>
    <script src="../js/jquery-3.7.1.js"></script>
</head>
<body>
    <button class="theme-toggle-btn" id="themeToggle" title="Ganti Tema">🌓</button>

    <div class="container">
        <h1>Form Edit Mahasiswa</h1>
        <form action="../process/proses_edit_mahasiswa.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="nrp_asli" value="<?php echo htmlspecialchars($mahasiswa['nrp']); ?>">
            
            <div class="form-group">
                <label for="nrp">NRP (Tidak dapat diubah)</label>
                <input type="text" id="nrp" name="nrp" value="<?php echo htmlspecialchars($mahasiswa['nrp']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($mahasiswa['nama']); ?>" required>
            </div>
             <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="Pria" <?php if ($mahasiswa['gender'] == 'Pria') echo 'selected'; ?>>Pria</option>
                    <option value="Wanita" <?php if ($mahasiswa['gender'] == 'Wanita') echo 'selected'; ?>>Wanita</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($mahasiswa['tanggal_lahir']); ?>" required>
            </div>
            <div class="form-group">
                <label for="angkatan">Angkatan</label>
                <input type="number" id="angkatan" name="angkatan" value="<?php echo htmlspecialchars($mahasiswa['angkatan']); ?>" required min="1900" max="2099" step="1">
            </div>
            <div class="form-group">
                <label for="foto">Ganti Foto (Kosongkan jika tidak ingin ganti)</label>                
                <?php
                if (!empty($mahasiswa['foto_extention'])) {
                    $foto_path = "../images/" . $mahasiswa['nrp'] . "." . $mahasiswa['foto_extention'];                    
                    echo "<img src='" . $foto_path . "' alt='Foto saat ini' class='current-photo'>";                                
                }
                ?>
                <input type="file" id="foto" name="foto" accept="image/jpeg, image/png">
            </div>
            <div class="btn-group">
                <a href="tabelmahasiswa.php" class="btn btn-back">Batal</a>
                <button type="submit" class="btn btn-save">Update Data</button>
            </div>
        </form>
    </div>

    <script>
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