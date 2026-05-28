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

    <title>Entrega del alumno | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa-layout.css" rel="stylesheet">
    <link href="css/doa-componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/detalle_tarea_entregada.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-detalle-tarea-entregada">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa">
            <section class="detalle-asignatura-principal">
                <div class="cabecera-detalle-asignatura">
                    <div class="cabecera-detalle-asignatura__texto">
                        <a class="enlace-volver-asignaturas" href="detalle_tarea_profesor.php" id="linkVolverTareaProfesor">
                            <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                <img alt="" src="img/iconos/grey-chevron-right.svg">
                            </span>

                            <span>Volver a la tarea</span>
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

                <section class="entrega-profesor-grid">
                    <article class="tarjeta-entrega-profesor">
                        <div class="cabecera-entrega-profesor">
                            <span class="etiqueta-entrega-profesor" id="estadoEntregaAlumno">Estado</span>

                            <h2 id="tituloTareaEntrega">Tarea</h2>

                            <p id="descripcionTareaEntrega">
                                Descripción de la tarea.
                            </p>
                        </div>

                        <section class="resumen-entrega-profesor" aria-label="Resumen de la entrega">
                            <article class="dato-entrega-profesor dato-entrega-profesor--principal">
                                <span>Alumno</span>
                                <strong id="nombreAlumnoEntrega">Alumno</strong>
                            </article>

                            <article class="dato-entrega-profesor">
                                <span>Fecha de entrega</span>
                                <strong id="fechaEntregaAlumno">-</strong>
                            </article>

                            <article class="dato-entrega-profesor">
                                <span>Estado</span>
                                <strong id="estadoResumenEntrega">-</strong>
                            </article>

                            <article class="dato-entrega-profesor">
                                <span>Calificación</span>
                                <strong id="notaResumenEntrega">-</strong>
                            </article>
                        </section>

                        <section class="bloque-archivos-entrega">
                            <h3>Archivos entregados</h3>

                            <ul class="lista-archivos-entrega" id="listaArchivosEntregaProfesor"></ul>
                        </section>

                        <section class="bloque-comentario-alumno">
                            <h3>Comentario del alumno</h3>

                            <p id="comentarioAlumnoEntrega">
                                Sin comentario adicional.
                            </p>
                        </section>
                    </article>

                    <aside class="panel-calificacion-entrega">
                        <form class="tarjeta-calificacion-entrega" id="formCalificacionEntrega">
                            <h3>Calificar entrega</h3>

                            <p>
                                Añade una nota y un comentario visible para el alumno.
                            </p>

                            <div class="grupo-campo-entrega">
                                <label for="inputNotaEntrega">Nota</label>
                                <input id="inputNotaEntrega" type="number" min="0" max="10" step="0.1" placeholder="Ej. 8.5" required>
                            </div>

                            <div class="grupo-campo-entrega">
                                <label for="inputComentarioProfesor">Comentario</label>
                                <textarea id="inputComentarioProfesor" rows="5" placeholder="Escribe una valoración breve..." required></textarea>
                            </div>

                            <button class="boton-guardar-calificacion" type="submit">
                                Guardar calificación
                            </button>

                            <a class="boton-volver-entrega" href="detalle_tarea_profesor.php" id="linkCancelarCalificacion">
                                Volver
                            </a>

                            <p class="mensaje-calificacion mensaje-calificacion--oculto" id="mensajeCalificacion">
                                Calificación guardada correctamente.
                            </p>
                        </form>

                        <article class="tarjeta-info-entrega">
                            <h3>Datos del alumno</h3>

                            <ul class="lista-info-entrega">
                                <li>
                                    <span>Correo</span>
                                    <strong id="correoAlumnoEntrega">-</strong>
                                </li>

                                <li>
                                    <span>Tarea</span>
                                    <strong id="nombreTareaLateral">-</strong>
                                </li>

                                <li>
                                    <span>Asignatura</span>
                                    <strong id="nombreAsignaturaLateral">-</strong>
                                </li>
                            </ul>
                        </article>
                    </aside>
                </section>
            </section>
        </main>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/doa-datos.js"></script>
    <script src="js/detalle_tarea_entregada.js"></script>
</body>
</html>