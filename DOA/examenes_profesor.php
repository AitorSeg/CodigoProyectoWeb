<?php
$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar examen...";

require_once __DIR__ . "/includes/proteger_doa.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Exámenes | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/examenes.css" rel="stylesheet">
    <link href="css/examenes_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-examenes pagina-examenes-profesor">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa">
            <section class="cabecera-detalle-asignatura">
                <div class="cabecera-detalle-asignatura__texto">
                    <a class="enlace-volver-asignaturas" href="detalle_asignatura_profesor.php" id="linkVolverDetalle">
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

                        <a class="pestanas-asignatura__item" href="listado_tareas_profesor.php" id="linkPestanaTareas">
                            Tareas
                        </a>

                        <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="examenes_profesor.php" id="linkPestanaExamenes">
                            Exámenes
                        </a>

                        <a class="pestanas-asignatura__item" href="calificaciones_profesor.php" id="linkPestanaCalificaciones">
                            Calificaciones
                        </a>
                    </nav>
                </div>
            </section>

            <section class="cabecera-seccion-profesor">
                <div>
                    <h2>Exámenes de la asignatura</h2>

                    <p>
                        Consulta los exámenes publicados, revisa su estado y accede al detalle de cada prueba.
                    </p>
                </div>

                <a class="boton-profesor-principal" href="crear_examen.php" id="linkCrearExamen">
                    Crear examen
                </a>
            </section>

            <section class="resumen-examenes-profesor" aria-label="Resumen de exámenes">
                <article class="tarjeta-resumen-examen tarjeta-resumen-examen--principal">
                    <span>Exámenes publicados</span>
                    <strong id="totalExamenesPublicados">0</strong>
                </article>

                <article class="tarjeta-resumen-examen">
                    <span>Abiertos</span>
                    <strong id="totalExamenesAbiertos">0</strong>
                </article>

                <article class="tarjeta-resumen-examen">
                    <span>Entregas recibidas</span>
                    <strong id="totalEntregasRecibidas">0</strong>
                </article>

                <article class="tarjeta-resumen-examen">
                    <span>Pendientes de revisar</span>
                    <strong id="totalPendientesRevision">0</strong>
                </article>
            </section>

            <section class="seccion-examenes-profesor">
                <div class="cabecera-listado-examenes">
                    <h2>Listado de exámenes</h2>

                    <div class="filtros-examenes-profesor">
                        <button class="filtro-examen filtro-examen--activo" type="button" data-filtro="todos">
                            Todos
                        </button>

                        <button class="filtro-examen" type="button" data-filtro="abierto">
                            Abiertos
                        </button>

                        <button class="filtro-examen" type="button" data-filtro="proximo">
                            Próximos
                        </button>

                        <button class="filtro-examen" type="button" data-filtro="cerrado">
                            Cerrados
                        </button>
                    </div>
                </div>

                <div class="tabla-examenes-profesor">
                    <div class="tabla-examenes-profesor__cabecera">
                        <span>Examen</span>
                        <span>Fecha</span>
                        <span>Duración</span>
                        <span>Entregas</span>
                        <span>Pendientes</span>
                        <span>Estado</span>
                        <span>Acción</span>
                    </div>

                    <div id="listadoExamenesProfesor"></div>
                </div>
            </section>
        </main>
    </div>

    
    <script src="js/doa_datos.js"></script>
    <script src="js/examenes_profesor.js"></script>
</body>
</html>