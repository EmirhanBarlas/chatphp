<?php
require_once "sql.php";
$messages_query = $connection->query("SELECT users.username, messages.message, messages.timestamp FROM messages JOIN users ON messages.user_id = users.id ORDER BY messages.id DESC LIMIT 10");
$messages = $messages_query->fetch_all(MYSQLI_ASSOC);
foreach ($messages as $message) {
    echo '<div class="message-item">';
    echo '<div class="' . ($message['username'] === $username ? 'my-message-text' : 'message-text') . '"><strong>' . htmlspecialchars($message['username']) . ':</strong> ' . htmlspecialchars($message['message']) . '</div>';
    echo '<div class="message-meta">' . date('Y-m-d H:i:s', strtotime($message['timestamp'])) . '</div>';
    echo '</div>';
}
$connection->close();
?>
