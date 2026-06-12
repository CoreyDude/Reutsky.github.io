<?php
// подключение к БД
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'siberia_agro'); // новое имя БД

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (mysqli_connect_errno()) {
    exit('Ошибка подключения к БД: ' . mysqli_connect_error());
}

mysqli_set_charset($connection, "utf8mb4");
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>