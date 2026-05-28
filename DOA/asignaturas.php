<?php
$rol_pagina = "alumno";
$pagina_activa = "panel";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar asignatura...";

require_once __DIR__ . "/includes/proteger_doa.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Mis asignaturas | DOA
    </title>
    <!-- Enlaces a hojas de estilo -->
    <link href="css/doa.css" rel="stylesheet" />
    <link href="css/doa_layout.css" rel="stylesheet" />
    <link href="css/doa_componentes.css" rel="stylesheet" />
    <link href="css/asignaturas.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet" />
    <!-- Fin enlaces a hojas de estilo -->
</head>

<body class="pagina-doa pagina-asignaturas">
    <!-- Inclusión del Header -->
    <?php include "includes/header-doa.php"; ?>
    <!-- Inicio del contenido principal -->
    <div class="layout-doa">
        <!-- Inclusión de la barra lateral -->
        <?php include "includes/barra-lateral-doa.php"; ?>
        <!-- Inicio del contenido principal de la página -->
        <main class="contenido-doa">
            <section class="cabecera-asignaturas">
                <h1>
                    Mis asignaturas
                </h1>
            </section>
            <section aria-label="Resumen de asignaturas" class="resumen-general-asignaturas">
                <article class="dato-asignaturas">
                    <button aria-controls="detalleAsignaturasActivas" aria-expanded="false" class="dato-asignaturas__boton" type="button">
                        <span class="dato-asignaturas__numero">3</span>

                        <span class="dato-asignaturas__contenido">
                            <span class="dato-asignaturas__texto">Asignaturas activas</span>
                            <span class="dato-asignaturas__accion">Ver listado</span>
                        </span>

                        <span aria-hidden="true" class="dato-asignaturas__flecha">
                            <img alt="" src="img/iconos/grey-chevron-right.svg">
                        </span>
                    </button>
                    <div class="dato-asignaturas__detalle" hidden="" id="detalleAsignaturasActivas">
                        <a class="enlace-detalle-asignatura" data-asignatura="matematicas" href="detalle_asignatura.php">
                            Matemáticas
                        </a>
                        <a class="enlace-detalle-asignatura" data-asignatura="programacion" href="detalle_asignatura.php">
                            Programación
                        </a>
                        <a class="enlace-detalle-asignatura" data-asignatura="fisica" href="detalle_asignatura.php">
                            Física
                        </a>
                    </div>
                </article>
                <article class="dato-asignaturas">
                    <button aria-controls="detalleTareasPendientes" aria-expanded="false" class="dato-asignaturas__boton" type="button">
                        <span class="dato-asignaturas__numero">2</span>

                        <span class="dato-asignaturas__contenido">
                            <span class="dato-asignaturas__texto">Tareas pendientes</span>
                            <span class="dato-asignaturas__accion">Revisar tareas</span>
                        </span>

                        <span aria-hidden="true" class="dato-asignaturas__flecha">
                            <img alt="" src="img/iconos/grey-chevron-right.svg">
                        </span>
                    </button>
                    <div class="dato-asignaturas__detalle" hidden="" id="detalleTareasPendientes">
                        <a class="enlace-detalle-asignatura" data-asignatura="programacion" href="listado_tareas.php">
                            Ejercicio recursividad
                        </a>
                        <a class="enlace-detalle-asignatura" data-asignatura="fisica" href="listado_tareas.php">
                            Ejercicio grafos
                        </a>
                    </div>
                </article>
                <article class="dato-asignaturas">
                    <button aria-controls="detalleProximaEvaluacion" aria-expanded="false" class="dato-asignaturas__boton" type="button">
                        <span class="dato-asignaturas__numero">1</span>

                        <span class="dato-asignaturas__contenido">
                            <span class="dato-asignaturas__texto">Próxima evaluación</span>
                            <span class="dato-asignaturas__accion">Ver examen</span>
                        </span>

                        <span aria-hidden="true" class="dato-asignaturas__flecha">
                            <img alt="" src="img/iconos/grey-chevron-right.svg">
                        </span>
                    </button>
                    <div class="dato-asignaturas__detalle" hidden="" id="detalleProximaEvaluacion">
                        <a class="enlace-detalle-asignatura" data-asignatura="matematicas" href="examenes.php">
                            Parcial 1 · 15 Oct, 2025
                        </a>
                    </div>
                </article>
            </section>
            <!-- Inicio del listado de asignaturas -->
            <section aria-label="Listado de asignaturas" class="lista-asignaturas">
                <article class="tarjeta-asignatura tarjeta-asignatura--activa" data-asignatura="matematicas">
                    <div class="tarjeta-asignatura__cabecera">
                        <div>
                            <h2>
                                Matemáticas
                            </h2>
                            <p>
                                Profesor: Don Pepito
                            </p>
                        </div>
                        <span class="estado-asignatura">
                            Actual
                        </span>
                    </div>
                    <div class="tarjeta-asignatura__progreso">
                        <div class="tarjeta-asignatura__progreso-texto">
                            <span>
                                Progreso de la asignatura
                            </span>
                            <strong>
                                58%
                            </strong>
                        </div>
                        <span class="barra-asignatura">
                            <span class="relleno-progreso--58">
                            </span>
                        </span>
                    </div>
                    <div class="tarjeta-asignatura__detalle">
                        <span>
                            Unidad actual
                        </span>
                        <strong>
                            Unidad 03 · Límites
                        </strong>
                    </div>
                    <div class="tarjeta-asignatura__acciones">
                        <a class="boton-asignatura boton-asignatura--principal" href="detalle_asignatura.php">
                            Entrar
                        </a>
                        <a class="boton-asignatura enlace-recurso-asignatura" href="recursos_alumno.php?materia=matematicas">
                            Recursos
                        </a>
                        <a class="boton-asignatura enlace-detalle-asignatura" data-asignatura="matematicas" href="listado_tareas.php">
                            Tareas
                        </a>
                    </div>
                </article>
                <article class="tarjeta-asignatura" data-asignatura="programacion">
                    <div class="tarjeta-asignatura__cabecera">
                        <div>
                            <h2>
                                Programación
                            </h2>
                            <p>
                                Profesor: Don Pepito
                            </p>
                        </div>
                    </div>
                    <div class="tarjeta-asignatura__progreso">
                        <div class="tarjeta-asignatura__progreso-texto">
                            <span>
                                Progreso de la asignatura
                            </span>
                            <strong>
                                46%
                            </strong>
                        </div>
                        <span class="barra-asignatura">
                            <span class="relleno-progreso--46">
                            </span>
                        </span>
                    </div>
                    <div class="tarjeta-asignatura__detalle">
                        <span>
                            Unidad actual
                        </span>
                        <strong>
                            Unidad 03 · Recursividad
                        </strong>
                    </div>
                    <div class="tarjeta-asignatura__acciones">
                        <a class="boton-asignatura boton-asignatura--principal" href="detalle_asignatura.php">
                            Entrar
                        </a>
                        <a class="boton-asignatura enlace-recurso-asignatura" href="recursos_alumno.php?materia=programacion">
                            Recursos
                        </a>
                        <a class="boton-asignatura enlace-detalle-asignatura" data-asignatura="programacion" href="listado_tareas.php">
                            Tareas
                        </a>
                    </div>
                </article>
                <article class="tarjeta-asignatura" data-asignatura="fisica">
                    <div class="tarjeta-asignatura__cabecera">
                        <div>
                            <h2>
                                Física
                            </h2>
                            <p>
                                Profesora: Eolande Merriton Mizzi
                            </p>
                        </div>
                    </div>
                    <div class="tarjeta-asignatura__progreso">
                        <div class="tarjeta-asignatura__progreso-texto">
                            <span>
                                Progreso de la asignatura
                            </span>
                            <strong>
                                42%
                            </strong>
                        </div>
                        <span class="barra-asignatura">
                            <span class="relleno-progreso--42">
                            </span>
                        </span>
                    </div>
                    <div class="tarjeta-asignatura__detalle">
                        <span>
                            Unidad actual
                        </span>
                        <strong>
                            Unidad 03 · Movimiento y fuerzas
                        </strong>
                    </div>
                    <div class="tarjeta-asignatura__acciones">
                        <a class="boton-asignatura boton-asignatura--principal" href="detalle_asignatura.php">
                            Entrar
                        </a>
                        <a class="boton-asignatura enlace-recurso-asignatura" href="recursos_alumno.php?materia=fisica">
                            Recursos
                        </a>
                        <a class="boton-asignatura enlace-detalle-asignatura" data-asignatura="fisica" href="listado_tareas.php">
                            Tareas
                        </a>
                    </div>
                </article>
            </section>
            <!-- Final del listado de asignaturas -->
        </main>
        <!-- Final del contenido principal de la página -->
    </div>
    <!-- Final del contenido principal -->

    <script src="js/doa_datos.js">
    </script>
    <script src="js/asignaturas.js">
    </script>
</body>

</html>