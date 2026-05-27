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

    <title>Detalle de tarea | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa-layout.css" rel="stylesheet">
    <link href="css/doa-componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/detalle_tarea.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-detalle-tarea">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa">
            <section class="detalle-asignatura-principal">
                <div class="cabecera-detalle-asignatura">
                    <div class="cabecera-detalle-asignatura__texto">
                        <a class="enlace-volver-asignaturas" href="listado_tareas.php" id="linkVolverTareas">
                            <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                <img alt="" src="img/iconos/grey-chevron-right.svg">
                            </span>

                            <span>Volver a tareas</span>
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

                <section class="detalle-tarea">
                    <div class="cabecera-tarea-detalle">
                        <span class="etiqueta-estado etiqueta-estado--pendiente" id="estadoTarea">
                            Pendiente
                        </span>

                        <h2 id="tituloTarea">Tarea</h2>

                        <p class="descripcion-tarea" id="descripcionTarea">
                            Descripción de la tarea.
                        </p>
                    </div>

                    <div class="fechas-tarea">
                        <div class="fecha-tarea">
                            <span>Fecha de emisión</span>
                            <p class="fecha-tarea__valor" id="fechaEmisionTarea">-</p>
                        </div>

                        <div class="fecha-tarea">
                            <span>Fecha de entrega</span>
                            <p class="fecha-tarea__valor" id="fechaEntregaTarea">-</p>
                        </div>

                        <div class="fecha-tarea">
                            <span>Calificación</span>
                            <p class="fecha-tarea__valor" id="calificacionTarea">-</p>
                        </div>
                    </div>

                    <div class="zona-entrega">
                        <section class="archivos-subidos">
                            <div>
                                <h3 class="titulo-bloque-tarea">Mi entrega</h3>

                                <ul class="lista-archivos" id="listaArchivosEntrega"></ul>
                            </div>

                            <div class="subida-archivo">
                                <label class="boton-subir" for="archivoEntrega">
                                    Seleccionar archivo
                                </label>

                                <input class="input-archivo-entrega" id="archivoEntrega" type="file" multiple>
                            </div>
                        </section>

                        <section class="recursos-adjuntos">
                            <h3>Recursos adjuntos</h3>

                            <ul id="listaRecursosAdjuntos"></ul>
                        </section>

                        <section class="tarjeta-acciones-tarea">
                            <h3>Acciones de entrega</h3>

                            <p>
                                Puedes guardar tu entrega como borrador o entregarla cuando tengas el archivo preparado.
                            </p>

                            <div class="acciones-tarea">
                                <button class="boton-secundario" type="button" id="btnGuardarTarea">
                                    Guardar
                                </button>

                                <button class="boton-principal" type="button" id="btnEntregarTarea">
                                    Entregar
                                </button>

                                <a class="boton-secundario boton-enlace-tarea" href="listado_tareas.php" id="linkCancelarTarea">
                                    Cancelar
                                </a>
                            </div>
                        </section>
                    </div>

                    <p class="mensaje-tarea mensaje-tarea--oculto" id="mensajeTarea">
                        Cambios guardados.
                    </p>
                </section>
            </section>
        </main>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/doa-datos.js"></script>
    <script src="js/detalle_tarea.js"></script>
</body>

</html>