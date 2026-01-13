<?php
session_start();
if(isset($_SESSION['user'])){
    $isadmin = $_SESSION['is_admin'];
    if($isadmin != 1){
        header("Location: index.php");
    } else {
        header("Location: adminhome.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        :root {
            --bg-body: #f0f2f5;
            --bg-container: #fff;
            --text-main: #2c3e50;
            --text-secondary: #333;
            --input-bg: #fff;
            --input-text: #000;
            --border-color: #ccc;
            --shadow: rgba(0, 0, 0, 0.1);
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
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: sans-serif;
            background-color: var(--bg-body);
            margin: 0;
            transition: background 0.3s;
        }

        .container {
            width: 400px;
            background-color: var(--bg-container);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px var(--shadow);
            transition: background 0.3s;
        }

        h1, h2 {
            text-align: center;
            color: var(--text-main);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--text-secondary);
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

        .btn-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            cursor: pointer;
            text-align: center;
        }

        .btn-save {
            background-color: #2ecc71;
            flex-grow: 1;
            width: 100%;
        }

        .btn-back {
            background-color: #7f8c8d;
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
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: transform 0.2s;
        }
        .theme-toggle-btn:hover { transform: scale(1.1); }
    </style>
    <script src="js/jquery-3.7.1.js"></script>
</head>

<body>
    <button class="theme-toggle-btn" id="themeToggle" title="Ganti Tema">🌓</button>

    <div class="container">
        <h2>Login</h2>
        <form action="process/proses_login.php" method="post">

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" name="login" class="btn btn-save">Login</button>
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