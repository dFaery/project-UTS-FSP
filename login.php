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

        .form-group {
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
    </style>
</head>

<body>
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
</body>

</html>