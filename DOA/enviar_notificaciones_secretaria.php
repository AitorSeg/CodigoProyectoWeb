<?php
$rol_pagina = "secretaria";
$pagina_activa = "notificaciones";
$enlace_panel = "panel_secretaria.php";
$placeholder_buscador = "Buscar asignatura, profesor, alumno...";
?>

<!DOCTYPE html>
<html lang="es">
<head>
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
</head>

<body class="pagina-doa pagina-notificaciones pagina-enviar-notificaciones">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <?php include "includes/contenido-enviar-notificaciones.php"; ?>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/enviar_notificaciones.js"></script>
</body>
</html>