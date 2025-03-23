<?php
$host = 'localhost';
$db_login = 'root';
$db_password = '';
$db_name = 'washing';
$conn = new mysqli($host, $db_login, $db_password, $db_name);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

?>