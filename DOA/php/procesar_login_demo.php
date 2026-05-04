<?php
session_start();
require_once __DIR__ . '/usuarios_demo.php';

/*
    Intelephense no siempre detecta variables creadas dentro de archivos incluidos.
    Esta línea deja claro que la lista de usuarios existe después del require_once.
*/
$usuariosDemo = $usuariosDemo ?? [];

$email = strtolower(trim($_POST['email'] ?? ''));
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    header('Location: elegir_perfil.php?error=campos');
    exit;
}

$usuario = buscarUsuarioPorCredenciales($usuariosDemo, $email, $password);

if ($usuario === null) {
    $emailUrl = urlencode($email);
    header("Location: elegir_perfil.php?error=credenciales&email=$emailUrl");
    exit;
}

$_SESSION['usuario_demo'] = [
    'id' => $usuario['id'],
    'dni' => $usuario['dni'],
    'nombre' => $usuario['nombre'],
    'email' => $usuario['email'],
    'tipo' => $usuario['tipo'],
    'rol' => $usuario['rol'],
];

/*
    De momento todos los perfiles entran al mismo panel.
    Más adelante se podrá redirigir según el tipo de usuario.
*/
header('Location: panel_principal.php');
exit;
