<?php
$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar alumno, calificación...";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Calificaciones del grupo | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa-layout.css" rel="stylesheet">
    <link href="css/doa-componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/calificaciones.css" rel="stylesheet">
    <link href="css/calificaciones_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-calificaciones pagina-calificaciones-profesor">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa">
            <section class="cabecera-detalle-asignatura">
                <div class="cabecera-detalle-asignatura__texto">
                    <a class="enlace-volver-asignaturas" href="detalle_asignatura_profesor.php">
                        <span class="enlace-volver-asignaturas__icono" aria-hidden="true">
                            <img src="img/iconos/grey-chevron-right.svg" alt="">
                        </span>
                        <span>Volver a detalles de la asignatura</span>
                    </a>

                    <h1 id="tituloAsignatura">Asignatura</h1>

                    <ul class="metadatos-asignatura">
                        <li>
                            <img src="img/iconos/grey-graduation-cap.svg" alt="">
                            <span id="grupoAsignatura">Grupo</span>
                        </li>

                        <li>
                            <img src="img/iconos/grey-user.svg" alt="">
                            <span id="totalAlumnosAsignatura">Alumnos</span>
                        </li>

                        <li>
                            <img src="img/iconos/grey-notebook.svg" alt="">
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

                        <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="calificaciones_profesor.php" id="linkPestanaCalificaciones">
                            Calificaciones
                        </a>
                    </nav>
                </div>
            </section>

            <h2 class="titulo-seccion-calificaciones">Calificaciones del grupo</h2>

            <section class="resumen-calificaciones" aria-label="Resumen de calificaciones del grupo">
                <article class="tarjeta-resumen-nota tarjeta-resumen-nota--principal">
                    <span>Nota media del grupo</span>
                    <strong id="notaMediaGrupo">-</strong>
                </article>

                <article class="tarjeta-resumen-nota">
                    <span>Alumnos aprobados</span>
                    <strong id="alumnosAprobados">-</strong>
                </article>

                <article class="tarjeta-resumen-nota">
                    <span>Pendientes de corregir</span>
                    <strong id="pendientesCorreccion">-</strong>
                </article>

                <article class="tarjeta-resumen-nota">
                    <span>Alumnos suspendidos</span>
                    <strong id="alumnosSuspendidos">-</strong>
                </article>
            </section>

            <section class="seccion-calificaciones">
                <div class="cabecera-tabla-calificaciones">
                    <h2>Seguimiento del alumnado</h2>

                    <div class="filtros-calificaciones">
                        <label class="filtro-calificaciones">
                            <select id="filtroEstadoCalificacion" aria-label="Filtrar por estado">
                                <option value="todos">Estado: todos</option>
                                <option value="aprobado">Aprobados</option>
                                <option value="suspendido">Suspendidos</option>
                                <option value="pendiente">Pendientes</option>
                            </select>

                            <span class="filtro-calificaciones__icono" aria-hidden="true">
                                <img src="img/iconos/grey-chevron-right.svg" alt="">
                            </span>
                        </label>

                        <label class="filtro-calificaciones">
                            <select id="ordenCalificacion" aria-label="Ordenar calificaciones">
                                <option value="nota">Ordenar por nota</option>
                                <option value="nombre">Ordenar por nombre</option>
                                <option value="pendientes">Ordenar por pendientes</option>
                            </select>

                            <span class="filtro-calificaciones__icono" aria-hidden="true">
                                <img src="img/iconos/grey-chevron-right.svg" alt="">
                            </span>
                        </label>
                    </div>
                </div>

                <div class="tabla-calificaciones tabla-calificaciones--profesor">
                    <table>
                        <thead>
                            <tr>
                                <th>Alumno</th>
                                <th>Tareas</th>
                                <th>Exámenes</th>
                                <th>Nota final</th>
                                <th>Pendientes</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody id="tablaCalificacionesProfesor"></tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/doa-datos.js"></script>
    <script src="js/calificaciones_profesor.js"></script>
</body>
</html>