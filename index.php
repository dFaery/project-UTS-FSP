<?php
session_start(); 
if(!isset($_SESSION['user'])){
    header("Location: login.php");
}

$username = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        body {
            font-family: sans-serif;
        }

        h3 {
            text-align: left;
            color: #2c3e50;
            padding: 12px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0px 20px 0px 20px;
        }

        .btn-change-password {
            padding: 12px 16px 12px 16px;
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

        .btn-change-password>a {
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
        }       

        .btn-change-password:hover {
            background-color: #2980b9;
            transform: scale(1.02);
        }
    </style>
</head>

<body>
    <header>
        <h3>Homepage</h3>
        <form action="change_password.php" method="POST">
            <div class="btn-change-password">
                <a href="change_password.php">Change Password</a>                                
                <input type="hidden" name="username" value="<?php echo(htmlspecialchars($username));?>">
            </div>            
        </form>
    </header>
</body>

</html>