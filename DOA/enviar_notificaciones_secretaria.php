<?php
// Inicio configuración de página

$rol_pagina = "secretaria";
$pagina_activa = "notificaciones";
$enlace_panel = "panel_secretaria.php";
$placeholder_buscador = "Buscar asignatura, profesor, alumno...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar notificación | DOA</title>

    <link rel="stylesheet" href="css/doa.css">
    <link rel="stylesheet" href="css/doa_layout.css">
    <link rel="stylesheet" href="css/doa_componentes.css">
    <link rel="stylesheet" href="css/enviar_notificaciones.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-notificaciones pagina-enviar-notificaciones pagina-enviar-notificaciones--secretaria">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido de envío -->

        <?php require_once __DIR__ . "/includes/contenido-enviar-notificaciones.php"; ?>

        <!-- Fin contenido de envío -->
    </div>
</body>

</html>