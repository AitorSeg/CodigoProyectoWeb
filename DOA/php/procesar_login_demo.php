<?php
session_start();

/*
    Cargamos los usuarios mock de forma compatible con las dos versiones
    que hemos usado durante el desarrollo.
*/
$usuariosDemo = [];
$datosUsuarios = require __DIR__ . '/usuarios_demo.php';

if (is_array($datosUsuarios)) {
    $usuariosDemo = $datosUsuarios['usuarios'] ?? $datosUsuarios['usuariosDemo'] ?? $usuariosDemo;
}

$email = strtolower(trim($_POST['email'] ?? ''));
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    header('Location: elegir_perfil.php?error=campos');
    exit;
}

$usuarioEncontrado = null;

foreach ($usuariosDemo as $usuario) {
    if (($usuario['email'] ?? '') === $email && ($usuario['password'] ?? '') === $password) {
        $usuarioEncontrado = $usuario;
        break;
    }
}

if ($usuarioEncontrado === null) {
    $emailUrl = urlencode($email);
    header("Location: elegir_perfil.php?error=credenciales&email=$emailUrl");
    exit;
}

$_SESSION['usuario_demo'] = [
    'id' => $usuarioEncontrado['id'],
    'dni' => $usuarioEncontrado['dni'],
    'nombre' => $usuarioEncontrado['nombre'],
    'email' => $usuarioEncontrado['email'],
    'tipo' => $usuarioEncontrado['tipo'],
    'rol' => $usuarioEncontrado['rol'],
];

header('Location: panel_principal.php');
exit;
