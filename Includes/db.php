<?php
$host = 'localhost';
$db = 'gateapp';
$user = 'root';
$pass = 'rWwR7qMQ2ecRK06';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
$pdo = new PDO($dsn, $user, $pass, $options);
?>
