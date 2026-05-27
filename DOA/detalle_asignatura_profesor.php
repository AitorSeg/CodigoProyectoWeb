<?php
$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar recurso, tarea...";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Detalle de asignatura | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa-layout.css" rel="stylesheet">
    <link href="css/doa-componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/detalle_asignatura_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-detalle-asignatura pagina-detalle-asignatura-profesor">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa contenido-detalle-asignatura contenido-detalle-asignatura-profesor">
            <div class="detalle-asignatura-grid">
                <section class="detalle-asignatura-principal">
                    <div class="cabecera-detalle-asignatura">
                        <div class="cabecera-detalle-asignatura__texto">
                            <a class="enlace-volver-asignaturas" href="asignaturas_profesor.php">
                                <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                    <img alt="" src="img/iconos/grey-chevron-right.svg">
                                </span>
                                <span>Volver a mis asignaturas</span>
                            </a>

                            <h1 id="tituloAsignatura">Asignatura</h1>

                            <ul class="metadatos-asignatura">
                                <li>
                                    <img alt="" src="img/iconos/grey-graduation-cap.svg">
                                    <span id="grupoAsignatura">Grupo</span>
                                </li>

                                <li>
                                    <img alt="" src="img/iconos/grey-user.svg">
                                    <span id="totalAlumnosAsignatura">Alumnos</span>
                                </li>

                                <li>
                                    <img alt="" src="img/iconos/grey-notebook.svg">
                                    <span id="unidadActualTextoAsignatura">Unidad actual</span>
                                </li>
                            </ul>
                        </div>

                        <div class="cabecera-detalle-asignatura__pestanas">
                            <nav class="pestanas-asignatura" aria-label="Secciones de la asignatura">
                                <a class="pestanas-asignatura__item" href="recursos_profesor.php" id="linkPestanaRecursos">
                                    Recursos
                                </a>

                                <a class="pestanas-asignatura__item" href="listado_tareas_profe.html" id="linkPestanaTareas">
                                    Tareas
                                </a>

                                <a class="pestanas-asignatura__item" href="examenes_profesor.html" id="linkPestanaExamenes">
                                    Exámenes
                                </a>

                                <a class="pestanas-asignatura__item" href="calificaciones_profesor.php" id="linkPestanaCalificaciones">
                                    Calificaciones
                                </a>
                            </nav>
                        </div>
                    </div>

                    <section class="resumen-docente-asignatura" aria-label="Resumen docente de la asignatura">
                        <article class="dato-docente-asignatura dato-docente-asignatura--principal">
                            <span>Tareas activas</span>
                            <strong id="totalTareasActivas">0</strong>
                        </article>

                        <article class="dato-docente-asignatura">
                            <span>Entregas pendientes</span>
                            <strong id="totalEntregasPendientes">0</strong>
                        </article>

                        <article class="dato-docente-asignatura">
                            <span>Recursos publicados</span>
                            <strong id="totalRecursosPublicados">0</strong>
                        </article>

                        <article class="dato-docente-asignatura">
                            <span>Próximo examen</span>
                            <strong id="fechaProximoExamen">-</strong>
                        </article>
                    </section>

                    <article class="tarjeta-progreso-asignatura tarjeta-progreso-asignatura--activa tarjeta-progreso-asignatura--sin-margen">
                        <div class="tarjeta-progreso-asignatura__cabecera">
                            <h2>Ruta de progreso</h2>
                        </div>

                        <div class="progreso-asignatura progreso-asignatura--avance-40-33" id="rutaProgresoAsignatura" aria-label="Ruta de progreso de la asignatura">
                            <span class="progreso-asignatura__destello" aria-hidden="true"></span>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada progreso-asignatura__unidad--ocultar-movil">
                                <span class="progreso-asignatura__badge"></span>
                                <span class="progreso-asignatura__estado" aria-hidden="true">
                                    <img alt="" src="img/iconos/blue-check.svg">
                                </span>
                                <a class="progreso-asignatura__nombre" href="#">Unidad 01</a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada">
                                <span class="progreso-asignatura__badge"></span>
                                <span class="progreso-asignatura__estado" aria-hidden="true">
                                    <img alt="" src="img/iconos/blue-check.svg">
                                </span>
                                <a class="progreso-asignatura__nombre" href="#">Unidad 02</a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--actual">
                                <span class="progreso-asignatura__badge">Actual</span>
                                <span class="progreso-asignatura__estado" aria-hidden="true">
                                    <img alt="" src="img/iconos/blue-play.svg">
                                </span>
                                <a class="progreso-asignatura__nombre" href="#">Unidad 03</a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada">
                                <span class="progreso-asignatura__badge"></span>
                                <span class="progreso-asignatura__estado" aria-hidden="true">
                                    <img alt="" src="img/iconos/grey-x.svg">
                                </span>
                                <a class="progreso-asignatura__nombre" href="#">Unidad 04</a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada progreso-asignatura__unidad--ocultar-movil">
                                <span class="progreso-asignatura__badge"></span>
                                <span class="progreso-asignatura__estado" aria-hidden="true">
                                    <img alt="" src="img/iconos/grey-x.svg">
                                </span>
                                <a class="progreso-asignatura__nombre" href="#">Unidad 05</a>
                            </div>
                        </div>
                    </article>

                    <article class="tarjeta-unidad-actual">
                        <div class="tarjeta-unidad-actual__contenido">
                            <p class="tarjeta-unidad-actual__etiqueta">Unidad actual</p>

                            <h2 id="tituloUnidadActual">Unidad actual</h2>

                            <p id="descripcionUnidadActual">
                                Descripción de la unidad actual.
                            </p>

                            <div class="acciones-docente-asignatura">
                                <a class="boton-docente-detalle boton-docente-detalle--principal" href="recursos_profesor.php" id="linkBotonRecursos">
                                    Gestionar recursos
                                </a>

                                <a class="boton-docente-detalle" href="crear_tarea.html" id="linkBotonCrearTarea">
                                    Crear tarea
                                </a>

                                <a class="boton-docente-detalle" href="crearexamen.html" id="linkBotonCrearExamen">
                                    Crear examen
                                </a>

                                <a class="boton-docente-detalle" href="detalle_asignatura.php" id="linkVistaAlumno">
                                    Vista alumno
                                </a>
                            </div>
                        </div>
                    </article>
                </section>

                <aside class="panel-derecho-asignatura">
                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">Tarea destacada</p>

                        <div class="tarjeta-lateral-panel__contenido">
                            <h2 id="tituloTareaDestacada">Tarea</h2>

                            <p class="texto-vencimiento">
                                Entregadas:
                                <strong id="entregasTareaDestacada">0</strong>
                            </p>

                            <a class="boton-secundario-panel" href="listado_tareas_profe.html" id="linkTareaDestacada">
                                Revisar entregas
                            </a>
                        </div>
                    </div>

                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">Próximo examen</p>

                        <div class="tarjeta-lateral-panel__contenido">
                            <h2 id="tituloExamenDestacado">Examen</h2>

                            <ul class="lista-detalles-panel">
                                <li>
                                    <img alt="" src="img/iconos/grey-calendar.svg">
                                    <span id="fechaExamenDestacado">Fecha</span>
                                </li>

                                <li>
                                    <img alt="" src="img/iconos/grey-clock.svg">
                                    <span id="horaExamenDestacado">Hora</span>
                                </li>

                                <li>
                                    <img alt="" src="img/iconos/grey-map-pin.svg">
                                    <span id="lugarExamenDestacado">Aula</span>
                                </li>
                            </ul>

                            <a class="boton-secundario-panel" href="examenes_profesor.html" id="linkExamenDestacado">
                                Detalles
                            </a>
                        </div>
                    </div>

                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">Actividad reciente</p>

                        <div class="lista-actividad-docente" id="listaActividadDocente"></div>
                    </div>
                </aside>
            </div>
        </main>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/doa-datos.js"></script>
    <script src="js/detalle_asignatura_profesor.js"></script>
</body>

</html>