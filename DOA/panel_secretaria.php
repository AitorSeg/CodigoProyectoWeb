<?php
$rol_pagina = "secretaria";
$pagina_activa = "panel";
$enlace_panel = "panel_secretaria.php";
$placeholder_buscador = "Buscar asignatura, profesor, alumno...";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Panel de Secretaría | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa-layout.css" rel="stylesheet">
    <link href="css/doa-componentes.css" rel="stylesheet">
    <link href="css/secretaria.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-secretaria">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa contenido-secretaria">
            <section class="cabecera-secretaria">
                <div class="cabecera-secretaria__texto">
                    <p class="cabecera-secretaria__eyebrow">Panel de Secretaría</p>

                    <h1>Buenos días, Ondrea</h1>

                    <p>
                        Gestiona las asignaturas del centro y asigna profesores y alumnos
                        a los grupos correspondientes.
                    </p>
                </div>
            </section>

            <section class="resumen-secretaria" aria-label="Resumen de Secretaría">
                <article class="dato-secretaria dato-secretaria--principal">
                    <span class="dato-secretaria__label">Asignaturas creadas</span>
                    <strong class="dato-secretaria__valor">8</strong>
                </article>

                <article class="dato-secretaria">
                    <span class="dato-secretaria__label">Profesores asignados</span>
                    <strong class="dato-secretaria__valor">12</strong>
                </article>

                <article class="dato-secretaria">
                    <span class="dato-secretaria__label">Alumnos matriculados</span>
                    <strong class="dato-secretaria__valor">214</strong>
                </article>

                <article class="dato-secretaria">
                    <span class="dato-secretaria__label">Asignaciones pendientes</span>
                    <strong class="dato-secretaria__valor">3</strong>
                </article>
            </section>

            <div class="grid-secretaria">
                <section class="zona-principal-secretaria">
                    <article class="bloque-secretaria">
                        <div class="bloque-secretaria__cabecera">
                            <div>
                                <h2>Gestión rápida</h2>
                                <p>Accede a las acciones principales de Secretaría.</p>
                            </div>
                        </div>

                        <div class="acciones-secretaria">
                            <a class="tarjeta-accion-secretaria" href="crear_asignatura.php">
                                <span class="tarjeta-accion-secretaria__icono" aria-hidden="true">
                                    <img src="img/iconos/blue-notebook.svg" alt="">
                                </span>

                                <span class="tarjeta-accion-secretaria__texto">
                                    <strong>Crear asignatura</strong>
                                    <small>Alta de una nueva asignatura o grupo.</small>
                                </span>
                            </a>

                            <a class="tarjeta-accion-secretaria" href="asignaciones_secretaria.php">
                                <span class="tarjeta-accion-secretaria__icono" aria-hidden="true">
                                    <img src="img/iconos/blue-user.svg" alt="">
                                </span>

                                <span class="tarjeta-accion-secretaria__texto">
                                    <strong>Asignar usuarios</strong>
                                    <small>Vincular profesores y alumnos a una asignatura.</small>
                                </span>
                            </a>
                        </div>
                    </article>

                    <article class="bloque-secretaria">
                        <div class="bloque-secretaria__cabecera">
                            <div>
                                <h2>Últimas asignaturas creadas</h2>
                                <p>Revisión rápida de las asignaturas añadidas recientemente.</p>
                            </div>

                            <a class="bloque-secretaria__enlace" href="asignaturas_secretaria.php">
                                Ver asignaturas
                            </a>
                        </div>

                        <div class="tabla-simple-secretaria">
                            <div class="tabla-simple-secretaria__cabecera">
                                <span>Asignatura</span>
                                <span>Código</span>
                                <span>Profesor</span>
                                <span>Alumnos</span>
                            </div>

                            <a class="tabla-simple-secretaria__fila" href="asignaturas_secretaria.php">
                                <span>
                                    <strong>Programación II</strong>
                                    <small>Grupo A</small>
                                </span>
                                <span>GTI-203</span>
                                <span>Kevan Pounds</span>
                                <span>32</span>
                            </a>

                            <a class="tabla-simple-secretaria__fila" href="asignaturas_secretaria.php">
                                <span>
                                    <strong>Matemáticas</strong>
                                    <small>Grupo B</small>
                                </span>
                                <span>GTI-104</span>
                                <span>Don Pepito</span>
                                <span>28</span>
                            </a>

                            <a class="tabla-simple-secretaria__fila" href="asignaturas_secretaria.php">
                                <span>
                                    <strong>Física</strong>
                                    <small>Grupo A</small>
                                </span>
                                <span>GTI-112</span>
                                <span>Eolande Merriton</span>
                                <span>26</span>
                            </a>
                        </div>
                    </article>
                </section>

                <aside class="lateral-secretaria">
                    <article class="tarjeta-lateral-secretaria">
                        <h3>Asignaciones pendientes</h3>

                        <div class="lista-lateral-secretaria">
                            <a class="item-lateral-secretaria" href="asignaciones_secretaria.php">
                                <strong>Diseño de Interfaces</strong>
                                <span>Profesor pendiente de asignar</span>
                            </a>

                            <a class="item-lateral-secretaria" href="asignaciones_secretaria.php">
                                <strong>Animación 3D</strong>
                                <span>12 alumnos sin grupo</span>
                            </a>

                            <a class="item-lateral-secretaria" href="asignaciones_secretaria.php">
                                <strong>Proyecto Web</strong>
                                <span>Grupo pendiente de revisión</span>
                            </a>
                        </div>
                    </article>

                    <article class="tarjeta-lateral-secretaria">
                        <h3>Actividad reciente</h3>

                        <div class="lista-lateral-secretaria">
                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>Programación II</strong>
                                <span>Se han asignado 32 alumnos.</span>
                            </div>

                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>Matemáticas</strong>
                                <span>Profesor actualizado correctamente.</span>
                            </div>

                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>Física</strong>
                                <span>Asignatura creada en el sistema.</span>
                            </div>
                        </div>
                    </article>
                </aside>
            </div>
        </main>
    </div>

    <script src="js/doa-datos.js"></script>
    <script src="js/doa_layout.js"></script>
</body>

</html>