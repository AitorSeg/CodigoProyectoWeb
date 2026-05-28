<?php
$rol_pagina = "secretaria";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_secretaria.php";
$placeholder_buscador = "Buscar asignatura, profesor, alumno...";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Asignaturas | Secretaría DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
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
            <section class="cabecera-secretaria cabecera-secretaria--con-accion">
                <div class="cabecera-secretaria__texto">
                    <p class="cabecera-secretaria__eyebrow">Gestión académica</p>

                    <h1>Asignaturas</h1>

                    <p>
                        Consulta las asignaturas creadas, revisa sus datos principales
                        y accede a sus asignaciones.
                    </p>
                </div>

                <a class="boton-secretaria boton-secretaria--principal" href="crear_asignatura.php">
                    Crear asignatura
                </a>
            </section>

            <section aria-label="Resumen de asignaturas" class="resumen-secretaria">
                <article class="dato-secretaria dato-secretaria--principal">
                    <span class="dato-secretaria__label">Asignaturas activas</span>
                    <strong class="dato-secretaria__valor">8</strong>
                </article>

                <article class="dato-secretaria">
                    <span class="dato-secretaria__label">Con profesor</span>
                    <strong class="dato-secretaria__valor">6</strong>
                </article>

                <article class="dato-secretaria">
                    <span class="dato-secretaria__label">Sin profesor</span>
                    <strong class="dato-secretaria__valor">2</strong>
                </article>

                <article class="dato-secretaria">
                    <span class="dato-secretaria__label">Alumnos asignados</span>
                    <strong class="dato-secretaria__valor">214</strong>
                </article>
            </section>

            <section class="bloque-secretaria">
                <div class="bloque-secretaria__cabecera">
                    <div>
                        <h2>Listado de asignaturas</h2>

                        <p>
                            Vista administrativa de las asignaturas disponibles en la demo.
                        </p>
                    </div>
                </div>

                <div class="tabla-asignaturas-secretaria">
                    <div class="tabla-asignaturas-secretaria__cabecera">
                        <span>Asignatura</span>
                        <span>Código</span>
                        <span>Curso</span>
                        <span>Profesor</span>
                        <span>Alumnos</span>
                        <span>Estado</span>
                        <span>Acciones</span>
                    </div>

                    <article class="tabla-asignaturas-secretaria__fila">
                        <div class="asignatura-secretaria-nombre">
                            <strong>Programación II</strong>
                            <small>Grupo A · Unidad 03</small>
                        </div>

                        <span>GTI-203</span>
                        <span>2º</span>
                        <span>Kevan Pounds</span>
                        <span>32</span>

                        <span>
                            <span class="estado-secretaria estado-secretaria--completa">
                                Completa
                            </span>
                        </span>

                        <div class="acciones-fila-secretaria">
                            <a class="enlace-accion-secretaria" href="asignaciones_secretaria.php">
                                Asignaciones
                            </a>

                            <a class="enlace-accion-secretaria" href="crear_asignatura.php">
                                Editar
                            </a>
                        </div>
                    </article>

                    <article class="tabla-asignaturas-secretaria__fila">
                        <div class="asignatura-secretaria-nombre">
                            <strong>Matemáticas</strong>
                            <small>Grupo B · Unidad 03</small>
                        </div>

                        <span>GTI-104</span>
                        <span>1º</span>
                        <span>Don Pepito</span>
                        <span>28</span>

                        <span>
                            <span class="estado-secretaria estado-secretaria--completa">
                                Completa
                            </span>
                        </span>

                        <div class="acciones-fila-secretaria">
                            <a class="enlace-accion-secretaria" href="asignaciones_secretaria.php">
                                Asignaciones
                            </a>

                            <a class="enlace-accion-secretaria" href="crear_asignatura.php">
                                Editar
                            </a>
                        </div>
                    </article>

                    <article class="tabla-asignaturas-secretaria__fila">
                        <div class="asignatura-secretaria-nombre">
                            <strong>Física</strong>
                            <small>Grupo A · Unidad 02</small>
                        </div>

                        <span>GTI-112</span>
                        <span>1º</span>
                        <span>Eolande Merriton</span>
                        <span>26</span>

                        <span>
                            <span class="estado-secretaria estado-secretaria--completa">
                                Completa
                            </span>
                        </span>

                        <div class="acciones-fila-secretaria">
                            <a class="enlace-accion-secretaria" href="asignaciones_secretaria.php">
                                Asignaciones
                            </a>

                            <a class="enlace-accion-secretaria" href="crear_asignatura.php">
                                Editar
                            </a>
                        </div>
                    </article>

                    <article class="tabla-asignaturas-secretaria__fila">
                        <div class="asignatura-secretaria-nombre">
                            <strong>Diseño de Interfaces</strong>
                            <small>Grupo C · Sin unidad activa</small>
                        </div>

                        <span>GTI-221</span>
                        <span>2º</span>
                        <span>Pendiente</span>
                        <span>0</span>

                        <span>
                            <span class="estado-secretaria estado-secretaria--pendiente">
                                Pendiente
                            </span>
                        </span>

                        <div class="acciones-fila-secretaria">
                            <a class="enlace-accion-secretaria" href="asignaciones_secretaria.php">
                                Asignaciones
                            </a>

                            <a class="enlace-accion-secretaria" href="crear_asignatura.php">
                                Editar
                            </a>
                        </div>
                    </article>
                </div>
            </section>
        </main>
    </div>

    <script src="js/doa_datos.js"></script>
    <script src="js/doa_layout.js"></script>
</body>
</html>