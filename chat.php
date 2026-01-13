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

$thread = $threadObj->getThreadById($idThread);
if (!$thread) {
    echo "<script>alert('Thread tidak ditemukan'); window.location.href='index.php';</script>";
    exit();
}

$idGrup = $thread['idgrup'];
$isMember = $grupObj->isMember($idGrup, $username);
if (!$isMember) {
    echo "<script>alert('Akses Ditolak: Anda bukan anggota grup ini atau telah dikeluarkan.'); window.location.href='index.php';</script>";
    exit();
}
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
        :root {
            --bg-body: #f4f4f4;
            --bg-container: #fff;
            --bg-chat-area: #ecf0f1;
            --text-main: #333;
            --text-light: #fff;
            --msg-in-bg: #d2d5d7ff;
            --msg-in-text: #000;
            --msg-out-bg: #3498db;
            --msg-out-text: #fff;
            --input-bg: #fff;
            --input-text: #000;
            --border-color: #ddd;
            --shadow: rgba(0, 0, 0, 0.1);
        }

        body.dark-mode {
            --bg-body: #18191a;
            --bg-container: #242526;
            --bg-chat-area: #1e1f20;
            --text-main: #e4e6eb;
            --text-light: #e4e6eb;
            --msg-in-bg: #3a3b3c;
            --msg-in-text: #e4e6eb;
            --msg-out-bg: #3498db;
            --msg-out-text: #fff;
            --input-bg: #3a3b3c;
            --input-text: #e4e6eb;
            --border-color: #555;
            --shadow: rgba(255, 255, 255, 0.1);
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            transition: background 0.3s, color 0.3s;
        }

        .chat-container {
            width: 100%;
            height: 100vh;
            margin: auto;
            display: flex;
            flex-direction: column;
            background: var(--bg-container);
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-container);
            z-index: 1000;
            box-sizing: border-box;
            min-height: 56px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .header-right {
            display: flex;
            align-items: center;
        }

        .main {
            flex: 1;
            padding: 15px;
            padding-bottom: 58px;
            padding-top: 64px;
            background: var(--bg-chat-area);
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
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
            background: var(--msg-in-bg);
            color: var(--msg-in-text);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }

        .message.outgoing {
            background: var(--msg-out-bg);
            color: var(--msg-out-text);
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .send-message {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 10px;
            background: var(--bg-container);
            border-top: 1px solid var(--border-color);
            z-index: 1000;
        }

        .send-message input {
            flex: 1;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            outline: none;
            background: var(--input-bg);
            color: var(--input-text);
        }

        .send-message button {
            display: none;
            border: none;
            background: transparent;
            border-radius: 5px;
            cursor: pointer;
        }

        .send-message button:hover {
            transform: scale(1.1);
            transition: transform 0.2s ease-in-out;
        }

        #btn-back {
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: transparent;
            margin-right: 10px;
        }

        body.dark-mode #btn-back img, 
        body.dark-mode .send-message img {
            filter: invert(1);
        }

        .send-message img {
            width: 32px;
            border-radius: 24px;
        }

        .header img {
            width: 16px;
            border-radius: 24px;
        }

        .msg-sender {
            font-size: 12px;
            margin-bottom: 2px !important;
        }

        .msg-item {
            font-weight: normal;
        }

        .msg-send-time {
            font-size: 10px;
            text-align: end;
            opacity: 0.7;
            margin-top: 4px !important;
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
            border: 1px solid var(--border-color);
            border-radius: 20px;
            outline: none;
            font-size: 14px;
            background: var(--input-bg);
            color: var(--input-text);
        }

        .tag {
            padding: 2px 8px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 10px;
            text-transform: uppercase;
            margin-left: 8px;
            display: inline-block;
        }

        .tag-open {
            background: #27ae60;
            color: #fff;
        }

        .tag-close {
            background: #e74c3c;
            color: #fff;
        }

        #btnEditThread {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            background-color: #f39c12;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }

        .theme-toggle-btn {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--text-main);
            color: var(--bg-container);
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1001;
            transition: transform 0.2s;
        }
        .theme-toggle-btn:hover { transform: scale(1.1); }

    </style>
    <script src="js/jquery-3.7.1.js"></script>
</head>

<body>
    <button class="theme-toggle-btn" id="themeToggle" title="Ganti Tema">🌓</button>

    <div class="chat-container">
        <div class="header">
            <div class="header-left">
                <button type="button" name="btn-back" id="btn-back">
                    <img src="images/assets/img-arrow-back.png" alt="Back">
                </button>
                <span>Chat Room</span>
                <?php if ($thread['status'] === "Open"): ?>
                    <span class="tag tag-open">OPEN</span>
                <?php else: ?>
                    <span class="tag tag-close">CLOSED</span>
                <?php endif; ?>
            </div>
            <div class="header-right">
                <form method="post">
                    <input type="hidden" name="threadStatusNow" id="threadStatusNow" value="<?= $thread['status'] ?>">
                    <input type="hidden" name="isOwner" id="isOwner" value="<?= $isOwner ?>">
                    <button type="button" id="btnEditThread">
                        <?php if ($thread['status'] === "Open"): ?>
                            Open Thread
                        <?php else: ?>
                            Close Thread
                        <?php endif ?>
                    </button>
                </form>
            </div>
        </div>

        <div class="main" id="chatBox">
            <?php
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
        <input type="hidden" id="lastChatId" value="<?= $lastChatId ?>">


        <div class="send-message">
            <form method="POST" class="send-form">
                <input type="text" id="messageInput" placeholder="Type a message" name="isi" data-thread-status="<?= $isOpen ?>" required>
                <input type="hidden" id="idThread" value="<?= $idThread ?>">
                <button type="button" id="sendMessage" name="sendMessage">
                    <img src="images/assets/img-send-msg.png" alt="Send">
                </button>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const savedTheme = document.cookie.split('; ').find(row => row.startsWith('theme='));
            if (savedTheme && savedTheme.split('=')[1] === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();

        $(document).ready(function() {
            // Scroll to bottom on load
            $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);

            setInterval(loadChat, 2000);

            const threadStatus = $('#messageInput').data('thread-status');
            const isOwner = $("#isOwner").val();
            
            if (threadStatus === 'Close') {
                $('#messageInput').prop('disabled', true);
                $('#messageInput').attr('placeholder', 'Hanya pembuat thread yang dapat mengirim pesan')
                $('#sendMessage').prop('disabled', true);
            }

            if (isOwner == false) {
                $(".header-right").hide()
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
                        idThread: $('#idThread').val(),
                        isi: isi
                    },
                    success: function(res) {
                        $('#messageInput').val('');
                        loadChat();
                    }
                });
            });

            $("#btnEditThread").click(function() {
                let currentStatus = $("#threadStatusNow").val()
                let newStatus = currentStatus === "Open" ? "Close" : "Open";
                $.ajax({
                    url: "process/ajax_update_thread_status.php",
                    type: "POST",
                    data: {
                        idThread: $("#idThread").val(),
                        status: newStatus
                    },
                    success: function(data) {
                        let statusNow = data.trim();
                        let isOwner = $("#isOwner").val();
                        $("#threadStatusNow").val(statusNow);

                        if(isOwner == 1){
                            if (statusNow === "Open") {
                                $(".tag").removeClass("tag-close").addClass("tag-open").text("OPEN");
                                $("#btnEditThread").text("Close Thread");
                                $("#messageInput").prop("disabled", false);
                                $('#messageInput').attr('placeholder', 'Type a message')
    
                            } else {
                                $(".tag").removeClass("tag-open").addClass("tag-close").text("CLOSED");
                                $("#btnEditThread").text("Open Thread");
                                $("#messageInput").prop("disabled", true);
                                $('#messageInput').attr('placeholder', 'Hanya pembuat thread yang dapat mengirim pesan')
                            }
                        }
                    }
                })
            })

            $('#btn-back').on('click', function() {
                window.location.href = 'detail_grup.php?id=<?= $idGrup ?>';
            });

            function loadChat() {
                let lastChatId = $("#lastChatId").val()
                $.ajax({
                    url: "process/ajax_load_new_chat.php",
                    type: "POST",
                    data: {
                        idThread: $("#idThread").val(),
                        lastChatId: lastChatId
                    },
                    success: function(res) {
                        if (res.trim() === "") return;

                        $("#chatBox").append(res);

                        let lastDiv = $("#chatBox .message").last();
                        let newLastId = lastDiv.data("id");

                        if (newLastId !== undefined) {
                            $("#lastChatId").val(newLastId);
                        }

                        $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
                    }
                })
            }

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