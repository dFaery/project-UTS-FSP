<?php
// session_start();
// if (isset($_SESSION['user'])) {
//     $username = $_SESSION['user'];
//     if ($username != "admin") {
//         header("Location: login.php");
//     }
// } else {
//     header("Location: login.php");
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f5f6fa;
        }

        .admin-avatar {
            width: 42px;
            height: 42px;
            border-radius: 100%;
            object-fit: cover;
        }

        .admin-information {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar {
            width: 230px;
            height: 100vh;
            background-color: #fff;
            border-right: 1px solid #e5e5e5;
            padding: 20px;
            box-sizing: border-box;
        }

        .logo {
            font-size: 18px;
            font-weight: 600;
            color: #444;
            margin-bottom: 25px;
        }

        .menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu li {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 5px;
            border-radius: 8px;
            cursor: pointer;
            color: #555;
            font-size: 15px;
            transition: background-color 0.2s, color 0.2s;
        }

        .menu li:hover {
            background-color: #f0f2f5;
            color: #000;
        }

        .menu li.active {
            background-color: #2d2d2d;
            color: #fff;
        }

        .icon {
            margin-right: 10px;
            font-size: 16px;
        }

        .section-title {
            font-size: 12px;
            color: #999;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin: 20px 0 10px 15px;
        }

        .container {
            display: flex;
        }

        .main-content {
            padding: 12px 20px;
        }        
    </style>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="admin-information">
                <img src="images/111.jpg" class="admin-avatar">
                <h5>Administrator</h5>
            </div>
            <h2 class="logo">Kelola Table</h2>
            <nav class="menu">
                <ul>
                    <li class="active" id="btn-table-dosen">Tabel Dosen</li>
                    <li id="btn-table-mahasiswa">Tabel Mahasiswa</li>
                </ul>
            </nav>
        </aside>
        <section class="main-content">
            <div class="greetings">
                <h3 class="welcome-greetings">Welcome, Admin!!!</h3>
                <p class="label-greetings">What do you want to explore today?</p>
            </div>
            <div class="content-table">
                
            </div>
        </section>

        <script src="jquery-3.7.1.js"></script>
        <script>
            $(document).ready(function() {
                $('.menu li').click(function() {
                    $('.menu li').removeClass('active');
                    $(this).addClass('active');
                });
            });


            var menu_dosen_active = false;
            var menu_mahasiswa_active = false;

            $("#btn-table-dosen").click(function() {
                if (menu_dosen_active == false) {
                    $(".content-table").load("tabeldosen.php");
                    // $(".content-table").append("<h1 class='table-dosen'>Table Dosen</h1>");
                    // $(".table-data").addClass("table-mahasiswa")
                    // $(".table-mahasiswa").remove();
                    menu_dosen_active = true
                    menu_mahasiswa_active = false
                }
            })

            $("#btn-table-mahasiswa").click(function() {
                if (menu_mahasiswa_active == false) {
                    $(".content-table").load("tabelmahasiswa.php");
                    // $(".content-table").append("<h1 class='table-mahasiswa'>Table Mahasiswa</h1>");
                    // $(".table-data").addClass("table-dosen")
                    // $(".table-dosen").remove();
                    menu_mahasiswa_active = true
                    menu_dosen_active = false
                }
            })
        </script>
    </div>
</body>

</html>