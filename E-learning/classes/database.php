<?php
$server = 'localhost';
$dbusername = 'E-learning';
$password = 'Oude_Bocht45!';
$database = 'klas4s24_593900';

$conn = new PDO("mysql:host=$server;dbname=$database;charset=utf8mb4", $dbusername, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);