<?php
$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar recurso, tarea...";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Inicio: metadatos principales -->
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Detalle de asignatura | DOA</title>
    <!-- Fin: metadatos principales -->

    <!-- Inicio: hojas de estilo -->
    <link href="css/doa.css" rel="stylesheet" />
    <link href="css/doa_layout.css" rel="stylesheet" />
    <link href="css/doa_componentes.css" rel="stylesheet" />
    <link href="css/detalle_asignatura.css" rel="stylesheet" />
    <!-- Fin: hojas de estilo -->

    <!-- Inicio: fuente Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet" />
    <!-- Fin: fuente Inter -->
</head>

<body class="pagina-doa pagina-detalle-asignatura">
    <!-- Inicio: cabecera común DOA -->
    <?php include "includes/header-doa.php"; ?>
    <!-- Fin: cabecera común DOA -->

    <!-- Inicio: layout principal del detalle de asignatura -->
    <div class="layout-doa">
        <!-- Inicio: navegación lateral común -->
        <?php include "includes/barra-lateral-doa.php"; ?>
        <!-- Fin: navegación lateral común -->

        <!-- Inicio: contenido principal del detalle de asignatura -->
        <main class="contenido-doa contenido-detalle-asignatura">
            <div class="detalle-asignatura-grid">
                <!-- Inicio: zona principal del detalle de asignatura -->
                <section class="detalle-asignatura-principal">
                    <!-- Inicio: cabecera del detalle de asignatura -->
                    <div class="cabecera-detalle-asignatura">
                        <!-- Inicio: información de la asignatura -->
                        <div class="cabecera-detalle-asignatura__texto">
                            <a class="enlace-volver-asignaturas" href="asignaturas.php">
                                <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                    <img alt="" src="img/iconos/grey-chevron-right.svg" />
                                </span>
                                <span>Volver a mis asignaturas</span>
                            </a>

                            <h1 id="tituloAsignatura">Cargando...</h1>

                            <ul class="metadatos-asignatura">
                                <li>
                                    <img alt="" src="img/iconos/grey-user.svg" />
                                    <span id="profesorAsignatura">Cargando...</span>
                                </li>

                                <li>
                                    <img alt="" src="img/iconos/grey-notebook.svg" />
                                    <span id="unidadActualTextoAsignatura">Unidad actual</span>
                                </li>
                            </ul>
                        </div>
                        <!-- Fin: información de la asignatura -->

                        <!-- Inicio: pestañas internas de la asignatura -->
                        <div class="cabecera-detalle-asignatura__pestanas">
                            <nav aria-label="Secciones de la asignatura" class="pestanas-asignatura">
                                <a class="pestanas-asignatura__item" href="recursos_alumno.php" id="linkPestanaRecursos">
                                    Recursos
                                </a>

                                <a class="pestanas-asignatura__item" href="listado_tareas.php">
                                    Tareas
                                </a>

                                <a class="pestanas-asignatura__item" href="examenes.php">
                                    Exámenes
                                </a>

                                <a class="pestanas-asignatura__item" href="calificaciones.php">
                                    Calificaciones
                                </a>
                            </nav>
                        </div>
                        <!-- Fin: pestañas internas de la asignatura -->
                    </div>
                    <!-- Fin: cabecera del detalle de asignatura -->

                    <!-- Inicio: ruta de progreso de la asignatura -->
                    <article class="tarjeta-progreso-asignatura tarjeta-progreso-asignatura--activa tarjeta-progreso-asignatura--sin-margen">
                        <div class="tarjeta-progreso-asignatura__cabecera">
                            <h2>Ruta de progreso</h2>
                        </div>

                        <div aria-label="Ruta de progreso de la asignatura" class="progreso-asignatura progreso-asignatura--avance-40-33" id="rutaProgresoAsignatura">
                            <span class="progreso-asignatura__destello" aria-hidden="true"></span>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada progreso-asignatura__unidad--ocultar-movil">
                                <span class="progreso-asignatura__badge"></span>

                                <span aria-hidden="true" class="progreso-asignatura__estado">
                                    <img alt="" src="img/iconos/blue-check.svg" />
                                </span>

                                <a class="progreso-asignatura__nombre" href="#">
                                    Unidad 01
                                </a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada">
                                <span class="progreso-asignatura__badge"></span>

                                <span aria-hidden="true" class="progreso-asignatura__estado">
                                    <img alt="" src="img/iconos/blue-check.svg" />
                                </span>

                                <a class="progreso-asignatura__nombre" href="#">
                                    Unidad 02
                                </a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--actual">
                                <span class="progreso-asignatura__badge">
                                    Actual
                                </span>

                                <span aria-hidden="true" class="progreso-asignatura__estado">
                                    <img alt="" src="img/iconos/blue-play.svg" />
                                </span>

                                <a class="progreso-asignatura__nombre" href="#">
                                    Unidad 03
                                </a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada">
                                <span class="progreso-asignatura__badge"></span>

                                <span aria-hidden="true" class="progreso-asignatura__estado">
                                    <img alt="" src="img/iconos/grey-x.svg" />
                                </span>

                                <a class="progreso-asignatura__nombre" href="#">
                                    Unidad 04
                                </a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada progreso-asignatura__unidad--ocultar-movil">
                                <span class="progreso-asignatura__badge"></span>

                                <span aria-hidden="true" class="progreso-asignatura__estado">
                                    <img alt="" src="img/iconos/grey-x.svg" />
                                </span>

                                <a class="progreso-asignatura__nombre" href="#">
                                    Unidad 05
                                </a>
                            </div>
                        </div>
                    </article>
                    <!-- Fin: ruta de progreso de la asignatura -->

                    <!-- Inicio: tarjeta de unidad actual -->
                    <article class="tarjeta-unidad-actual">
                        <div class="tarjeta-unidad-actual__contenido">
                            <p class="tarjeta-unidad-actual__etiqueta">
                                Unidad actual
                            </p>

                            <h2 id="tituloUnidadActual">
                                Unidad 03. Recursividad
                            </h2>

                            <p id="descripcionUnidadActual">
                                En esta unidad aprenderás qué es la recursividad y cómo usarla para resolver problemas paso a paso.
                                Veremos cómo una función puede llamarse a sí misma de forma controlada mediante un caso base
                                y una llamada recursiva.
                            </p>

                            <div class="tarjeta-unidad-actual__bloques">
                                <article class="bloque-unidad">
                                    <strong>Objetivo</strong>
                                    <span>Comprender el funcionamiento de una llamada recursiva y su caso base.</span>
                                </article>

                                <article class="bloque-unidad">
                                    <strong>Estado</strong>
                                    <span>Unidad en curso dentro de la ruta de progreso de la asignatura.</span>
                                </article>

                                <article class="bloque-unidad">
                                    <strong>Siguiente paso</strong>
                                    <span>Revisar los recursos del tema y completar la próxima tarea.</span>
                                </article>
                            </div>

                            <div class="tarjeta-unidad-actual__acciones">
                                <a class="boton-entrar-asignatura" href="recursos_alumno.php" id="linkBotonRecursos">
                                    Recursos del tema
                                </a>

                                <div class="tarjeta-unidad-actual__enlaces">
                                    <a href="#">
                                        Criterios de evaluación
                                    </a>

                                    <a href="#">
                                        Conocimientos previos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <!-- Fin: tarjeta de unidad actual -->
                </section>
                <!-- Fin: zona principal del detalle de asignatura -->

                <!-- Inicio: panel lateral derecho de la asignatura -->
                <aside class="panel-derecho-asignatura">
                    <!-- Inicio: tarjeta de próxima evaluación -->
                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">
                            Próxima evaluación
                        </p>

                        <div class="tarjeta-lateral-panel__contenido">
                            <h2 id="tituloEvaluacionAsignatura">
                                Parcial 1
                            </h2>

                            <ul class="lista-detalles-panel">
                                <li>
                                    <img alt="" src="img/iconos/grey-calendar.svg" />
                                    <span id="fechaEvaluacionAsignatura">15 Oct, 2025</span>
                                </li>

                                <li>
                                    <img alt="" src="img/iconos/grey-clock.svg" />
                                    <span id="horaEvaluacionAsignatura">10:00 AM</span>
                                </li>

                                <li>
                                    <img alt="" src="img/iconos/grey-map-pin.svg" />
                                    <span id="lugarEvaluacionAsignatura">Edificio G, Aula 6</span>
                                </li>
                            </ul>

                            <a class="boton-secundario-panel" href="#">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                    <!-- Fin: tarjeta de próxima evaluación -->

                    <!-- Inicio: tarjeta de próxima tarea -->
                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">
                            Próxima tarea
                        </p>

                        <div class="tarjeta-lateral-panel__contenido">
                            <h2 id="tituloTareaAsignatura">
                                Ejercicio recursividad
                            </h2>

                            <p class="texto-vencimiento">
                                Vence en:
                                <strong id="vencimientoTareaAsignatura">2 días</strong>
                            </p>

                            <a class="boton-secundario-panel" href="#">
                                Ir a la tarea
                            </a>
                        </div>
                    </div>
                    <!-- Fin: tarjeta de próxima tarea -->
                </aside>
                <!-- Fin: panel lateral derecho de la asignatura -->
            </div>
        </main>
        <!-- Fin: contenido principal del detalle de asignatura -->
    </div>
    <!-- Fin: layout principal del detalle de asignatura -->

    <!-- Inicio: scripts -->
    <script src="js/doa_layout.js"></script>
    <script src="js/doa_datos.js"></script>
    <script src="js/detalle_asignatura.js"></script>
    <!-- Fin: scripts -->
</body>

</html>