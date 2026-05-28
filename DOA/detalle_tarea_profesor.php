<?php
$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar tarea, alumno...";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Detalle de tarea | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/detalle_tarea_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-detalle-tarea-profesor">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa">
            <section class="detalle-asignatura-principal">
                <div class="cabecera-detalle-asignatura">
                    <div class="cabecera-detalle-asignatura__texto">
                        <a class="enlace-volver-asignaturas" href="listado_tareas_profesor.php" id="linkVolverTareas">
                            <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                <img alt="" src="img/iconos/grey-chevron-right.svg">
                            </span>

                            <span>Volver a tareas</span>
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
                        <nav aria-label="Secciones de la asignatura" class="pestanas-asignatura">
                            <a class="pestanas-asignatura__item" href="recursos_profesor.php" id="linkPestanaRecursos">
                                Recursos
                            </a>

                            <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="listado_tareas_profesor.php" id="linkPestanaTareas">
                                Tareas
                            </a>

                            <a class="pestanas-asignatura__item" href="examenes_profesor.php" id="linkPestanaExamenes">
                                Exámenes
                            </a>

                            <a class="pestanas-asignatura__item" href="calificaciones_profesor.php" id="linkPestanaCalificaciones">
                                Calificaciones
                            </a>
                        </nav>
                    </div>
                </div>

                <section class="detalle-tarea-profesor-grid">
                    <article class="tarjeta-tarea-profesor">
                        <div class="cabecera-tarea-profesor">
                            <span class="etiqueta-tarea-profesor" id="estadoTareaProfesor">Estado</span>

                            <h2 id="tituloTareaProfesor">Tarea</h2>

                            <p id="descripcionTareaProfesor">
                                Descripción de la tarea.
                            </p>
                        </div>

                        <section class="resumen-tarea-profesor" aria-label="Resumen de la tarea">
                            <article class="dato-tarea-profesor dato-tarea-profesor--principal">
                                <span>Entregas</span>
                                <strong id="entregasTareaProfesor">-</strong>
                            </article>

                            <article class="dato-tarea-profesor">
                                <span>Pendientes</span>
                                <strong id="pendientesTareaProfesor">-</strong>
                            </article>

                            <article class="dato-tarea-profesor">
                                <span>Fecha de entrega</span>
                                <strong id="fechaEntregaTareaProfesor">-</strong>
                            </article>

                            <article class="dato-tarea-profesor">
                                <span>Estado</span>
                                <strong id="estadoResumenTareaProfesor">-</strong>
                            </article>
                        </section>

                        <section class="bloque-entregas-profesor">
                            <div class="cabecera-bloque-entregas">
                                <h3>Entregas del alumnado</h3>

                                <div class="grupo-filtros">
                                    <label class="filtro-select">
                                        <select id="filtroEstadoEntrega">
                                            <option value="todos">Estado: todos</option>
                                            <option value="entregada">Entregadas</option>
                                            <option value="tardia">Tardías</option>
                                            <option value="pendiente">Pendientes</option>
                                        </select>
                                    </label>

                                    <label class="filtro-select">
                                        <select id="ordenEntregas">
                                            <option value="nombre">Ordenar por nombre</option>
                                            <option value="nota">Ordenar por nota</option>
                                            <option value="fecha">Ordenar por fecha</option>
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <div class="tabla-entregas-profesor">
                                <div class="tabla-entregas-profesor__cabecera">
                                    <p>Alumno</p>
                                    <p>Estado</p>
                                    <p>Fecha</p>
                                    <p>Calificación</p>
                                    <p>Acción</p>
                                </div>

                                <div id="cuerpoEntregasProfesor"></div>
                            </div>
                        </section>
                    </article>

                    <aside class="panel-tarea-profesor">
                        <article class="tarjeta-lateral-tarea-profesor">
                            <h3>Acciones</h3>

                            <a class="boton-tarea-profesor boton-tarea-profesor--principal" href="crear_tarea.php" id="linkEditarTarea">
                                Editar tarea
                            </a>

                            <a class="boton-tarea-profesor" href="calificaciones_profesor.php" id="linkCalificacionesTarea">
                                Ver calificaciones
                            </a>
                        </article>

                        <article class="tarjeta-lateral-tarea-profesor">
                            <h3>Recursos adjuntos</h3>

                            <ul class="lista-recursos-profesor" id="listaRecursosTareaProfesor"></ul>
                        </article>
                    </aside>
                </section>
            </section>
        </main>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/doa_datos.js"></script>
    <script src="js/detalle_tarea_profesor.js"></script>
</body>
</html>