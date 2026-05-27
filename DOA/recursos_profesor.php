<?php
$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar recursos...";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Recursos | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa-layout.css" rel="stylesheet">
    <link href="css/doa-componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/recursos-alumno.css" rel="stylesheet">
    <link href="css/recursos-profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-recursos-asignatura pagina-recursos-profesor">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa">
            <section class="detalle-asignatura-principal">
                <div class="cabecera-detalle-asignatura">
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
                            <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="recursosdoa.php" id="linkPestanaRecursos">
                                Recursos
                            </a>

                            <a class="pestanas-asignatura__item" href="listado_tareas_profe.html" id="linkPestanaTareas">
                                Tareas
                            </a>

                            <a class="pestanas-asignatura__item" href="examenes_profesor.html" id="linkPestanaExamenes">
                                Exámenes
                            </a>

                            <a class="pestanas-asignatura__item" href="calificaciones_profesor.php" id="linkPestanaCalificaciones">
                                Calificaciones
                            </a>
                        </nav>
                    </div>
                </div>

                <section class="bloque-recursos">
                    <div class="card tarjeta-subida-recurso">
                        <div class="tarjeta-subida-recurso__cabecera">
                            <div>
                                <h2 class="tarjeta-subida-recurso__titulo">Subir recurso</h2>

                                <p class="tarjeta-subida-recurso__texto">
                                    Añade un nuevo recurso a la biblioteca de la unidad seleccionada.
                                </p>
                            </div>
                        </div>

                        <form class="formulario-recursos-grid" id="formSubirRecurso">
                            <div>
                                <label class="form-label" for="tituloRecurso">Título del recurso *</label>

                                <input
                                    class="input"
                                    id="tituloRecurso"
                                    type="text"
                                    placeholder="Ej. Presentación Unidad 03"
                                    required
                                >
                            </div>

                            <div>
                                <label class="form-label" for="archivoRecurso">Archivo *</label>

                                <input
                                    class="input input-archivo-recurso"
                                    id="archivoRecurso"
                                    type="file"
                                    required
                                >
                            </div>

                            <div class="formulario-recursos-grid__acciones">
                                <button class="btn btn-primary boton-guardar-recurso" type="submit">
                                    Guardar recurso
                                </button>
                            </div>
                        </form>

                        <p class="mensaje-recurso-subido mensaje-recurso-subido--oculto" id="mensajeRecursoSubido">
                            Recurso añadido correctamente.
                        </p>
                    </div>
                </section>

                <section class="bloque-recursos">
                    <h2>Biblioteca de la asignatura</h2>

                    <div class="biblioteca-layout">
                        <aside class="biblioteca-sidebar" id="menuCarpetasGeneral">
                            <div class="biblioteca-carpeta">
                                <div class="biblioteca-carpeta__titulo">
                                    <img src="img/iconos/grey-notebook.svg" alt="" aria-hidden="true">
                                    TEMARIO
                                </div>

                                <ul class="biblioteca-menu-lista">
                                    <li>
                                        <a class="carpeta-click" href="#" data-unidad="UNIDAD 01">UNIDAD 01</a>
                                    </li>

                                    <li>
                                        <a class="carpeta-click" href="#" data-unidad="UNIDAD 02">UNIDAD 02</a>
                                    </li>

                                    <li>
                                        <a class="carpeta-click activo" href="#" data-unidad="UNIDAD 03">UNIDAD 03</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="biblioteca-carpeta">
                                <div class="biblioteca-carpeta__titulo carpeta-click" data-unidad="PRÁCTICAS">
                                    <img src="img/iconos/grey-file.svg" alt="" aria-hidden="true">
                                    PRÁCTICAS
                                </div>
                            </div>

                            <div class="biblioteca-carpeta">
                                <div class="biblioteca-carpeta__titulo carpeta-click" data-unidad="EXÁMENES">
                                    <img src="img/iconos/grey-check.svg" alt="" aria-hidden="true">
                                    EXÁMENES
                                </div>
                            </div>
                        </aside>

                        <div class="biblioteca-contenido">
                            <div class="biblioteca-toolbar">
                                <div class="biblioteca-breadcrumb" id="btnNavegacionMovil">
                                    <span id="textoCarpetaPadre">TEMARIO</span>

                                    <img
                                        class="flecha-breadcrumb"
                                        id="flechaBreadcrumb"
                                        src="img/iconos/grey-chevron-right.svg"
                                        alt=""
                                        aria-hidden="true"
                                    >

                                    <strong id="textoBreadcrumb">UNIDAD 03</strong>

                                    <img
                                        class="icono-desplegable-movil"
                                        src="img/iconos/grey-down-arrow.svg"
                                        alt=""
                                        aria-hidden="true"
                                    >
                                </div>

                                <div class="contenedor-filtros-wrapper">
                                    <button class="btn-filtrar-movil" id="btnFiltrarMovil" type="button">
                                        FILTRAR
                                        <img src="img/iconos/grey-down-arrow.svg" alt="" aria-hidden="true">
                                    </button>

                                    <div class="biblioteca-filtros" id="contenedorFiltros">
                                        <select class="input-filtro" id="filtroTipo">
                                            <option value="TODOS">TIPO: TODOS</option>
                                            <option value="PDF">PDF</option>
                                            <option value="ZIP">ZIP</option>
                                            <option value="PPTX">PPTX</option>
                                        </select>

                                        <select class="input-filtro" id="filtroEtiqueta">
                                            <option value="TODAS">ETIQUETAS: TODAS</option>
                                            <option value="Unidad actual">Unidad actual</option>
                                            <option value="Práctica">Práctica</option>
                                            <option value="Importante">Importante</option>
                                            <option value="Nuevo">Nuevo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="biblioteca-tabla">
                                <div class="biblioteca-tabla-header">
                                    <span>NOMBRE</span>
                                    <span>TIPO</span>
                                    <span>TAMAÑO</span>
                                    <span>ETIQUETAS</span>
                                    <span>FECHA</span>
                                </div>

                                <div id="cuerpoTablaArchivos"></div>
                            </div>
                        </div>
                    </div>
                </section>
            </section>
        </main>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/doa-datos.js"></script>
    <script src="js/recursos_profesor.js"></script>
</body>
</html>