<?php
$servername = "localhost";
$username = "user_name";
$password = "sql_password";
$dbname = "database_name";
$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("Veritabanına bağlanılamadı: " . $connection->connect_error);
}
?>
