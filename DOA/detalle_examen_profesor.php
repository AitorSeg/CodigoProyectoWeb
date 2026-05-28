<?php
$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar examen...";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Detalle del examen | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/detalle_examen.css" rel="stylesheet">
    <link href="css/detalle_examen_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-detalle-examen pagina-detalle-examen-profesor">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa">
            <section class="cabecera-detalle-asignatura">
                <div class="cabecera-detalle-asignatura__texto">
                    <a class="enlace-volver-asignaturas" href="examenes_profesor.php" id="linkVolverExamenes">
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
                            <img src="img/iconos/grey-graduation-cap.svg" alt="">
                            <span id="grupoExamen">Grupo</span>
                        </li>

                        <li>
                            <img src="img/iconos/grey-calendar.svg" alt="">
                            <span id="fechaExamen">Fecha</span>
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

            <section class="detalle-examen-grid">
                <article class="tarjeta-detalle-examen" id="tarjetaDetalleExamen">
                    <div class="tarjeta-detalle-examen__cabecera">
                        <span class="estado-detalle-examen" id="estadoExamen">Estado</span>

                        <h2>Información del examen</h2>

                        <p id="descripcionExamen">
                            Descripción del examen.
                        </p>
                    </div>

                    <div class="datos-examen datos-examen--profesor">
                        <div class="dato-examen">
                            <span>Apertura</span>
                            <strong id="aperturaExamen">-</strong>
                        </div>

                        <div class="dato-examen">
                            <span>Cierre</span>
                            <strong id="cierreExamen">-</strong>
                        </div>

                        <div class="dato-examen">
                            <span>Duración</span>
                            <strong id="duracionExamen">-</strong>
                        </div>

                        <div class="dato-examen">
                            <span>Entregas</span>
                            <strong id="entregasExamen">-</strong>
                        </div>

                        <div class="dato-examen">
                            <span>Pendientes</span>
                            <strong id="pendientesExamen">-</strong>
                        </div>
                    </div>

                    <div class="bloque-detalle-examen">
                        <h3>Contenido evaluado</h3>
                        <ul class="lista-temas-examen" id="temasExamen"></ul>
                    </div>

                    <div class="bloque-detalle-examen">
                        <h3>Preguntas configuradas</h3>
                        <div class="lista-preguntas-profesor" id="listaPreguntasProfesor"></div>
                    </div>
                </article>

                <aside class="panel-accion-examen">
                    <div class="tarjeta-accion-examen">
                        <p class="tarjeta-accion-examen__titulo">Gestión del examen</p>

                        <p class="tarjeta-accion-examen__texto" id="mensajeGestionExamen">
                            Revisa el estado del examen y accede a las acciones disponibles.
                        </p>

                        <a class="boton-realizar-examen" href="crear_examen.php" id="linkEditarExamen">
                            Editar examen
                        </a>
                    </div>

                    <div class="tarjeta-aviso-examen">
                        <strong>Seguimiento</strong>

                        <ul class="lista-seguimiento-examen">
                            <li>
                                <span>Alumnos del grupo</span>
                                <strong id="totalAlumnosGrupo">-</strong>
                            </li>

                            <li>
                                <span>Entregados</span>
                                <strong id="totalEntregadosGrupo">-</strong>
                            </li>

                            <li>
                                <span>Sin entregar</span>
                                <strong id="totalSinEntregarGrupo">-</strong>
                            </li>
                        </ul>
                    </div>

                    <div class="tarjeta-aviso-examen">
                        <strong>Accesos rápidos</strong>

                        <div class="acciones-rapidas-examen">
                            <a href="calificaciones_profesor.php" id="linkCalificacionesExamen">
                                Ver calificaciones
                            </a>

                            <a href="examenes_profesor.php" id="linkListadoExamenes">
                                Ver todos los exámenes
                            </a>
                        </div>
                    </div>
                </aside>
            </section>
        </main>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/doa_datos.js"></script>
    <script src="js/detalle_examen_profesor.js"></script>
</body>
</html>