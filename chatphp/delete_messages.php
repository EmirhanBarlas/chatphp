<?php
require_once "sql.php";
session_start();
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
$connection->close();
header("Location: chat.php");
exit;
?>
