<?php
$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar examen...";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Inicio: metadatos principales -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exámenes | DOA</title>
    <!-- Fin: metadatos principales -->

    <!-- Inicio: hojas de estilo -->
    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa-layout.css" rel="stylesheet">
    <link href="css/doa-componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/examenes.css" rel="stylesheet">
    <!-- Fin: hojas de estilo -->

    <!-- Inicio: fuente Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Fin: fuente Inter -->
</head>

<body class="pagina-doa pagina-examenes">
    <!-- Inicio: cabecera común DOA -->
    <?php include "includes/header-doa.php"; ?>
    <!-- Fin: cabecera común DOA -->

    <!-- Inicio: layout principal de exámenes del alumno -->
    <div class="layout-doa">
        <!-- Inicio: navegación lateral común -->
        <?php include "includes/barra-lateral-doa.php"; ?>
        <!-- Fin: navegación lateral común -->

        <!-- Inicio: contenido principal de exámenes -->
        <main class="contenido-doa contenido-detalle-asignatura contenido-examenes">
            <!-- Inicio: cabecera de la asignatura -->
            <section class="detalle-asignatura-principal">
                <div class="cabecera-detalle-asignatura">
                    <!-- Inicio: información de la asignatura -->
                    <div class="cabecera-detalle-asignatura__texto">
                        <a class="enlace-volver-asignaturas" href="detalle_asignatura.php">
                            <span class="enlace-volver-asignaturas__icono" aria-hidden="true">
                                <img src="img/iconos/grey-chevron-right.svg" alt="">
                            </span>
                            <span>Volver a detalles de la asignatura</span>
                        </a>

                        <h1 id="tituloAsignatura">Cargando...</h1>

                        <ul class="metadatos-asignatura">
                            <li>
                                <img src="img/iconos/grey-user.svg" alt="">
                                <span id="profesorAsignatura">Cargando...</span>
                            </li>

                            <li>
                                <img src="img/iconos/grey-notebook.svg" alt="">
                                <span id="unidadActualTextoAsignatura">Unidad actual</span>
                            </li>
                        </ul>
                    </div>
                    <!-- Fin: información de la asignatura -->

                    <!-- Inicio: pestañas internas de la asignatura -->
                    <div class="cabecera-detalle-asignatura__pestanas">
                        <nav class="pestanas-asignatura" aria-label="Secciones de la asignatura">
                            <a class="pestanas-asignatura__item" href="recursos_alumno.php">Recursos</a>
                            <a class="pestanas-asignatura__item" href="listado_tareas.html">Tareas</a>
                            <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="examenes.php">Exámenes</a>
                            <a class="pestanas-asignatura__item" href="calificaciones.html">Calificaciones</a>
                        </nav>
                    </div>
                    <!-- Fin: pestañas internas de la asignatura -->
                </div>

                <!-- Inicio: resumen de exámenes -->
                <section class="resumen-examenes" aria-label="Resumen de exámenes">
                    <article class="tarjeta-resumen-examen">
                        <span>Exámenes abiertos</span>
                        <strong id="totalExamenesAbiertos">0</strong>
                    </article>

                    <article class="tarjeta-resumen-examen">
                        <span>Próximo examen</span>
                        <strong id="proximoExamenTexto">---</strong>
                    </article>

                    <article class="tarjeta-resumen-examen">
                        <span>Completados</span>
                        <strong id="totalExamenesRealizados">0</strong>
                    </article>
                </section>
                <!-- Fin: resumen de exámenes -->

                <!-- Inicio: examen destacado -->
                <section class="examen-destacado" id="examenDestacado">
                    <div class="examen-destacado__contenido">
                        <span class="etiqueta-examen etiqueta-examen--abierto" id="estadoExamenDestacado">
                            Abierto
                        </span>

                        <h2 id="tituloExamenDestacado">Cargando...</h2>

                        <p id="descripcionExamenDestacado">
                            Cargando información del examen...
                        </p>
                    </div>

                    <div class="examen-destacado__accion">
                        <p>
                            <span>Disponible hasta</span>
                            <strong id="fechaLimiteExamenDestacado">---</strong>
                        </p>

                        <a class="boton-entrar-examen" href="detalle_examen.php" id="botonExamenDestacado">
                            Entrar
                        </a>
                    </div>
                </section>
                <!-- Fin: examen destacado -->

                <!-- Inicio: listado de exámenes -->
                <section class="bloque-listado-examenes">
                    <div class="cabecera-listado-examenes">
                        <h2>Exámenes de la asignatura</h2>

                        <div class="filtros-examenes" aria-label="Filtros de exámenes">
                            <button class="filtro-examen filtro-examen--activo" data-filtro="todos" type="button">
                                Todos
                            </button>

                            <button class="filtro-examen" data-filtro="abierto" type="button">
                                Abiertos
                            </button>

                            <button class="filtro-examen" data-filtro="cerrado" type="button">
                                Cerrados
                            </button>

                            <button class="filtro-examen" data-filtro="proximo" type="button">
                                Próximos
                            </button>
                        </div>
                    </div>

                    <div class="tabla-examenes">
                        <div class="tabla-examenes__cabecera">
                            <p>Examen</p>
                            <p>Fecha</p>
                            <p>Duración</p>
                            <p>Estado</p>
                            <p>Acción</p>
                        </div>

                        <div class="tabla-examenes__contenido" id="listadoExamenes"></div>
                    </div>
                </section>
                <!-- Fin: listado de exámenes -->
            </section>
            <!-- Fin: cabecera y contenido de exámenes -->
        </main>
        <!-- Fin: contenido principal de exámenes -->
    </div>
    <!-- Fin: layout principal de exámenes del alumno -->

    <!-- Inicio: scripts -->
    <script src="js/doa_layout.js"></script>
    <script src="js/doa-datos.js"></script>
    <script src="js/doa-examenes-datos.js"></script>
    <script src="js/examenes.js"></script>
    <!-- Fin: scripts -->
</body>
</html>