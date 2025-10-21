    <?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
    } else {
        $username = $_SESSION['user'];
        $isadmin = $_SESSION['is_admin'];                
        if ($isadmin == 1) {
            header("Location: adminhome.php");
        }
    }
    $username = $_POST['username'];
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Change Password</title>
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                font-family: sans-serif;
                background-color: #f0f2f5;
                margin: 0;
            }

            .container {
                width: 400px;
                background-color: #fff;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }

            h1,
            h2 {
                text-align: center;
                color: #2c3e50;
            }

            .form-group,
            .form-group-warning-msg {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }

            .form-group input {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                box-sizing: border-box;
            }

            .btn-group {
                display: flex;
                gap: 10px;
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
                background-color: #686868ff;
                flex-grow: 1;
                width: 100%;
            }

            .btn-back {
                background-color: #7f8c8d;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <h2>Change Password</h2>
            <form action="proses_change_password.php" method="post">

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
        <script src="jquery-3.7.1.js"></script>
        <script>
            $(document).ready(function() {
                $('#password, #re_enter_password').keyup(function() {
                    //hapus msg yg lama
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
            });
        </script>
    </body>

    </html>