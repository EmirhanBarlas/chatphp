<?php
session_start();
require_once "sql.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}
$user_id = $_SESSION["user_id"];
$select_query = $connection->prepare("SELECT username FROM users WHERE id = ?");
$select_query->bind_param("i", $user_id);
$select_query->execute();
$result = $select_query->get_result();
$user = $result->fetch_assoc();
$username = $user["username"];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["message"])) {
    $message = $_POST["message"];
    $insert_query = $connection->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
    $insert_query->bind_param("is", $user_id, $message);
    $insert_query->execute();
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $session_insert_query = $connection->prepare("INSERT INTO sessions (user_id, session_id, ip_address) VALUES (?, ?, ?)");
    $session_insert_query->bind_param("iss", $user_id, session_id(), $ip_address);
    $session_insert_query->execute();
}
$messages_query = $connection->query("SELECT users.username, messages.message, messages.timestamp FROM messages JOIN users ON messages.user_id = users.id ORDER BY messages.id DESC LIMIT 10");
$messages = $messages_query->fetch_all(MYSQLI_ASSOC);
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sohbet</title>
    <style>
        body {
            background-color: #f5f5f5;
            margin-bottom: 70px;
        }

        .container {
            max-width: 650px;
            margin-top: 20px;
        }

        .chat-box {
            background-color: #fff;
            border-radius: 10px;
            height: 91%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 15px;
        }

        .chat-header {
            background-color: #4caf50;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        .chat-messages {
            max-height: 90%;
            overflow-y: n;
            padding: 10px;
        }

        .chat-input-group {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 10px;
            background-color: #ffff;
            border-top: 1px solid #ccc;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .chat-input {
            flex-grow: 1;
            border-radius: 0;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .chat-send {
            border-radius: 0;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            background-color: #ece5dd;
        }

        .message-item {
            margin-bottom: 15px;
        }

        .message-text {
            background-color: #e2f7cb;
            padding: 10px;
            border-radius: 10px;
            word-wrap: break-word;
            display: inline-block;
            max-width: 70%;
        }

        .message-meta {
            font-size: 12px;
            color: #555;
            margin-left: 10px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="chat-box">
        <div class="chat-header">
        <h2 class="mb-4">Chat PHP - <?php echo $username; ?></h2>
        </div>
        <div class="chat-messages">
            <?php
            foreach ($messages as $message) {
                echo '<div class="message-item">';
                echo '<div class="message-text"><strong>' . htmlspecialchars($message['username']) . ':</strong> ' . htmlspecialchars($message['message']) . '</div>';
                echo '<div class="message-meta">' . date('Y-m-d H:i:s', strtotime($message['timestamp'])) . '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>

<div class="chat-input-group">
    <form id="message-form" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" class="d-flex">
        <input type="text" name="message" class="form-control chat-input" autocomplete="off" required>
        <button type="submit" class="btn btn-success chat-send">Send</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    function updateChat() {
        $.ajax({
            url: "get_messages.php",
            method: "GET",
            success: function (data) {
                $(".chat-messages").html(data);
            }
        });
    }
    $(document).ready(function () {
        updateChat();
        setInterval(updateChat, 10);
        $("#message-form").submit(function (event) {
            event.preventDefault();
            $.ajax({
                url: $(this).attr("action"),
                method: $(this).attr("method"),
                data: $(this).serialize(),
                success: function () {
                    updateChat();
                    $("#message-form input[name='message']").val('');
                }
            });
        });
    });
</script>
</body>
</html>
