<?php
$host = 'localhost';
$base_datos = 'gti_doa';
$usuario = 'root';
$contrasena = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$base_datos;charset=$charset";

$pdo = new PDO($dsn, $usuario, $contrasena, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
