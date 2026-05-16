<?php
    $rol_pagina = "profesor";
    $pagina_activa = "panel";
    $enlace_panel = "panel_profesor.php";
    $placeholder_buscador = "Buscar asignatura, tarea...";
?>
<!DOCTYPE html>
<html lang="es">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>
   Panel del profesor | DOA
  </title>
  <!-- Enlaces a hojas de estilo -->
  <link href="css/doa.css" rel="stylesheet"/>
  <link href="css/doa-layout.css" rel="stylesheet"/>
  <link href="css/doa-componentes.css" rel="stylesheet"/>
  <link href="css/panel_profesor.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com" rel="preconnect"/>
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet"/>
  <!-- Fin enlaces a hojas de estilo -->
 </head>
 <body class="pagina-doa pagina-panel-profesor">
  <!-- Inicio de el header -->
    <?php include "includes/header-doa.php"; ?>
  <!-- Final de el header -->
  <!-- Inicio del contenido principal -->
  <div class="layout-doa">
   <!-- Inicio de la barra lateral -->
    <?php include "includes/barra-lateral-doa.php"; ?>
   <!-- Final de la barra lateral -->
   <!-- Inicio del contenido principal de la página -->
        <main class="contenido-doa contenido-panel-profesor">
            <section class="cabecera-panel-profesor">
                <div class="cabecera-panel-profesor__texto">
                    <p class="cabecera-panel-profesor__eyebrow">Panel del profesor</p>

                    <h1>Buenos días, Kevan</h1>

                    <p>
                        Gestiona tus asignaturas, revisa entregas pendientes y consulta el estado
                        actual de tus grupos.
                    </p>
                </div>
            </section>

            <section class="resumen-panel-profesor" aria-label="Resumen general del profesor">
                <article class="dato-panel-profesor">
                    <span class="dato-panel-profesor__label">Asignaturas</span>
                    <strong class="dato-panel-profesor__valor">3</strong>
                </article>

                <article class="dato-panel-profesor">
                    <span class="dato-panel-profesor__label">Tareas activas</span>
                    <strong class="dato-panel-profesor__valor">7</strong>
                </article>

                <article class="dato-panel-profesor">
                    <span class="dato-panel-profesor__label">Entregas pendientes</span>
                    <strong class="dato-panel-profesor__valor">29</strong>
                </article>

                <article class="dato-panel-profesor">
                    <span class="dato-panel-profesor__label">Recursos publicados</span>
                    <strong class="dato-panel-profesor__valor">18</strong>
                </article>
            </section>

            <div class="panel-profesor-grid">
                <section class="panel-profesor-principal">
                    <article class="bloque-panel-profesor">
                        <div class="bloque-panel-profesor__cabecera">
                            <div>
                                <h2>Asignaturas destacadas</h2>
                                <p>
                                    Vista rápida de las asignaturas con más actividad reciente.
                                </p>
                            </div>

                            <a href="asignaturas_profesor.html" class="bloque-panel-profesor__enlace">
                                Ver mis asignaturas
                            </a>
                        </div>

                        <div class="grid-asignaturas-profesor">
                            <article class="tarjeta-asignatura-profesor tarjeta-asignatura-profesor--activa">
                                <div class="tarjeta-asignatura-profesor__cabecera">
                                    <div>
                                        <h3>Programación II</h3>
                                        <p>Unidad 03 · Recursividad</p>
                                    </div>

                                    <span class="estado-asignatura-docente">Activa</span>
                                </div>

                                <div class="tarjeta-asignatura-profesor__stats">
                                    <div class="mini-stat-profesor">
                                        <span>Alumnos</span>
                                        <strong>32</strong>
                                    </div>

                                    <div class="mini-stat-profesor">
                                        <span>Tareas</span>
                                        <strong>3</strong>
                                    </div>

                                    <div class="mini-stat-profesor">
                                        <span>Entregas</span>
                                        <strong>14</strong>
                                    </div>
                                </div>

                                <div class="tarjeta-asignatura-profesor__acciones">
                                    <a href="recursosdoa.html?asignatura=programacion" class="boton-docente boton-docente--principal">
                                        Entrar
                                    </a>
                                </div>
                            </article>

                            <article class="tarjeta-asignatura-profesor">
                                <div class="tarjeta-asignatura-profesor__cabecera">
                                    <div>
                                        <h3>Matemáticas</h3>
                                        <p>Unidad 03 · Límites</p>
                                    </div>
                                </div>

                                <div class="tarjeta-asignatura-profesor__stats">
                                    <div class="mini-stat-profesor">
                                        <span>Alumnos</span>
                                        <strong>28</strong>
                                    </div>

                                    <div class="mini-stat-profesor">
                                        <span>Tareas</span>
                                        <strong>2</strong>
                                    </div>

                                    <div class="mini-stat-profesor">
                                        <span>Entregas</span>
                                        <strong>9</strong>
                                    </div>
                                </div>

                                <div class="tarjeta-asignatura-profesor__acciones">
                                    <a href="recursosdoa.html?asignatura=matematicas" class="boton-docente boton-docente--principal">
                                        Entrar
                                    </a>
                                </div>
                            </article>
                        </div>
                    </article>
                </section>

                <aside class="panel-profesor-lateral">
                    <article class="tarjeta-panel-lateral">
                        <div class="tarjeta-panel-lateral__cabecera">
                            <h3>Tareas activas</h3>
                        </div>

                        <div class="lista-panel-lateral">
                            <a href="crear_tarea.html?asignatura=programacion" class="item-panel-lateral">
                                <div class="item-panel-lateral__texto">
                                    <strong>Ejercicio de recursividad</strong>
                                    <span>Programación II · 14 entregas pendientes</span>
                                </div>

                                <small>Vence en 2 días</small>
                            </a>

                            <a href="crear_tarea.html?asignatura=matematicas" class="item-panel-lateral">
                                <div class="item-panel-lateral__texto">
                                    <strong>Hoja de límites</strong>
                                    <span>Matemáticas · 9 entregas pendientes</span>
                                </div>

                                <small>Vence mañana</small>
                            </a>

                            <a href="crear_tarea.html?asignatura=fisica" class="item-panel-lateral">
                                <div class="item-panel-lateral__texto">
                                    <strong>Ejercicios de MRU</strong>
                                    <span>Física · 6 entregas pendientes</span>
                                </div>

                                <small>Vence en 4 días</small>
                            </a>
                        </div>
                    </article>

                    <article class="tarjeta-panel-lateral">
                        <div class="tarjeta-panel-lateral__cabecera">
                            <h3>Próximos exámenes</h3>
                        </div>

                        <div class="lista-panel-lateral">
                            <a href="crearexamen.html?asignatura=matematicas" class="item-panel-lateral">
                                <div class="item-panel-lateral__texto">
                                    <strong>Parcial 01</strong>
                                    <span>Matemáticas</span>
                                </div>

                                <small>15 Nov</small>
                            </a>

                            <a href="crearexamen.html?asignatura=programacion" class="item-panel-lateral">
                                <div class="item-panel-lateral__texto">
                                    <strong>Quiz de punteros</strong>
                                    <span>Programación II</span>
                                </div>

                                <small>18 Nov</small>
                            </a>

                            <a href="crearexamen.html?asignatura=fisica" class="item-panel-lateral">
                                <div class="item-panel-lateral__texto">
                                    <strong>Control de cinemática</strong>
                                    <span>Física</span>
                                </div>

                                <small>22 Nov</small>
                            </a>
                        </div>
                    </article>
                </aside>
            </div>
        </main>
   <!-- Final del contenido principal de la página -->
  </div>
  <!-- Final del contenido principal -->
  <script src="js/doa-datos.js">
  </script>
  <script src="js/doa-layout.js">
  </script>
 </body>
</html>
