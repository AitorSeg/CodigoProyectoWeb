<?php
$rol_pagina = "secretaria";
$pagina_activa = "asignaciones";
$enlace_panel = "panel_secretaria.php";
$placeholder_buscador = "Buscar asignatura, profesor, alumno...";

require_once __DIR__ . "/includes/proteger_doa.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Asignaciones | Secretaría DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/secretaria.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-secretaria pagina-asignaciones-secretaria">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa contenido-secretaria">
            <section class="cabecera-secretaria">
                <div class="cabecera-secretaria__texto">
                    <p class="cabecera-secretaria__eyebrow">Gestión académica</p>

                    <h1>Asignaciones</h1>

                    <p>
                        Asigna profesores y alumnos a las asignaturas creadas en el sistema.
                    </p>
                </div>
            </section>

            <section aria-label="Resumen de asignaciones" class="resumen-secretaria">
                <article class="dato-secretaria dato-secretaria--principal">
                    <span class="dato-secretaria__label">Asignaturas</span>
                    <strong class="dato-secretaria__valor" id="totalAsignaturasSecretaria">0</strong>
                </article>

                <article class="dato-secretaria">
                    <span class="dato-secretaria__label">Profesores disponibles</span>
                    <strong class="dato-secretaria__valor" id="totalProfesoresSecretaria">0</strong>
                </article>

                <article class="dato-secretaria">
                    <span class="dato-secretaria__label">Alumnos disponibles</span>
                    <strong class="dato-secretaria__valor" id="totalAlumnosSecretaria">0</strong>
                </article>

                <article class="dato-secretaria">
                    <span class="dato-secretaria__label">Pendientes</span>
                    <strong class="dato-secretaria__valor" id="totalPendientesSecretaria">0</strong>
                </article>
            </section>

            <div class="grid-asignaciones-secretaria">
                <section class="bloque-secretaria">
                    <div class="bloque-secretaria__cabecera">
                        <div>
                            <h2>Asignar usuarios</h2>

                            <p>
                                Selecciona una asignatura y configura el profesor responsable y los alumnos del grupo.
                            </p>
                        </div>
                    </div>

                    <form class="formulario-asignaciones-secretaria" id="formAsignacionesSecretaria">
                        <div class="campo-formulario-secretaria campo-formulario-secretaria--completo">
                            <label class="form-label" for="selectAsignaturaSecretaria">
                                Asignatura *
                            </label>

                            <select class="input" id="selectAsignaturaSecretaria"></select>
                        </div>

                        <div class="panel-asignacion-secretaria">
                            <div class="panel-asignacion-secretaria__cabecera">
                                <div>
                                    <h3>Profesor asignado</h3>

                                    <p>
                                        Selecciona el docente responsable de la asignatura.
                                    </p>
                                </div>
                            </div>

                            <label class="form-label" for="selectProfesorSecretaria">
                                Profesor
                            </label>

                            <select class="input" id="selectProfesorSecretaria"></select>
                        </div>

                        <div class="panel-asignacion-secretaria">
                            <div class="panel-asignacion-secretaria__cabecera">
                                <div>
                                    <h3>Alumnos asignados</h3>

                                    <p>
                                        Marca los alumnos que pertenecen a este grupo.
                                    </p>
                                </div>

                                <span class="contador-alumnos-secretaria" id="contadorAlumnosSecretaria">
                                    0 seleccionados
                                </span>
                            </div>

                            <div class="lista-alumnos-secretaria" id="listaAlumnosSecretaria"></div>
                        </div>

                        <p class="mensaje-formulario-secretaria mensaje-formulario-secretaria--oculto" id="mensajeAsignacionesSecretaria">
                            Asignaciones guardadas correctamente en modo demo.
                        </p>

                        <div class="acciones-formulario-secretaria">
                            <a class="boton-secretaria" href="asignaturas_secretaria.php">
                                Volver a asignaturas
                            </a>

                            <button class="boton-secretaria boton-secretaria--principal" type="submit">
                                Guardar asignaciones
                            </button>
                        </div>
                    </form>
                </section>

                <aside class="lateral-secretaria">
                    <article class="tarjeta-lateral-secretaria">
                        <h3>Resumen actual</h3>

                        <dl class="resumen-asignacion-secretaria">
                            <div>
                                <dt>Asignatura</dt>
                                <dd id="resumenNombreAsignatura">-</dd>
                            </div>

                            <div>
                                <dt>Código</dt>
                                <dd id="resumenCodigoAsignatura">-</dd>
                            </div>

                            <div>
                                <dt>Profesor</dt>
                                <dd id="resumenProfesorAsignatura">Pendiente</dd>
                            </div>

                            <div>
                                <dt>Alumnos</dt>
                                <dd id="resumenAlumnosAsignatura">0 asignados</dd>
                            </div>

                            <div>
                                <dt>Estado</dt>
                                <dd>
                                    <span class="estado-secretaria estado-secretaria--pendiente" id="resumenEstadoAsignatura">
                                        Pendiente
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </article>

                    <article class="tarjeta-lateral-secretaria">
                        <h3>Indicaciones</h3>

                        <div class="lista-lateral-secretaria">
                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>Profesor</strong>
                                <span>Cada asignatura debe tener un profesor responsable.</span>
                            </div>

                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>Alumnos</strong>
                                <span>Los alumnos marcados quedarán asociados al grupo seleccionado.</span>
                            </div>

                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>Modo demo</strong>
                                <span>Los cambios se simulan en el navegador mediante localStorage.</span>
                            </div>
                        </div>
                    </article>
                </aside>
            </div>
        </main>
    </div>

    <script src="js/asignaciones_secretaria.js"></script>
</body>
</html>