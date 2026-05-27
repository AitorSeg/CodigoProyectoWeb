<?php
$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar tarea...";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Listado de tareas | DOA
    </title>
    <!-- Enlaces a hojas de estilo -->
    <link href="css/doa.css" rel="stylesheet" />
    <link href="css/doa-layout.css" rel="stylesheet" />
    <link href="css/doa-componentes.css" rel="stylesheet" />
    <link href="css/detalle_asignatura.css" rel="stylesheet" />
    <link href="css/listado_tareas.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet" />
    <!-- Fin enlaces a hojas de estilo -->
</head>

<body class="pagina-doa pagina-listado-tareas">
    <!-- Header -->
    <?php include "includes/header-doa.php"; ?>
    <!-- Inicio del contenido principal -->
    <div class="layout-doa">
        <!-- Barra Lateral -->
        <?php include "includes/barra-lateral-doa.php"; ?>
        <!-- Inicio del contenido principal de la página -->
        <main class="contenido-doa contenido-detalle-asignatura contenido-listado-tareas">
            <!-- Inicio del detalle de asignatura principal -->
            <section class="detalle-asignatura-principal">
                <!-- Inicio de la cabecera de detalles de asignatura -->
                <div class="cabecera-detalle-asignatura">
                    <!-- Inicio de la información de la asignatura -->
                    <div class="cabecera-detalle-asignatura__texto">
                        <a class="enlace-volver-asignaturas" href="detalle_asignatura.php" id="linkVolverDetalle">
                            <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                <img alt="" src="img/iconos/grey-chevron-right.svg" />
                            </span>
                            <span>
                                Volver a detalles de la asignatura
                            </span>
                        </a>
                        <h1 id="tituloAsignatura">
                            Programación II
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
                                <span id="unidadActualTextoAsignatura">
                                    Unidad 03: Recursividad
                                </span>
                            </li>
                        </ul>
                    </div>
                    <!-- Final de la información de la asignatura -->
                    <!-- Inicio de las pestañas de navegación entre secciones de la asignatura -->
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
                    <!-- Final de las pestañas de navegación entre secciones de la asignatura -->
                </div>
                <!-- Final de la cabecera de detalles de asignatura -->
                <!-- Inicio del bloque de próxima entrega -->
                <section class="proxima-entrega">
                    <div class="proxima-entrega__contenido">
                        <span class="etiqueta-estado etiqueta-estado--entregada" id="estadoProximaTarea">
                            Entregada
                        </span>
                        <h2>
                            PRóXIMA ENTREGA
                        </h2>
                        <p class="proxima-entrega__titulo" id="tituloProximaTarea">
                            Tarea 1: Desarrollo de APIs
                        </p>
                        <p class="proxima-entrega__descripcion" id="descripcionProximaTarea">
                            Descripción de la tarea: Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                            Sed ac eros metus. Mauris id tortor ut nibh viverra rhoncus. Maecenas tincidunt
                            sem dapibus, rhoncus ligula a, ultricies sapien. Phasellus venenatis risus feugiat,
                            tempus elit nec, imperdiet sapien.
                        </p>
                    </div>
                    <div class="proxima-entrega__accion">
                        <p class="proxima-entrega__tiempo" id="tiempoProximaTarea">
                            Tiempo restante: 3 días
                        </p>
                        <a class="boton-ver-detalles" href="detalle_tarea.php" id="linkProximaTarea">
                            Ver detalles
                        </a>
                    </div>
                </section>
                <!-- Final del bloque de próxima entrega -->
                <!-- Inicio del bloque de listado de tareas -->
                <section class="bloque-listado-tareas">
                    <div class="cabecera-listado-tareas">
                        <h2>Tareas</h2>

                        <div class="grupo-filtros">
                            <label class="filtro-select">
                                <select id="filtroTipo">
                                    <option value="todas">Tipo: todas</option>
                                    <option value="tarea">Tipo: tareas</option>
                                    <option value="practica">Tipo: prácticas</option>
                                </select>
                            </label>

                            <label class="filtro-select">
                                <select id="filtroEstado">
                                    <option value="todos">Estado: todos</option>
                                    <option value="entregada">Estado: entregada</option>
                                    <option value="pendiente">Estado: pendiente</option>
                                    <option value="tardia">Estado: tardía</option>
                                </select>
                            </label>

                            <label class="filtro-select">
                                <select id="ordenTareas">
                                    <option value="fecha_entrega">Ordenar por fecha</option>
                                    <option value="nombre">Ordenar por nombre</option>
                                    <option value="estado">Ordenar por estado</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <!-- Final de los filtros de tareas -->
                    <!-- Inicio de la tabla de tareas -->
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
                    <!-- Final de la tabla de tareas -->
                </section>
                <!-- Final del bloque de listado de tareas -->
            </section>
            <!-- Final del detalle de asignatura principal -->
        </main> <!-- Final del contenido principal de la página -->
    </div>
    <!-- Final del contenido principal -->
    <script src="js/doa_layout.js">
    </script>
    <script src="js/doa-datos.js">
    </script>
    <script src="js/listado_tareas.js">
    </script>
</body>

</html>