<?php
$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar examen...";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Inicio: metadatos principales -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del examen | DOA</title>
    <!-- Fin: metadatos principales -->

    <!-- Inicio: hojas de estilo -->
    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa-layout.css" rel="stylesheet">
    <link href="css/doa-componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/detalle_examen.css" rel="stylesheet">
    <!-- Fin: hojas de estilo -->

    <!-- Inicio: fuente Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Fin: fuente Inter -->
</head>

<body class="pagina-doa pagina-detalle-examen">
    <!-- Inicio: cabecera común DOA -->
    <?php include "includes/header-doa.php"; ?>
    <!-- Fin: cabecera común DOA -->

    <!-- Inicio: layout principal del detalle de examen -->
    <div class="layout-doa">
        <!-- Inicio: navegación lateral común -->
        <?php include "includes/barra-lateral-doa.php"; ?>
        <!-- Fin: navegación lateral común -->

        <!-- Inicio: contenido principal del detalle de examen -->
        <main class="contenido-doa">
            <!-- Inicio: cabecera del examen -->
            <section class="cabecera-detalle-asignatura">
                <!-- Inicio: información principal del examen -->
                <div class="cabecera-detalle-asignatura__texto">
                    <a class="enlace-volver-asignaturas" href="examenes.php">
                        <span class="enlace-volver-asignaturas__icono" aria-hidden="true">
                            <img src="img/iconos/grey-chevron-right.svg" alt="">
                        </span>
                        <span>Volver a exámenes</span>
                    </a>

                    <h1 id="tituloExamen">Detalle del examen</h1>

                    <ul class="metadatos-asignatura">
                        <li>
                            <img src="img/iconos/grey-notebook.svg" alt="">
                            <span id="asignaturaExamen">Asignatura</span>
                        </li>

                        <li>
                            <img src="img/iconos/grey-calendar.svg" alt="">
                            <span id="fechaExamen">Fecha</span>
                        </li>
                    </ul>
                </div>
                <!-- Fin: información principal del examen -->

                <!-- Inicio: pestañas internas de la asignatura -->
                <div class="cabecera-detalle-asignatura__pestanas">
                    <nav class="pestanas-asignatura" aria-label="Secciones de la asignatura">
                        <a class="pestanas-asignatura__item" href="recursos_alumno.php">Recursos</a>
                        <a class="pestanas-asignatura__item" href="listado_tareas.html">Tareas</a>
                        <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="examenes.php">Exámenes</a>
                        <a class="pestanas-asignatura__item" href="calificaciones.php">Calificaciones</a>
                    </nav>
                </div>
                <!-- Fin: pestañas internas de la asignatura -->
            </section>
            <!-- Fin: cabecera del examen -->

            <!-- Inicio: cuerpo del detalle de examen -->
            <section class="detalle-examen-grid">
                <!-- Inicio: tarjeta principal del examen -->
                <article class="tarjeta-detalle-examen" id="tarjetaDetalleExamen">
                    <div class="tarjeta-detalle-examen__cabecera">
                        <span class="estado-detalle-examen" id="estadoExamen">Abierto</span>

                        <h2>Información del examen</h2>

                        <p id="descripcionExamen">
                            Examen tipo test sobre los contenidos de la unidad actual.
                        </p>
                    </div>

                    <!-- Inicio: datos principales del examen -->
                    <div class="datos-examen">
                        <div class="dato-examen">
                            <span>Apertura</span>
                            <strong id="aperturaExamen">10 Nov, 2026</strong>
                        </div>

                        <div class="dato-examen">
                            <span>Cierre</span>
                            <strong id="cierreExamen">15 Nov, 2026</strong>
                        </div>

                        <div class="dato-examen">
                            <span>Duración</span>
                            <strong id="duracionExamen">45 min</strong>
                        </div>

                        <div class="dato-examen">
                            <span>Preguntas</span>
                            <strong id="preguntasExamen">10 preguntas</strong>
                        </div>

                        <div class="dato-examen">
                            <span>Intentos</span>
                            <strong id="intentosExamen">1 intento</strong>
                        </div>
                    </div>
                    <!-- Fin: datos principales del examen -->

                    <!-- Inicio: contenido evaluado -->
                    <div class="bloque-detalle-examen">
                        <h3>Contenido evaluado</h3>
                        <ul class="lista-temas-examen" id="temasExamen"></ul>
                    </div>
                    <!-- Fin: contenido evaluado -->

                    <!-- Inicio: indicaciones -->
                    <div class="bloque-detalle-examen">
                        <h3>Indicaciones</h3>

                        <p>
                            Lee con atención cada pregunta antes de responder. Cuando empieces el examen,
                            el tiempo comenzará a contar y no podrás reiniciarlo desde esta demo.
                        </p>
                    </div>
                    <!-- Fin: indicaciones -->
                </article>
                <!-- Fin: tarjeta principal del examen -->

                <!-- Inicio: panel lateral de acción -->
                <aside class="panel-accion-examen">
                    <div class="tarjeta-accion-examen">
                        <p class="tarjeta-accion-examen__titulo">Acceso al examen</p>

                        <p class="tarjeta-accion-examen__texto" id="mensajeAccesoExamen">
                            El examen está disponible. Puedes empezar cuando quieras.
                        </p>

                        <a class="boton-realizar-examen" href="realizar_examen.html" id="botonRealizarExamen">
                            Realizar examen
                        </a>
                    </div>

                    <div class="tarjeta-aviso-examen">
                        <strong>Recuerda</strong>

                        <p>
                            Esta pantalla forma parte de la demo. Las respuestas no afectarán a ningún expediente real.
                        </p>
                    </div>
                </aside>
                <!-- Fin: panel lateral de acción -->
            </section>
            <!-- Fin: cuerpo del detalle de examen -->
        </main>
        <!-- Fin: contenido principal del detalle de examen -->
    </div>
    <!-- Fin: layout principal del detalle de examen -->

    <!-- Inicio: scripts -->
    <script src="js/doa_layout.js"></script>
    <script src="js/doa-datos.js"></script>
    <script src="js/doa-examenes-datos.js"></script>
    <script src="js/detalle_examen.js"></script>
    <!-- Fin: scripts -->
</body>
</html>