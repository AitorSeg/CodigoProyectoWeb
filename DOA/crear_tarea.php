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

    <title>Crear tarea | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa-layout.css" rel="stylesheet">
    <link href="css/doa-componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/crear_tarea.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-crear-tarea">
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

                <form class="crear-tarea-grid" id="formularioCrearTarea">
                    <section class="columna-principal-tarea">
                        <article class="tarjeta-formulario-tarea">
                            <h2 id="tituloPaginaTarea">Crear tarea</h2>

                            <div class="grupo-campo-tarea">
                                <label for="inputTituloTarea">Título de la tarea</label>
                                <input id="inputTituloTarea" type="text" placeholder="Ej. Práctica: cooperación de webs" required>
                            </div>

                            <div class="formulario-doble-tarea">
                                <div class="grupo-campo-tarea">
                                    <label for="selectTipoTarea">Tipo</label>

                                    <select id="selectTipoTarea" required>
                                        <option value="tarea">Tarea</option>
                                        <option value="practica">Práctica</option>
                                    </select>
                                </div>

                                <div class="grupo-campo-tarea">
                                    <label for="inputUnidadTarea">Unidad</label>
                                    <input id="inputUnidadTarea" type="text" placeholder="Ej. Unidad 03" required>
                                </div>
                            </div>

                            <div class="grupo-campo-tarea">
                                <label for="inputDescripcionTarea">Descripción</label>
                                <textarea id="inputDescripcionTarea" rows="5" placeholder="Explica qué debe entregar el alumnado..." required></textarea>
                            </div>
                        </article>

                        <article class="tarjeta-formulario-tarea">
                            <h2>Fechas y entrega</h2>

                            <div class="formulario-doble-tarea">
                                <div class="grupo-campo-tarea">
                                    <label for="inputFechaEmision">Fecha de emisión</label>
                                    <input id="inputFechaEmision" type="date" required>
                                </div>

                                <div class="grupo-campo-tarea">
                                    <label for="inputFechaEntrega">Fecha de entrega</label>
                                    <input id="inputFechaEntrega" type="date" required>
                                </div>
                            </div>

                            <div class="grupo-campo-tarea">
                                <label for="selectEstadoTarea">Estado</label>

                                <select id="selectEstadoTarea" required>
                                    <option value="publicada">Publicada</option>
                                    <option value="borrador">Borrador</option>
                                </select>
                            </div>
                        </article>

                        <article class="tarjeta-formulario-tarea">
                            <div class="cabecera-recursos-tarea">
                                <div>
                                    <h2>Recursos adjuntos</h2>

                                    <p>
                                        Añade materiales relacionados con la tarea. En esta demo solo se muestran en pantalla.
                                    </p>
                                </div>

                                <label class="boton-anadir-recurso-tarea" for="inputRecursosTarea">
                                    Añadir recurso
                                </label>

                                <input id="inputRecursosTarea" class="input-recursos-tarea" type="file" multiple>
                            </div>

                            <ul class="lista-recursos-tarea" id="listaRecursosTarea">
                                <li class="item-sin-recursos">Todavía no hay recursos adjuntos.</li>
                            </ul>
                        </article>
                    </section>

                    <aside class="columna-lateral-tarea">
                        <article class="tarjeta-publicacion-tarea">
                            <h2>Publicación</h2>

                            <p>
                                Guarda la tarea para que aparezca en el listado del profesor.
                            </p>

                            <ul class="resumen-publicacion-tarea">
                                <li>
                                    <span>Asignatura</span>
                                    <strong id="resumenAsignaturaTarea">-</strong>
                                </li>

                                <li>
                                    <span>Tipo</span>
                                    <strong id="resumenTipoTarea">-</strong>
                                </li>

                                <li>
                                    <span>Estado</span>
                                    <strong id="resumenEstadoTarea">-</strong>
                                </li>
                            </ul>

                            <button class="boton-publicar-tarea" type="submit" id="botonGuardarTarea">
                                Guardar tarea
                            </button>

                            <a class="boton-descartar-tarea" href="listado_tareas_profesor.php" id="botonDescartarTarea">
                                Descartar
                            </a>

                            <p class="mensaje-tarea-guardada mensaje-tarea-guardada--oculto" id="mensajeTareaGuardada">
                                Tarea guardada correctamente.
                            </p>
                        </article>
                    </aside>
                </form>
            </section>
        </main>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/doa-datos.js"></script>
    <script src="js/crear_tarea.js"></script>
</body>

</html>