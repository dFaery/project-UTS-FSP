<?php
session_start();
require_once("class/Grup.php");
require_once("class/Thread.php");
require_once("class/Chat.php");

$grupObj = new Grup();
$threadObj = new Thread();
$chatObj = new Chat();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: adminhome.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('Thread tidak valid'); window.location.href='index.php';</script>";
}
$idThread = $_GET['id'];
$username = $_SESSION['user'];

// VALIDASI ADA/TIDAKNYA ID THREAD DI DB
$thread = $threadObj->getThreadById($idThread);
if (!$thread) {
    echo "<script>alert('Thread tidak ditemukan'); window.location.href='index.php';</script>";
    exit();
}

// VALIDASI USER TELAH BERGABUNG DIGRUP ATAU TIDAK
$idGrup = $thread['idgrup'];
$isMember = $grupObj->isMember($idGrup, $username);
if (!$isMember) {
    echo "<script>alert('Akses Ditolak: Anda bukan anggota grup ini atau telah dikeluarkan.'); window.location.href='index.php';</script>";
    exit();
}
// VALIDASI OWNER THREAD ATAU BUKAN
$isOwner = ($thread['username_pembuat'] == $username);
$isOpen = ($thread['status']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        .chat-container {
            width: 100%;
            height: 100vh;
            margin: auto;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-height: 24px;
            z-index: 1000;
            padding: 15px;
            background: #fff;
            color: #000;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .main {
            flex: 1;
            padding: 15px;
            padding-bottom: 58px;
            padding-top: 64px;
            background: #ecf0f1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            max-width: 70%;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 14px;
        }

        .message p {
            margin: 0;
        }

        .message.incoming {
            /* display: none; */
            background: #d2d5d7ff;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }

        .message.outgoing {
            /* display: none; */
            background: #3498db;
            color: #fff;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }


        .send-message {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 10px;
            background: #fff;
            border-top: 1px solid #ddd;
            z-index: 1000;
        }


        .send-message input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .send-message button {
            display: none;
            border: none;
            background: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        .send-message button:hover {
            transform: scale(1.1);
            transition: transform 0.2s ease-in-out;
        }

        .header button {
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: transparent;
        }

        .send-message img {
            width: 32px;
            border-radius: 24px;
        }

        .header img {
            width: 16px;
            border-radius: 24px;
        }

        .edit-thread {
            width: 48px;
        }

        .msg-sender {}

        .msg-item {
            font-weight: 100;
        }

        .msg-send-time {
            font-size: 8px;
            text-align: end;
        }

        .send-form {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-right: 12px;
        }

        .send-form input {
            width: 100%;
            flex: 1;
            padding: 10px 14px;
            border: 1px solid #ccc;
            border-radius: 20px;
            outline: none;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="chat-container">
        <div class="header">
            <div class="header-left">
                <button type="button" name="btn-back" id="btn-back">
                    <img src="images/assets/img-arrow-back.png" alt="Send">
                </button>
                <h5>Chat Room</h5>
            </div>
            <div class="edit-thread">
                <button type="button" id="btneditThread">
                    <img src="images/assets/img-dots.png" alt="edit">
                </button>
            </div>
        </div>

        <div class="main" id="chatBox">
            <?php
            // CONTINUE LOOPING CHAT DATA HERE
            $resChat = $chatObj->getAllChatByThreadId($idThread);
            $lastChatId = 0;
            if ($resChat->num_rows > 0) {
                while ($chat = $resChat->fetch_assoc()) {
                    $lastChatId = $chat['idchat'];
                    if ($chat['username_pembuat'] === $username) {
                        // use bubblechat logged in user
                        echo "<div class='message outgoing' data-id='{$chat['idchat']}'>";
                    } else {
                        // use bubblechat other user
                        echo "<div class='message incoming' data-id='{$chat['idchat']}'>";
                    }
                    echo "<p class='msg-sender'><b>" . $chat['username_pembuat'] . "</b></p>";
                    echo "<p class='msg-item'>" . $chat['isi'] . "</p>";
                    echo "<p class='msg-send-time'>" . $chat['tanggal_pembuatan'] . "</p>";
                    echo "</div>";
                }
            }
            ?>
        </div>
        <!-- Biar tahu ini chat id terakhirnya diberapa -->
        <input type="hidden" id="lastChatId" value="<?= $lastChatId ?>">


        <div class="send-message">
            <form method="POST" class="send-form">
                <input type="text" id="messageInput" placeholder="Type a message" name="isi" data-thread-status="<?= $isOpen ?>" required>
                <input type="hidden" id="idthread" value="<?= $idThread ?>">
                <button type="button" id="sendMessage" name="sendMessage">
                    <img src="images/assets/img-send-msg.png" alt="Send">
                </button>
            </form>
        </div>

    </div>

    <script src="js/jquery-3.7.1.js"></script>
    <script>
        $(document).ready(function() {
            setInterval(loadChat, 2000);

            const threadStatus = $('#messageInput').data('thread-status');
            if (threadStatus === 'Open') {
                $('#messageInput').prop('disabled', true);
                $('#messageInput').attr('placeholder', 'Hanya pembuat thread yang dapat mengirim pesan')
                $('#sendMessage').prop('disabled', true);
            }


            $("#messageInput").on("input", function() {
                var inputVal = $(this).val().trim();

                if (inputVal == "") {
                    $("#sendMessage").hide();
                } else {
                    $("#sendMessage").show();
                }
            });

            $('#sendMessage').click(function() {

                let isi = $('#messageInput').val().trim();

                $.ajax({
                    url: "process/ajax_insert_chat.php",
                    type: "POST",
                    data: {
                        idthread: $('#idthread').val(),
                        isi: isi
                    },
                    success: function(res) {
                        $('#messageInput').val('');
                    }
                });
            });

            $('#btn-back').on('click', function() {                
                window.location.href = 'detail_grup.php?id=<?= $idGrup ?>';
            });

            function loadChat() {
                let lastChatId = $("#lastChatId").val()
                console.log("lastChatId sebelum:", lastChatId);

                $.ajax({
                    url: "process/ajax_load_new_chat.php",
                    type: "POST",
                    data: {
                        idThread: $("#idthread").val(),
                        lastChatId: lastChatId
                    },
                    success: function(res) {
                        if (res.trim() === "") return;

                        $("#chatBox").append(res);

                        // deklarasikan DULU
                        let lastDiv = $("#chatBox .message").last();
                        let newLastId = lastDiv.data("id");

                        if (newLastId !== undefined) {
                            $("#lastChatId").val(newLastId);
                        }

                        $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
                    }
                })
            }
        })
    </script>
</body>

</html>