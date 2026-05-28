<?php
$rol_pagina = "secretaria";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_secretaria.php";
$placeholder_buscador = "Buscar asignatura, profesor, alumno...";

require_once __DIR__ . "/includes/proteger_doa.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Crear asignatura | Secretaría DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/secretaria.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-secretaria pagina-crear-asignatura-secretaria">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa contenido-secretaria">
            <section class="cabecera-secretaria">
                <div class="cabecera-secretaria__texto">
                    <a class="enlace-volver-secretaria" href="asignaturas_secretaria.php">
                        <span aria-hidden="true" class="enlace-volver-secretaria__icono">
                            <img alt="" src="img/iconos/grey-chevron-right.svg">
                        </span>

                        <span>Volver a asignaturas</span>
                    </a>

                    <p class="cabecera-secretaria__eyebrow">Gestión académica</p>

                    <h1>Crear asignatura</h1>

                    <p>
                        Da de alta una nueva asignatura o grupo para que posteriormente se puedan asignar profesores y alumnos.
                    </p>
                </div>
            </section>

            <div class="grid-formulario-secretaria">
                <section class="bloque-secretaria">
                    <div class="bloque-secretaria__cabecera">
                        <div>
                            <h2>Datos de la asignatura</h2>

                            <p>
                                Completa los datos básicos de la asignatura. Después podrás asignarle profesor y alumnos.
                            </p>
                        </div>
                    </div>

                    <form class="formulario-secretaria" id="formCrearAsignatura">
                        <div class="campo-formulario-secretaria">
                            <label class="form-label" for="nombreAsignatura">
                                Nombre de la asignatura *
                            </label>

                            <input class="input" id="nombreAsignatura" type="text" placeholder="Ej. Diseño de Interfaces">

                            <p class="mensaje-error-campo" id="errorNombreAsignatura"></p>
                        </div>

                        <div class="campo-formulario-secretaria">
                            <label class="form-label" for="codigoAsignatura">
                                Código *
                            </label>

                            <input class="input" id="codigoAsignatura" type="text" placeholder="Ej. GTI-221">

                            <p class="mensaje-error-campo" id="errorCodigoAsignatura"></p>
                        </div>

                        <div class="campo-formulario-secretaria">
                            <label class="form-label" for="cursoAsignatura">
                                Curso *
                            </label>

                            <select class="input" id="cursoAsignatura">
                                <option value="">Selecciona curso</option>
                                <option value="1º">1º</option>
                                <option value="2º">2º</option>
                                <option value="3º">3º</option>
                                <option value="4º">4º</option>
                            </select>

                            <p class="mensaje-error-campo" id="errorCursoAsignatura"></p>
                        </div>

                        <div class="campo-formulario-secretaria">
                            <label class="form-label" for="grupoAsignatura">
                                Grupo *
                            </label>

                            <select class="input" id="grupoAsignatura">
                                <option value="">Selecciona grupo</option>
                                <option value="A">Grupo A</option>
                                <option value="B">Grupo B</option>
                                <option value="C">Grupo C</option>
                            </select>

                            <p class="mensaje-error-campo" id="errorGrupoAsignatura"></p>
                        </div>

                        <div class="campo-formulario-secretaria campo-formulario-secretaria--completo">
                            <label class="form-label" for="descripcionAsignatura">
                                Descripción
                            </label>

                            <textarea class="input textarea-secretaria" id="descripcionAsignatura" placeholder="Descripción breve de la asignatura..."></textarea>
                        </div>

                        <div class="campo-formulario-secretaria campo-formulario-secretaria--completo">
                            <label class="form-label" for="estadoAsignatura">
                                Estado inicial
                            </label>

                            <select class="input" id="estadoAsignatura">
                                <option value="pendiente">Pendiente de asignaciones</option>
                                <option value="activa">Activa</option>
                            </select>
                        </div>

                        <p class="mensaje-formulario-secretaria mensaje-formulario-secretaria--oculto" id="mensajeFormularioAsignatura">
                            Asignatura creada correctamente en modo demo.
                        </p>

                        <div class="acciones-formulario-secretaria">
                            <a class="boton-secretaria" href="asignaturas_secretaria.php">
                                Cancelar
                            </a>

                            <button class="boton-secretaria boton-secretaria--principal" type="submit">
                                Guardar asignatura
                            </button>
                        </div>
                    </form>
                </section>

                <aside class="lateral-secretaria">
                    <article class="tarjeta-lateral-secretaria">
                        <h3>Después de crearla</h3>

                        <div class="lista-lateral-secretaria">
                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>1. Revisar datos</strong>
                                <span>Comprueba que el código, curso y grupo son correctos.</span>
                            </div>

                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>2. Asignar profesor</strong>
                                <span>Selecciona el docente responsable desde la pantalla de asignaciones.</span>
                            </div>

                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>3. Añadir alumnos</strong>
                                <span>Matricula los alumnos correspondientes al grupo.</span>
                            </div>
                        </div>
                    </article>

                    <article class="tarjeta-lateral-secretaria">
                        <h3>Estado demo</h3>

                        <p class="texto-lateral-secretaria">
                            En esta versión PMV, el alta se simula en el navegador. Más adelante estos datos se guardarían en la base de datos.
                        </p>
                    </article>
                </aside>
            </div>
        </main>
    </div>

    <script src="js/doa_datos.js"></script>
    
    <script src="js/crear_asignatura.js"></script>
</body>
</html>