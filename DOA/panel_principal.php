<?php
$rol_pagina = "alumno";
$pagina_activa = "panel";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar asignatura...";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Panel principal | DOA
    </title>
    <!-- Enlaces a hojas de estilo -->
    <link href="css/doa.css" rel="stylesheet" />
    <link href="css/doa_layout.css" rel="stylesheet" />
    <link href="css/doa_componentes.css" rel="stylesheet" />
    <link href="css/panel_principal.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet" />
    <!-- Fin enlaces a hojas de estilo -->
</head>

<body class="pagina-doa pagina-panel-principal">
    <!-- Header -->
    <?php include "includes/header-doa.php"; ?>
    <!-- Inicio del contenido principal -->
    <div class="layout-doa">
        <!-- Barra Lateral -->
        <?php include "includes/barra-lateral-doa.php"; ?>
        <!-- Inicio del contenido principal de la página -->
        <main class="contenido-doa contenido-panel-principal">
            <div class="panel-principal-grid">
                <!-- Inicio del bloque del panel principal -->
                <section class="bloque-panel-principal">
                    <div class="cabecera-bloque-panel">
                        <h1>
                            Mis asignaturas
                        </h1>
                        <a class="cabecera-bloque-panel__enlace" href="asignaturas.php">
                            VER TODAS LAS ASIGNATURAS
                        </a>
                    </div>
                    <div class="resumen-asignaturas">
                        <button class="tarjeta-asignatura-resumen tarjeta-asignatura-resumen--activa" data-asignatura="matematicas" type="button">
                            <span class="tarjeta-asignatura-resumen__nombre">
                                Matemáticas
                            </span>
                            <span aria-hidden="true" class="tarjeta-asignatura-resumen__punto">
                            </span>
                            <span aria-hidden="true" class="tarjeta-asignatura-resumen__barra">
                                <span class="relleno-progreso--58">
                                </span>
                            </span>
                        </button>
                        <button class="tarjeta-asignatura-resumen" data-asignatura="programacion" type="button">
                            <span class="tarjeta-asignatura-resumen__nombre">
                                Programación
                            </span>
                            <span aria-hidden="true" class="tarjeta-asignatura-resumen__punto">
                            </span>
                            <span aria-hidden="true" class="tarjeta-asignatura-resumen__barra">
                                <span class="relleno-progreso--46">
                                </span>
                            </span>
                        </button>
                        <button class="tarjeta-asignatura-resumen tarjeta-asignatura-resumen--ocultar-movil" data-asignatura="fisica" type="button">
                            <span class="tarjeta-asignatura-resumen__nombre">
                                Física
                            </span>
                            <span aria-hidden="true" class="tarjeta-asignatura-resumen__punto">
                            </span>
                            <span aria-hidden="true" class="tarjeta-asignatura-resumen__barra">
                                <span class="relleno-progreso--42">
                                </span>
                            </span>
                        </button>
                    </div>
                    <div class="lista-progresos-asignaturas" id="listaProgresosAsignaturas">
                        <article class="tarjeta-progreso-asignatura" data-asignatura="matematicas">
                            <div class="tarjeta-progreso-asignatura__cabecera">
                                <h2>
                                    Progreso Matemáticas
                                </h2>
                                <a class="boton-entrar-asignatura" href="detalle_asignatura.php">
                                    Entrar
                                </a>
                            </div>
                            <div aria-label="Progreso de Matemáticas" class="progreso-asignatura progreso-asignatura--avance-40-33">
                                <span class="progreso-asignatura__destello" aria-hidden="true"></span>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada progreso-asignatura__unidad--ocultar-movil">
                                    <span class="progreso-asignatura__badge">
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/blue-check.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 01
                                    </a>
                                </div>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada">
                                    <span class="progreso-asignatura__badge">
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/blue-check.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 02
                                    </a>
                                </div>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--actual">
                                    <span class="progreso-asignatura__badge">
                                        Actual
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/blue-play.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 03
                                    </a>
                                </div>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada">
                                    <span class="progreso-asignatura__badge">
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/grey-x.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 04
                                    </a>
                                </div>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada progreso-asignatura__unidad--ocultar-movil">
                                    <span class="progreso-asignatura__badge">
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/grey-x.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 05
                                    </a>
                                </div>
                            </div>
                        </article>
                        <article class="tarjeta-progreso-asignatura tarjeta-progreso-asignatura--secundaria" data-asignatura="programacion">
                            <div class="tarjeta-progreso-asignatura__cabecera">
                                <h2>
                                    Progreso Programación
                                </h2>
                                <a class="boton-entrar-asignatura" href="detalle_asignatura.php">
                                    Entrar
                                </a>
                            </div>
                            <div aria-label="Progreso de Programación" class="progreso-asignatura progreso-asignatura--avance-40-333">
                                <span class="progreso-asignatura__destello" aria-hidden="true"></span>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada progreso-asignatura__unidad--ocultar-movil">
                                    <span class="progreso-asignatura__badge">
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/grey-check.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 01
                                    </a>
                                </div>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada">
                                    <span class="progreso-asignatura__badge">
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/grey-check.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 02
                                    </a>
                                </div>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--actual">
                                    <span class="progreso-asignatura__badge">
                                        Actual
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/grey-play.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 03
                                    </a>
                                </div>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada">
                                    <span class="progreso-asignatura__badge">
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/grey-x.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 04
                                    </a>
                                </div>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada progreso-asignatura__unidad--ocultar-movil">
                                    <span class="progreso-asignatura__badge">
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/grey-x.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 05
                                    </a>
                                </div>
                            </div>
                        </article>
                        <article class="tarjeta-progreso-asignatura tarjeta-progreso-asignatura--secundaria tarjeta-progreso-asignatura--ocultar-movil" data-asignatura="fisica">
                            <div class="tarjeta-progreso-asignatura__cabecera">
                                <h2>
                                    Progreso Física
                                </h2>
                                <a class="boton-entrar-asignatura" href="detalle_asignatura.php">
                                    Entrar
                                </a>
                            </div>
                            <div aria-label="Progreso de Física" class="progreso-asignatura progreso-asignatura--avance-40-333">
                                <span class="progreso-asignatura__destello" aria-hidden="true"></span>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada progreso-asignatura__unidad--ocultar-movil">
                                    <span class="progreso-asignatura__badge">
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/grey-check.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 01
                                    </a>
                                </div>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada">
                                    <span class="progreso-asignatura__badge">
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/grey-check.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 02
                                    </a>
                                </div>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--actual">
                                    <span class="progreso-asignatura__badge">
                                        Actual
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/grey-play.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 03
                                    </a>
                                </div>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada">
                                    <span class="progreso-asignatura__badge">
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/grey-x.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 04
                                    </a>
                                </div>
                                <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada progreso-asignatura__unidad--ocultar-movil">
                                    <span class="progreso-asignatura__badge">
                                    </span>
                                    <span aria-hidden="true" class="progreso-asignatura__estado">
                                        <img alt="" src="img/iconos/grey-x.svg" />
                                    </span>
                                    <a class="progreso-asignatura__nombre" href="#">
                                        Unidad 05
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
                <!-- Final del bloque del panel principal -->
                <div class="panel-derecho">
                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">
                            Próxima evaluación
                        </p>
                        <div class="tarjeta-lateral-panel__contenido">
                            <h2>
                                Parcial 1
                            </h2>
                            <ul class="lista-detalles-panel">
                                <li>
                                    <img alt="" src="img/iconos/grey-calendar.svg" />
                                    <span>
                                        15 Oct, 2025
                                    </span>
                                </li>
                                <li>
                                    <img alt="" src="img/iconos/grey-clock.svg" />
                                    <span>
                                        10:00 AM
                                    </span>
                                </li>
                                <li>
                                    <img alt="" src="img/iconos/grey-map-pin.svg" />
                                    <span>
                                        Edificio G, Aula 6
                                    </span>
                                </li>
                            </ul>
                            <a class="boton-secundario-panel" href="#">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">
                            Tarea activa
                        </p>
                        <div class="tarjeta-lateral-panel__contenido">
                            <h2>
                                Ejercicio recursividad
                            </h2>
                            <p class="texto-vencimiento">
                                Vence en:
                                <strong>
                                    2 días
                                </strong>
                            </p>
                            <a class="boton-secundario-panel" href="#">
                                Ir a la tarea
                            </a>
                        </div>
                    </div>
                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">
                            Tarea activa
                        </p>
                        <div class="tarjeta-lateral-panel__contenido">
                            <h2>
                                Ejercicio grafos
                            </h2>
                            <p class="texto-vencimiento">
                                Vence en:
                                <strong>
                                    5 días
                                </strong>
                            </p>
                            <a class="boton-secundario-panel" href="#">
                                Ir a la tarea
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- Final del contenido principal de la página -->
    </div>
    <!-- Final del contenido principal -->
    <script src="js/doa_layout.js">
    </script>
    <script src="js/doa_datos.js">
    </script>
    <script src="js/panel_principal.js">
    </script>
</body>

</html>