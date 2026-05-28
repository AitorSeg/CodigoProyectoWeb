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

    <title>Crear examen | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/crear_examen.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-crear-examen">
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

                    <h1 id="tituloPaginaExamen">Crear examen</h1>

                    <ul class="metadatos-asignatura">
                        <li>
                            <img src="img/iconos/grey-notebook.svg" alt="">
                            <span id="tituloAsignatura">Asignatura</span>
                        </li>

                        <li>
                            <img src="img/iconos/grey-graduation-cap.svg" alt="">
                            <span id="grupoAsignatura">Grupo</span>
                        </li>

                        <li>
                            <img src="img/iconos/grey-calendar.svg" alt="">
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

            <form class="crear-examen-grid" id="formularioCrearExamen">
                <section class="columna-principal-formulario">
                    <article class="tarjeta-formulario-examen">
                        <h2>Configuración del examen</h2>

                        <div class="grupo-campo-formulario">
                            <label for="inputNombre">Nombre del examen</label>
                            <input id="inputNombre" type="text" placeholder="Ej. Parcial 02" required>
                        </div>

                        <div class="formulario-doble">
                            <div class="grupo-campo-formulario">
                                <label for="selectAsignatura">Asignatura</label>

                                <select id="selectAsignatura" required>
                                    <option value="programacion">Programación II</option>
                                    <option value="matematicas">Matemáticas</option>
                                    <option value="fisica">Física</option>
                                </select>
                            </div>

                            <div class="grupo-campo-formulario">
                                <label for="inputUnidad">Unidad</label>
                                <input id="inputUnidad" type="text" placeholder="Ej. Unidad 03" required>
                            </div>
                        </div>

                        <div class="grupo-campo-formulario">
                            <label for="inputDescripcion">Descripción o instrucciones</label>
                            <textarea id="inputDescripcion" rows="3" placeholder="Indica qué contenidos se evaluarán..." required></textarea>
                        </div>
                    </article>

                    <article class="tarjeta-formulario-examen">
                        <h2>Ajustes de evaluación</h2>

                        <div class="formulario-doble">
                            <div class="grupo-campo-formulario">
                                <label for="inputFechaApertura">Fecha de apertura</label>
                                <input id="inputFechaApertura" type="date" required>
                            </div>

                            <div class="grupo-campo-formulario">
                                <label for="inputFechaCierre">Fecha de cierre</label>
                                <input id="inputFechaCierre" type="date" required>
                            </div>
                        </div>

                        <div class="formulario-doble">
                            <div class="grupo-campo-formulario">
                                <label for="inputDuracion">Duración en minutos</label>
                                <input id="inputDuracion" type="number" min="1" placeholder="45" required>
                            </div>

                            <div class="grupo-campo-formulario">
                                <label for="inputIntentos">Intentos permitidos</label>
                                <input id="inputIntentos" type="number" min="1" value="1" required>
                            </div>
                        </div>
                    </article>

                    <article class="tarjeta-formulario-examen">
                        <div class="cabecera-preguntas-crear">
                            <div>
                                <h2>Preguntas del examen</h2>
                                <p>Añade preguntas tipo test y marca la respuesta correcta.</p>
                            </div>
                        </div>

                        <div class="contenedor-preguntas-crear" id="contenedorPreguntas"></div>

                        <button class="boton-anadir-pregunta" id="btnAnadirPregunta" type="button">
                            Añadir pregunta
                        </button>
                    </article>
                </section>

                <aside class="columna-lateral-formulario">
                    <article class="tarjeta-publicacion-examen">
                        <h2>Publicación</h2>

                        <p>
                            Al publicar, el examen quedará guardado en esta demo y volverás al listado de exámenes.
                        </p>

                        <ul class="resumen-publicacion-examen">
                            <li>
                                <span>Asignatura</span>
                                <strong id="resumenAsignaturaExamen">-</strong>
                            </li>

                            <li>
                                <span>Preguntas</span>
                                <strong id="resumenPreguntasExamen">0</strong>
                            </li>
                        </ul>

                        <button class="boton-publicar-examen" type="submit" id="botonPublicarExamen">
                            Publicar examen
                        </button>

                        <a class="boton-descartar-examen" href="examenes_profesor.php" id="botonDescartarExamen">
                            Descartar
                        </a>

                        <p class="mensaje-examen-guardado mensaje-examen-guardado--oculto" id="mensajeExamenGuardado">
                            Examen guardado correctamente.
                        </p>
                    </article>
                </aside>
            </form>
        </main>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/doa_datos.js"></script>
    <script src="js/crear_examen.js"></script>
</body>
</html>