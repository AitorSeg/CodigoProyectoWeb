<?php
$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar asignatura, tarea...";
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
    <link href="css/doa-layout.css" rel="stylesheet" />
    <link href="css/doa-componentes.css" rel="stylesheet" />
    <link href="css/asignaturas_profesor.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet" />
    <!-- Fin enlaces a hojas de estilo -->
</head>

<body class="pagina-doa pagina-asignaturas-profesor">
    <!-- Inicio de el header -->
    <?php include "includes/header-doa.php"; ?>
    <!-- Final de el header -->
    <!-- Inicio del contenido principal -->
    <div class="layout-doa">
        <!-- Inicio de la barra lateral -->
        <?php include "includes/barra-lateral-doa.php"; ?>
        <!-- Final de la barra lateral -->
        <!-- Inicio del contenido principal de la página -->
        <main class="contenido-doa contenido-asignaturas-profesor">
            <section class="cabecera-asignaturas-profesor">
                <div>
                    <p class="cabecera-asignaturas-profesor__eyebrow">
                        DOCENCIA
                    </p>
                    <h1>
                        Mis asignaturas
                    </h1>
                    <p>
                        Consulta el estado general de tus grupos y accede rápidamente
                        a cada asignatura.
                    </p>
                </div>
            </section>
            <section aria-label="Resumen general del profesor" class="resumen-asignaturas-profesor">
                <article class="dato-asignaturas-profesor dato-asignaturas-profesor--principal">
                    <span class="dato-asignaturas-profesor__label">
                        Asignaturas activas
                    </span>
                    <strong class="dato-asignaturas-profesor__valor">
                        3
                    </strong>
                </article>
                <article class="dato-asignaturas-profesor">
                    <span class="dato-asignaturas-profesor__label">
                        Alumnos totales
                    </span>
                    <strong class="dato-asignaturas-profesor__valor">
                        86
                    </strong>
                </article>
                <article class="dato-asignaturas-profesor">
                    <span class="dato-asignaturas-profesor__label">
                        Entregas pendientes
                    </span>
                    <strong class="dato-asignaturas-profesor__valor">
                        29
                    </strong>
                </article>
                <article class="dato-asignaturas-profesor">
                    <span class="dato-asignaturas-profesor__label">
                        Próximos exámenes
                    </span>
                    <strong class="dato-asignaturas-profesor__valor">
                        3
                    </strong>
                </article>
            </section>

            <section class="grid-asignaturas-docente" aria-label="Listado de asignaturas del profesor">
                <article class="tarjeta-asignatura-docente">
                    <div class="tarjeta-asignatura-docente__cabecera">
                        <div>
                            <h2>Programación II</h2>
                            <p class="tarjeta-asignatura-docente__unidad">Unidad 03 · Recursividad</p>
                        </div>
                    </div>

                    <ul class="tarjeta-asignatura-docente__meta">
                        <li>32 alumnos</li>
                        <li>Aula 2.4</li>
                        <li>Lunes y miércoles</li>
                    </ul>

                    <div class="tarjeta-asignatura-docente__stats">
                        <div class="mini-dato-docente">
                            <span>Tareas activas</span>
                            <strong>3</strong>
                        </div>

                        <div class="mini-dato-docente">
                            <span>Entregas pendientes</span>
                            <strong>14</strong>
                        </div>

                        <div class="mini-dato-docente">
                            <span>Recursos</span>
                            <strong>8</strong>
                        </div>

                        <div class="mini-dato-docente">
                            <span>Próximo examen</span>
                            <strong>18 Nov</strong>
                        </div>
                    </div>

                    <div class="tarjeta-asignatura-docente__actividad">
                        <span>Última actividad</span>
                        <p>14 entregas pendientes en la tarea “Ejercicio de recursividad”.</p>
                    </div>

                    <div class="tarjeta-asignatura-docente__acciones">
                        <a
                            href="recursosdoa.html?asignatura=programacion"
                            class="boton-docente boton-docente--principal"
                            data-asignatura="programacion">
                            Entrar
                        </a>

                        <a
                            href="recursosdoa.html?asignatura=programacion"
                            class="boton-docente"
                            data-asignatura="programacion">
                            Recursos
                        </a>

                        <a
                            href="listado_tareas_profe.html?asignatura=programacion"
                            class="boton-docente"
                            data-asignatura="programacion">
                            Tareas
                        </a>
                    </div>
                </article>

                <article class="tarjeta-asignatura-docente">
                    <div class="tarjeta-asignatura-docente__cabecera">
                        <div>
                            <h2>Matemáticas</h2>
                            <p class="tarjeta-asignatura-docente__unidad">Unidad 03 · Límites</p>
                        </div>
                    </div>

                    <ul class="tarjeta-asignatura-docente__meta">
                        <li>28 alumnos</li>
                        <li>Aula 1.2</li>
                        <li>Martes y jueves</li>
                    </ul>

                    <div class="tarjeta-asignatura-docente__stats">
                        <div class="mini-dato-docente">
                            <span>Tareas activas</span>
                            <strong>2</strong>
                        </div>

                        <div class="mini-dato-docente">
                            <span>Entregas pendientes</span>
                            <strong>9</strong>
                        </div>

                        <div class="mini-dato-docente">
                            <span>Recursos</span>
                            <strong>6</strong>
                        </div>

                        <div class="mini-dato-docente">
                            <span>Próximo examen</span>
                            <strong>15 Nov</strong>
                        </div>
                    </div>

                    <div class="tarjeta-asignatura-docente__actividad">
                        <span>Última actividad</span>
                        <p>9 entregas pendientes en la tarea “Hoja de límites”.</p>
                    </div>

                    <div class="tarjeta-asignatura-docente__acciones">
                        <a
                            href="recursosdoa.html?asignatura=matematicas"
                            class="boton-docente boton-docente--principal"
                            data-asignatura="matematicas">
                            Entrar
                        </a>

                        <a
                            href="recursosdoa.html?asignatura=matematicas"
                            class="boton-docente"
                            data-asignatura="matematicas">
                            Recursos
                        </a>

                        <a
                            href="listado_tareas_profe.html?asignatura=matematicas"
                            class="boton-docente"
                            data-asignatura="matematicas">
                            Tareas
                        </a>
                    </div>
                </article>

                <article class="tarjeta-asignatura-docente">
                    <div class="tarjeta-asignatura-docente__cabecera">
                        <div>
                            <h2>Física</h2>
                            <p class="tarjeta-asignatura-docente__unidad">Unidad 02 · Movimiento</p>
                        </div>
                    </div>

                    <ul class="tarjeta-asignatura-docente__meta">
                        <li>26 alumnos</li>
                        <li>Laboratorio 3</li>
                        <li>Viernes</li>
                    </ul>

                    <div class="tarjeta-asignatura-docente__stats">
                        <div class="mini-dato-docente">
                            <span>Tareas activas</span>
                            <strong>2</strong>
                        </div>

                        <div class="mini-dato-docente">
                            <span>Entregas pendientes</span>
                            <strong>6</strong>
                        </div>

                        <div class="mini-dato-docente">
                            <span>Recursos</span>
                            <strong>4</strong>
                        </div>

                        <div class="mini-dato-docente">
                            <span>Próximo examen</span>
                            <strong>22 Nov</strong>
                        </div>
                    </div>

                    <div class="tarjeta-asignatura-docente__actividad">
                        <span>Última actividad</span>
                        <p>6 entregas pendientes en la tarea “Ejercicios de MRU”.</p>
                    </div>

                    <div class="tarjeta-asignatura-docente__acciones">
                        <a
                            href="recursosdoa.html?asignatura=fisica"
                            class="boton-docente boton-docente--principal"
                            data-asignatura="fisica">
                            Entrar
                        </a>

                        <a
                            href="recursosdoa.html?asignatura=fisica"
                            class="boton-docente"
                            data-asignatura="fisica">
                            Recursos
                        </a>

                        <a
                            href="listado_tareas_profe.html?asignatura=fisica"
                            class="boton-docente"
                            data-asignatura="fisica">
                            Tareas
                        </a>
                    </div>
                </article>
            </section>
        </main>
        <!-- Final del contenido principal de la página -->
    </div>
    <!-- Final del contenido principal -->
    <script src="js/doa_layout.js">
    </script>
    <script src="js/asignaturas_profesor.js">
    </script>
</body>

</html>