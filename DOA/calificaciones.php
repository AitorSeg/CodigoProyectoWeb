<?php
$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar calificación...";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Calificaciones | DOA
    </title>
    <!-- Enlaces a hojas de estilo -->
    <link href="css/doa.css" rel="stylesheet" />
    <link href="css/doa_layout.css" rel="stylesheet" />
    <link href="css/doa_componentes.css" rel="stylesheet" />
    <link href="css/detalle_asignatura.css" rel="stylesheet" />
    <link href="css/calificaciones.css" rel="stylesheet" />

    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet" />
    <!-- Fin enlaces a hojas de estilo -->
</head>

<body class="pagina-doa pagina-calificaciones">
    <!-- Inclusión del header -->
    <?php include "includes/header-doa.php"; ?>

    <!-- Inicio del contenido principal -->
    <div class="layout-doa">

        <!-- Inclusión de la barra lateral -->
        <?php include "includes/barra-lateral-doa.php"; ?>

        <!-- Inicio del contenido principal de la página -->
        <main class="contenido-doa">
            <section class="cabecera-detalle-asignatura">
                <div class="cabecera-detalle-asignatura__texto">
                    <a class="enlace-volver-asignaturas" href="detalle_asignatura.php">
                        <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                            <img alt="" src="img/iconos/grey-chevron-right.svg" />
                        </span>
                        <span>
                            Volver a detalles de la asignatura
                        </span>
                    </a>
                    <h1 id="tituloCalificaciones">
                        Matemáticas
                    </h1>
                    <ul class="metadatos-asignatura">
                        <li>
                            <img alt="" src="img/iconos/grey-user.svg" />
                            <span id="profesorAsignatura">
                                Don Pepito
                            </span>
                        </li>
                        <li>
                            <img alt="" src="img/iconos/grey-notebook.svg" />
                            <span id="unidadActualAsignatura">
                                Unidad 03: Límites
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="cabecera-detalle-asignatura__pestanas">
                    <nav aria-label="Secciones de la asignatura" class="pestanas-asignatura">
                        <a class="pestanas-asignatura__item" href="recursos_alumno.php">
                            Recursos
                        </a>
                        <a class="pestanas-asignatura__item" href="listado_tareas.php">
                            Tareas
                        </a>
                        <a class="pestanas-asignatura__item" href="examenes.php">
                            Exámenes
                        </a>
                        <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="calificaciones.php">
                            Calificaciones
                        </a>
                    </nav>
                </div>
            </section>
            <h2 class="titulo-seccion-calificaciones">Calificaciones de la asignatura</h2>
            <section aria-label="Resumen de calificaciones" class="resumen-calificaciones">
                <article class="tarjeta-resumen-nota tarjeta-resumen-nota--principal">
                    <span>
                        Nota media
                    </span>
                    <strong id="notaMedia">
                        5,9
                    </strong>
                </article>
                <article class="tarjeta-resumen-nota">
                    <span>
                        Nota media exámenes
                    </span>
                    <strong id="notaMediaExamenes">
                        3,7
                    </strong>
                </article>
                <article class="tarjeta-resumen-nota">
                    <span>
                        Nota media tareas
                    </span>
                    <strong id="notaMediaTareas">
                        8,3
                    </strong>
                </article>
                <article class="tarjeta-resumen-nota">
                    <span>
                        Nota media prácticas
                    </span>
                    <strong id="notaMediaPracticas">
                        5,7
                    </strong>
                </article>
            </section>
            <section class="seccion-calificaciones">
                <div class="cabecera-tabla-calificaciones">
                    <h2>
                        Calificaciones
                    </h2>
                    <div class="grupo-filtros">
                        <label class="filtro-select">
                            <select id="filtroTipoCalificacion">
                                <option value="todas">
                                    Tipo: todas
                                </option>
                                <option value="examen">
                                    Exámenes
                                </option>
                                <option value="tarea">
                                    Tareas
                                </option>
                                <option value="practica">
                                    Prácticas
                                </option>
                            </select>
                        </label>
                        <label class="filtro-select">
                            <select id="filtroEstadoCalificacion">
                                <option value="todos">
                                    Estado: todos
                                </option>
                                <option value="corregido">
                                    Corregidas
                                </option>
                                <option value="proxima">
                                    Próximas
                                </option>
                            </select>
                        </label>
                        <label class="filtro-select">
                            <select id="ordenCalificacion">
                                <option value="fecha">
                                    Ordenar por fecha
                                </option>
                                <option value="nota">
                                    Ordenar por nota
                                </option>
                                <option value="nombre">
                                    Ordenar por nombre
                                </option>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="tabla-calificaciones">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    Actividad
                                </th>
                                <th>
                                    Tipo
                                </th>
                                <th>
                                    Unidad
                                </th>
                                <th>
                                    Peso
                                </th>
                                <th>
                                    Nota
                                </th>
                                <th>
                                    Estado
                                </th>
                                <th>
                                    Fecha
                                </th>
                                <th>
                                    Acción
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tablaCalificaciones">
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
        <!-- Final del contenido principal de la página -->
    </div>
    <!-- Final del contenido principal -->
    <script src="js/doa_layout.js">
    </script>
    <script src="js/doa_datos.js">
    </script>
    <script src="js/calificaciones.js">
    </script>
</body>

</html>