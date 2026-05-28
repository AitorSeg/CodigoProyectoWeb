<?php
session_start();

if (!isset($rol_pagina)) {
    http_response_code(500);
    exit("Error de configuración: falta definir \$rol_pagina antes de cargar proteger_doa.php.");
}

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../GTI/login.php?redir=doa");
    exit;
}

if (!isset($_SESSION["doa_id_usuario"])) {
    header("Location: elegir_perfil.php");
    exit;
}

$panel_usuario_doa = match ($_SESSION["doa_rol"]) {
    "alumno" => "panel_principal.php",
    "profesor" => "panel_profesor.php",
    "secretaria" => "panel_secretaria.php",
};

if ($_SESSION["doa_rol"] !== $rol_pagina) {
    header("Location: " . $panel_usuario_doa);
    exit;
}

$nombre_usuario_doa = trim($_SESSION["doa_nombre"] . " " . $_SESSION["doa_apellidos"]);

$rol_usuario_doa_texto = match ($_SESSION["doa_rol"]) {
    "alumno" => "Alumno",
    "profesor" => "Profesor",
    "secretaria" => "Secretaría",
};

function limpiar_texto_doa($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, "UTF-8");
}