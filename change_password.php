<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
} else {
    $username = $_SESSION['user'];
    $isadmin = $_SESSION['is_admin'];                
    if ($isadmin == 1) {
        header("Location: admin/adminhome.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        :root {
            --bg-body: #f0f2f5;
            --bg-container: #fff;
            --text-main: #2c3e50;
            --text-label: #333;
            --input-bg: #fff;
            --input-text: #000;
            --input-border: #ccc;
            --shadow: rgba(0, 0, 0, 0.1);
        }

        body.dark-mode {
            --bg-body: #18191a;
            --bg-container: #242526;
            --text-main: #e4e6eb;
            --text-label: #b0b3b8;
            --input-bg: #3a3b3c;
            --input-text: #e4e6eb;
            --input-border: #555;
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

        .form-group, .form-group-warning-msg {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--text-label);
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--input-border);
            border-radius: 5px;
            box-sizing: border-box;
            background-color: var(--input-bg);
            color: var(--input-text);
        }

        button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            cursor: pointer;
            text-align: center;
        }

        .change_password {
            background-color: #2ecc71;
            flex-grow: 1;
            width: 100%;
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
        <h2>Change Password</h2>
        <form action="process/proses_change_password.php" method="post">

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo "$username" ?>" readonly>
            </div>

            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password">Re-enter Password:</label>
                <input type="password" id="re_enter_password" name="re_enter_password" required>
            </div>

            <div class="form-group-warning-msg">
                <p class="warning-msg"></p>
            </div>
            <button type="submit" name="change_password" class="change_password" disabled>Change Password</button>
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
            $('#password, #re_enter_password').keyup(function() {
                $('.form-group-warning-msg').empty();
                let password = $('#password').val();
                let confirm = $('#re_enter_password').val();

                if (password === '' || confirm === '') return;

                if (password !== confirm) {
                    $('.form-group-warning-msg').append('<p style="color:red;">❌ Password tidak sama!</p>');
                    $('.change_password').attr("disabled", true);
                    $('.change_password').css("background-color", "#686868ff");
                } else {
                    $('.form-group-warning-msg').append('<p style="color:green;">✅ Password cocok</p>');
                    $('.change_password').removeAttr("disabled");
                    $('.change_password').css("background-color", "#2ecc71");
                }
            });

            $('.change_password').click(function(e) {
                if ($('#password').val() !== $('#re_enter_password').val()) {
                    e.preventDefault();
                    alert('Password dan konfirmasi tidak sama!');
                }
            });
            
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