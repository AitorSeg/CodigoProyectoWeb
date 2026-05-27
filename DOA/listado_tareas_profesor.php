<?php
$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar tarea...";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Tareas del profesor | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa-layout.css" rel="stylesheet">
    <link href="css/doa-componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/listado_tareas.css" rel="stylesheet">
    <link href="css/listado_tareas_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-listado-tareas pagina-listado-tareas-profesor">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa">
            <section class="detalle-asignatura-principal">
                <div class="cabecera-detalle-asignatura">
                    <div class="cabecera-detalle-asignatura__texto">
                        <a class="enlace-volver-asignaturas" href="detalle_asignatura_profesor.php" id="linkVolverDetalle">
                            <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                <img alt="" src="img/iconos/grey-chevron-right.svg">
                            </span>

                            <span>Volver a detalles de la asignatura</span>
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

                            <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="listado_tareas_profe.php" id="linkPestanaTareas">
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

                <section class="cabecera-tareas-profesor">
                    <div>
                        <h2>Tareas de la asignatura</h2>

                        <p>
                            Gestiona las tareas publicadas, revisa entregas y accede al detalle de cada actividad.
                        </p>
                    </div>

                    <a class="boton-crear-tarea-profesor" href="crear_tarea.php" id="linkCrearTarea">
                        Crear tarea
                    </a>
                </section>

                <section class="resumen-tareas-profesor" aria-label="Resumen de tareas">
                    <article class="tarjeta-resumen-tarea tarjeta-resumen-tarea--principal">
                        <span>Tareas activas</span>
                        <strong id="totalTareasActivas">0</strong>
                    </article>

                    <article class="tarjeta-resumen-tarea">
                        <span>Entregas recibidas</span>
                        <strong id="totalEntregasRecibidas">0</strong>
                    </article>

                    <article class="tarjeta-resumen-tarea">
                        <span>Pendientes de revisar</span>
                        <strong id="totalPendientesRevision">0</strong>
                    </article>

                    <article class="tarjeta-resumen-tarea">
                        <span>Tareas cerradas</span>
                        <strong id="totalTareasCerradas">0</strong>
                    </article>
                </section>

                <section class="bloque-listado-tareas">
                    <div class="cabecera-listado-tareas">
                        <h2>Listado de tareas</h2>

                        <div class="grupo-filtros">
                            <label class="filtro-select">
                                <select id="filtroTipo">
                                    <option value="todas">Tipo: todas</option>
                                    <option value="tarea">Tipo: tareas</option>
                                    <option value="practica">Tipo: prácticas</option>
                                </select>
                            </label>

                            <label class="filtro-select">
                                <select id="filtroEstado">
                                    <option value="todos">Estado: todos</option>
                                    <option value="publicada">Estado: publicadas</option>
                                    <option value="cerrada">Estado: cerradas</option>
                                    <option value="borrador">Estado: borradores</option>
                                </select>
                            </label>

                            <label class="filtro-select">
                                <select id="ordenTareas">
                                    <option value="fecha_entrega">Ordenar por fecha</option>
                                    <option value="nombre">Ordenar por nombre</option>
                                    <option value="pendientes">Ordenar por pendientes</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="tabla-tareas tabla-tareas-profesor">
                        <div class="tabla-tareas__cabecera">
                            <p>Tarea</p>
                            <p>Fecha de entrega</p>
                            <p>Entregas</p>
                            <p>Pendientes</p>
                            <p>Estado</p>
                            <p>Acción</p>
                        </div>

                        <div id="cuerpoTablaTareasProfesor"></div>
                    </div>
                </section>
            </section>
        </main>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/doa-datos.js"></script>
    <script src="js/listado_tareas_profesor.js"></script>
</body>
</html>