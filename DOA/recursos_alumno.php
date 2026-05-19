<?php
$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar recursos...";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos Alumno | DOA</title>

    <link rel="stylesheet" href="css/doa.css">
    <link rel="stylesheet" href="css/doa-layout.css">
    <link rel="stylesheet" href="css/doa-componentes.css">
    <link rel="stylesheet" href="css/detalle_asignatura.css">
    <link rel="stylesheet" href="css/recursos-alumno.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-recursos-asignatura">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa">
            <section class="detalle-asignatura-principal">
                <div class="cabecera-detalle-asignatura">
                    <div class="cabecera-detalle-asignatura__texto">
                        <a href="detalle_asignatura.php" class="enlace-volver-asignaturas">
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
                                <span id="unidadActualTextoAsignatura">Unidad Actual</span>
                            </li>
                        </ul>
                    </div>

                    <div class="cabecera-detalle-asignatura__pestanas">
                        <nav class="pestanas-asignatura" aria-label="Secciones de la asignatura">
                            <a href="recursos_alumno.php" class="pestanas-asignatura__item pestanas-asignatura__item--activo">Recursos</a>
                            <a href="listado_tareas.html" class="pestanas-asignatura__item">Tareas</a>
                            <a href="examenes.html" class="pestanas-asignatura__item">Exámenes</a>
                            <a href="calificaciones.html" class="pestanas-asignatura__item">Calificaciones</a>
                        </nav>
                    </div>
                </div>

                <section class="bloque-recursos">
                    <div class="recursos-tema-cabecera">
                        <h2>Recursos del tema actual</h2>

                        <a href="#" class="enlace-ver-todos">
                            Ver todos<span class="ocultar-movil"> los recursos del tema</span>
                        </a>
                    </div>

                    <div class="grid-recursos-tema">
                        <a href="#" class="tarjeta-recurso-mini">
                            <div class="tarjeta-recurso-mini__icono" aria-hidden="true">
                                <img src="img/iconos/grey-file.svg" alt="">
                            </div>

                            <div class="tarjeta-recurso-mini__info">
                                <h4>Material de la Unidad</h4>
                                <span>PDF · Unidad 03</span>
                            </div>
                        </a>

                        <a href="#" class="tarjeta-recurso-mini">
                            <div class="tarjeta-recurso-mini__icono" aria-hidden="true">
                                <img src="img/iconos/grey-play.svg" alt="">
                            </div>

                            <div class="tarjeta-recurso-mini__info">
                                <h4>Clase Grabada</h4>
                                <span>Vídeo · 12 min</span>
                            </div>
                        </a>
                    </div>
                </section>

                <section class="bloque-recursos">
                    <h2>Biblioteca</h2>

                    <div class="biblioteca-layout">
                        <aside class="biblioteca-sidebar" id="menuCarpetasGeneral">
                            <div class="biblioteca-carpeta">
                                <div class="biblioteca-carpeta__titulo">
                                    <img src="img/iconos/grey-notebook.svg" alt="" aria-hidden="true">
                                    TEMARIO
                                </div>

                                <ul class="biblioteca-menu-lista">
                                    <li>
                                        <a href="#" class="carpeta-click" data-unidad="UNIDAD 01">UNIDAD 01</a>
                                    </li>

                                    <li>
                                        <a href="#" class="carpeta-click" data-unidad="UNIDAD 02">UNIDAD 02</a>
                                    </li>

                                    <li>
                                        <a href="#" class="carpeta-click activo" data-unidad="UNIDAD 03">UNIDAD 03</a>
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
                                        src="img/iconos/grey-chevron-right.svg"
                                        alt=""
                                        class="flecha-breadcrumb"
                                        id="flechaBreadcrumb"
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
                                    <button class="btn-filtrar-movil" id="btnFiltrarMovil">
                                        FILTRAR
                                        <img src="img/iconos/grey-down-arrow.svg" alt="" aria-hidden="true">
                                    </button>

                                    <div class="biblioteca-filtros" id="contenedorFiltros">
                                        <select class="input-filtro" id="filtroTipo">
                                            <option value="TODOS">TIPO: TODOS</option>
                                            <option value="PDF">PDF</option>
                                            <option value="ZIP">ZIP</option>
                                        </select>

                                        <select class="input-filtro" id="filtroEtiqueta">
                                            <option value="TODAS">ETIQUETAS: TODAS</option>
                                            <option value="Unidad actual">Unidad actual</option>
                                            <option value="Práctica">Práctica</option>
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

    <script src="js/doa-datos.js"></script>
    <script src="js/doa_layout.js"></script>
    <script src="js/recursos_alumno.js"></script>
</body>
</html>