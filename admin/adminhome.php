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
    <title>Home Admin</title>
    <style>
        :root {
            --bg-body: #f0f2f5;
            --bg-container: #fff;
            --text-main: #2c3e50;
            --text-desc: #7f8c8d;
            --shadow: rgba(0, 0, 0, 0.1);
        }

        body.dark-mode {
            --bg-body: #18191a;
            --bg-container: #242526;
            --text-main: #e4e6eb;
            --text-desc: #b0b3b8;
            --shadow: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: sans-serif;
            background-color: var(--bg-body);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #333;
            transition: background 0.3s;
        }
        .container {
            text-align: center;
            background-color: var(--bg-container);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px var(--shadow);
            max-width: 500px;
            width: 90%;
            transition: transform 0.3s ease, background 0.3s;
        }
        .container:hover {
            transform: translateY(-5px);
        }
        h1 {
            color: var(--text-main);
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        p {
            color: var(--text-desc);
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .btn {
            padding: 15px 25px;
            font-size: 1.1rem;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            background-color: #3498db;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn:hover {
            background-color: #2980b9;
            transform: scale(1.02);
        }
        .btn.mahasiswa {
            background-color: #2ecc71;
        }
        .btn.mahasiswa:hover {
            background-color: #27ae60;
        }
        .btn.logout {
            background-color: #cf2424ff;
        }
        .btn.logout:hover {
            background-color: #b53e29ff;
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
        <h1>Selamat Datang, Admin!</h1>
        <p>Silakan pilih tabel yang ingin Anda kelola.</p>
        <div class="button-group">
            <a href="tabeldosen.php" class="btn">Kelola Tabel Dosen</a>
            <a href="tabelmahasiswa.php" class="btn mahasiswa">Kelola Tabel Mahasiswa</a>
            <a href="../process/proses_logout.php" class="btn logout">Logout</a>
        </div>
    </div>

    <script>
        const themeToggleBtn = document.getElementById('themeToggle');
        const body = document.body;
        const html = document.documentElement;

        // Apply theme from head script logic to body
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
            // Path=/ memastikan cookie terbaca di semua folder
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }
    </script>
</body>
</html>