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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Dosen</title>
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
        
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold; 
            color: var(--text-label);
        }

        .form-group input { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid var(--border-color); 
            border-radius: 5px; 
            box-sizing: border-box; 
            background-color: var(--input-bg);
            color: var(--input-text);
        }

        .btn-group { display: flex; gap: 10px; }
        
        .btn { 
            padding: 10px 15px; 
            border: none; 
            border-radius: 5px; 
            text-decoration: none; 
            color: white; 
            cursor: pointer; 
            text-align: center; 
        }

        .btn-save { background-color: #2ecc71; flex-grow: 1; }
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
        <h1>Form Tambah Dosen</h1>
        <form action="tambahakun.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="npk">NPK</label>
                <input type="text" id="npk" name="npk" required maxlength="6">
            </div>
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="foto">Foto</label>
                <input type="file" id="foto" name="foto" accept="image/jpeg, image/png">
            </div>
            <div class="btn-group">
                <a href="tabeldosen.php" class="btn btn-back">Batal</a>
                <button type="submit" class="btn btn-save" name="submit-dosen">Simpan Data</button>
            </div>            
        </form>
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