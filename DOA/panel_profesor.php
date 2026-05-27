<?php
$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar tarea...";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Tareas | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa-layout.css" rel="stylesheet">
    <link href="css/doa-componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/listado_tareas.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-listado-tareas">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa">
            <section class="detalle-asignatura-principal">
                <div class="cabecera-detalle-asignatura">
                    <div class="cabecera-detalle-asignatura__texto">
                        <a class="enlace-volver-asignaturas" href="detalle_asignatura.php" id="linkVolverDetalle">
                            <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                <img alt="" src="img/iconos/grey-chevron-right.svg">
                            </span>

                            <span>Volver a detalles de la asignatura</span>
                        </a>

                        <h1 id="tituloAsignatura">Asignatura</h1>

                        <ul class="metadatos-asignatura">
                            <li>
                                <img alt="" src="img/iconos/grey-user.svg">
                                <span id="profesorAsignatura">Profesor</span>
                            </li>

                            <li>
                                <img alt="" src="img/iconos/grey-notebook.svg">
                                <span id="unidadActualTextoAsignatura">Unidad actual</span>
                            </li>
                        </ul>
                    </div>

                    <div class="cabecera-detalle-asignatura__pestanas">
                        <nav aria-label="Secciones de la asignatura" class="pestanas-asignatura">
                            <a class="pestanas-asignatura__item" href="recursos_alumno.php" id="linkPestanaRecursos">
                                Recursos
                            </a>

                            <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="listado_tareas.php" id="linkPestanaTareas">
                                Tareas
                            </a>

                            <a class="pestanas-asignatura__item" href="examenes.php" id="linkPestanaExamenes">
                                Exámenes
                            </a>

                            <a class="pestanas-asignatura__item" href="calificaciones.php" id="linkPestanaCalificaciones">
                                Calificaciones
                            </a>
                        </nav>
                    </div>
                </div>

                <section class="proxima-entrega">
                    <div class="proxima-entrega__contenido">
                        <span class="etiqueta-estado etiqueta-estado--pendiente" id="estadoProximaTarea">
                            Pendiente
                        </span>

                        <h2>Próxima entrega</h2>

                        <p class="proxima-entrega__titulo" id="tituloProximaTarea">
                            Tarea pendiente
                        </p>

                        <p class="proxima-entrega__descripcion" id="descripcionProximaTarea">
                            Revisa los detalles de la próxima tarea de la asignatura.
                        </p>
                    </div>

                    <div class="proxima-entrega__accion">
                        <p class="proxima-entrega__tiempo" id="tiempoProximaTarea">
                            3 días
                        </p>

                        <a class="boton-ver-detalles" href="detalle_tarea.php" id="linkProximaTarea">
                            Ver detalles
                        </a>
                    </div>
                </section>

                <section class="bloque-listado-tareas">
                    <div class="cabecera-listado-tareas">
                        <h2>Tareas de la asignatura</h2>

                        <div class="filtros-tareas">
                            <label class="filtro-tarea">
                                <span>Tipo:</span>

                                <select id="filtroTipo">
                                    <option value="todas">Todas</option>
                                    <option value="tarea">Tareas</option>
                                    <option value="practica">Prácticas</option>
                                </select>
                            </label>

                            <label class="filtro-tarea">
                                <span>Estado:</span>

                                <select id="filtroEstado">
                                    <option value="todos">Todos</option>
                                    <option value="entregada">Entregada</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="tardia">Tardía</option>
                                </select>
                            </label>

                            <label class="filtro-tarea">
                                <span>Ordenar:</span>

                                <select id="ordenTareas">
                                    <option value="fecha_entrega">Fecha de entrega</option>
                                    <option value="nombre">Nombre</option>
                                    <option value="estado">Estado</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="tabla-tareas">
                        <div class="tabla-tareas__cabecera">
                            <p>Tarea</p>
                            <p>Fecha de emisión</p>
                            <p>Fecha de entrega</p>
                            <p>Estado</p>
                            <p>Calificación</p>
                        </div>

                        <div id="cuerpoTablaTareas"></div>
                    </div>
                </section>
            </section>
        </main>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/doa-datos.js"></script>
    <script src="js/listado_tareas.js"></script>
</body>
</html>