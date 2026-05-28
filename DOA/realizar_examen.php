<?php
$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar examen...";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Realizar examen | DOA
    </title>
    <!-- Enlaces a hojas de estilo -->
    <link href="css/doa.css" rel="stylesheet" />
    <link href="css/doa_layout.css" rel="stylesheet" />
    <link href="css/doa_componentes.css" rel="stylesheet" />
    <link href="css/detalle_asignatura.css" rel="stylesheet" />
    <link href="css/realizar_examen.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet" />
    <!-- Fin enlaces a hojas de estilo -->
</head>

<body class="pagina-doa pagina-realizar-examen">
    <!-- Inicio de el header -->
    <?php include "includes/header-doa.php"; ?>
    <!-- Final de el header -->
    <!-- Inicio del contenido principal -->
    <div class="layout-doa">
        <!-- Inicio de la barra lateral -->
        <?php include "includes/barra-lateral-doa.php"; ?>
        <!-- Final de la barra lateral -->
        <!-- Inicio del contenido principal de la página -->
        <main class="contenido-doa">
            <!-- Inicio de la cabecera de detalles de asignatura -->
            <section class="cabecera-detalle-asignatura">
                <!-- Inicio de la información de la asignatura -->
                <div class="cabecera-detalle-asignatura__texto">
                    <a class="enlace-volver-asignaturas" href="detalle_examen.php">
                        <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                            <img alt="" src="img/iconos/grey-chevron-right.svg" />
                        </span>
                        <span>
                            Volver al detalle del examen
                        </span>
                    </a>
                    <h1 id="tituloExamen">
                        Realizar examen
                    </h1>
                    <ul class="metadatos-asignatura">
                        <li>
                            <img alt="" src="img/iconos/grey-notebook.svg" />
                            <span id="asignaturaExamen">
                                Asignatura
                            </span>
                        </li>
                        <li>
                            <img alt="" src="img/iconos/grey-calendar.svg" />
                            <span id="fechaExamen">
                                Fecha
                            </span>
                        </li>
                        <li>
                            <img alt="" src="img/iconos/grey-clock.svg" />
                            <span id="duracionExamen">
                                Duración
                            </span>
                        </li>
                    </ul>
                </div>
                <!-- Final de la información de la asignatura -->
            </section>
            <!-- Final de la cabecera de detalles de asignatura -->
            <section class="realizar-examen-grid">
                <form class="formulario-examen" id="formularioExamen">
                    <div class="tarjeta-instrucciones-examen">
                        <h2>
                            Preguntas tipo test
                        </h2>
                        <p>
                            Selecciona una respuesta por pregunta. En esta demo, al entregar el examen
                            se mostrará el resultado directamente.
                        </p>
                    </div>
                    <div class="lista-preguntas-examen" id="listaPreguntasExamen">
                    </div>
                    <p class="mensaje-error-examen oculto" id="mensajeErrorExamen">
                        Responde todas las preguntas antes de entregar el examen.
                    </p>
                    <div class="acciones-examen">
                        <button class="boton-entregar-examen" type="submit">
                            Entregar examen
                        </button>
                        <a class="boton-secundario-examen" href="detalle_examen.php">
                            Cancelar
                        </a>
                    </div>
                </form>
                <aside class="panel-estado-examen">
                    <div class="tarjeta-estado-examen">
                        <p class="tarjeta-estado-examen__titulo">
                            Progreso
                        </p>
                        <div class="progreso-realizacion-examen">
                            <span id="contadorPreguntasRespondidas">
                                0 de 0 respondidas
                            </span>
                            <span aria-hidden="true" class="barra-progreso-examen">
                                <span id="barraProgresoExamen">
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="tarjeta-estado-examen">
                        <p class="tarjeta-estado-examen__titulo">
                            Información
                        </p>
                        <ul class="lista-info-examen">
                            <li>
                                <span>
                                    Preguntas
                                </span>
                                <strong id="totalPreguntasExamen">
                                    0
                                </strong>
                            </li>
                            <li>
                                <span>
                                    Intentos
                                </span>
                                <strong id="intentosExamen">
                                    1 intento
                                </strong>
                            </li>
                            <li>
                                <span>
                                    Estado
                                </span>
                                <strong>
                                    En curso
                                </strong>
                            </li>
                        </ul>
                    </div>
                    <div class="tarjeta-resultado-examen oculto" id="tarjetaResultadoExamen">
                        <p class="tarjeta-resultado-examen__titulo">
                            Resultado
                        </p>
                        <strong id="notaResultadoExamen">
                            0/10
                        </strong>
                        <span id="detalleResultadoExamen">
                            0 respuestas correctas
                        </span>
                        <a class="boton-volver-resultado" href="detalle_examen.php">
                            Volver al detalle
                        </a>
                    </div>
                </aside>
            </section>
        </main>
        <!-- Final del contenido principal de la página -->
    </div>
    <!-- Final del contenido principal -->
    <script src="js/doa_layout.js">
    </script>
    <script src="js/doa_datos.js">
    </script>
    <script src="js/doa_examenes_datos.js">
    </script>
    <script src="js/realizar_examen.js">
    </script>
</body>

</html>