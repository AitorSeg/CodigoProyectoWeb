<?php
$rol_pagina = "secretaria";
$pagina_activa = "asignaciones";
$enlace_panel = "panel_secretaria.php";
$placeholder_buscador = "Buscar asignatura, profesor, alumno...";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Asignaciones | Secretaría DOA
    </title>
    <!-- Enlaces a hojas de estilo -->
    <link href="css/doa.css" rel="stylesheet" />
    <link href="css/doa-layout.css" rel="stylesheet" />
    <link href="css/doa-componentes.css" rel="stylesheet" />
    <link href="css/secretaria.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet" />
    <!-- Fin enlaces a hojas de estilo -->
</head>

<body class="pagina-doa pagina-secretaria">
    <!-- Header -->
    <?php include "includes/header-doa.php"; ?>
    <!-- Inicio del contenido principal -->
    <div class="layout-doa">
        <!-- Barra Lateral -->
        <?php include "includes/barra-lateral-doa.php"; ?>
        <!-- Inicio del contenido principal de la página -->
        <main class="contenido-doa contenido-secretaria">
            <section class="cabecera-secretaria">
                <div class="cabecera-secretaria__texto">
                    <p class="cabecera-secretaria__eyebrow">
                        Gestión académica
                    </p>
                    <h1>
                        Asignaciones
                    </h1>
                    <p>
                        Asigna profesores y alumnos a las asignaturas creadas en el sistema.
                    </p>
                </div>
            </section>
            <section aria-label="Resumen de asignaciones" class="resumen-secretaria">
                <article class="dato-secretaria dato-secretaria--principal">
                    <span class="dato-secretaria__label">
                        Asignaturas
                    </span>
                    <strong class="dato-secretaria__valor">
                        8
                    </strong>
                </article>
                <article class="dato-secretaria">
                    <span class="dato-secretaria__label">
                        Profesores disponibles
                    </span>
                    <strong class="dato-secretaria__valor">
                        12
                    </strong>
                </article>
                <article class="dato-secretaria">
                    <span class="dato-secretaria__label">
                        Alumnos disponibles
                    </span>
                    <strong class="dato-secretaria__valor">
                        214
                    </strong>
                </article>
                <article class="dato-secretaria">
                    <span class="dato-secretaria__label">
                        Pendientes
                    </span>
                    <strong class="dato-secretaria__valor">
                        3
                    </strong>
                </article>
            </section>
            <div class="grid-asignaciones-secretaria">
                <section class="bloque-secretaria">
                    <div class="bloque-secretaria__cabecera">
                        <div>
                            <h2>
                                Asignar usuarios
                            </h2>
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
                            <select class="input" id="selectAsignaturaSecretaria">
                                <option value="programacion">
                                    Programación II · Grupo A
                                </option>
                                <option value="matematicas">
                                    Matemáticas · Grupo B
                                </option>
                                <option value="fisica">
                                    Física · Grupo A
                                </option>
                                <option value="interfaces">
                                    Diseño de Interfaces · Grupo C
                                </option>
                            </select>
                        </div>
                        <div class="panel-asignacion-secretaria">
                            <div class="panel-asignacion-secretaria__cabecera">
                                <div>
                                    <h3>
                                        Profesor asignado
                                    </h3>
                                    <p>
                                        Selecciona el docente responsable de la asignatura.
                                    </p>
                                </div>
                            </div>
                            <label class="form-label" for="selectProfesorSecretaria">
                                Profesor
                            </label>
                            <select class="input" id="selectProfesorSecretaria">
                                <option value="">
                                    Sin profesor asignado
                                </option>
                                <option value="kevan">
                                    Kevan Pounds Mainston
                                </option>
                                <option value="pepito">
                                    Don Pepito
                                </option>
                                <option value="eolande">
                                    Eolande Merriton Mizzi
                                </option>
                                <option value="luelle">
                                    Luelle Pridmore Starsmeare
                                </option>
                            </select>
                        </div>
                        <div class="panel-asignacion-secretaria">
                            <div class="panel-asignacion-secretaria__cabecera">
                                <div>
                                    <h3>
                                        Alumnos asignados
                                    </h3>
                                    <p>
                                        Marca los alumnos que pertenecen a este grupo.
                                    </p>
                                </div>
                                <span class="contador-alumnos-secretaria" id="contadorAlumnosSecretaria">
                                    0 seleccionados
                                </span>
                            </div>
                            <div class="lista-alumnos-secretaria">
                                <label class="item-alumno-secretaria">
                                    <input type="checkbox" value="lief" />
                                    <span>
                                        <strong>
                                            Lief Simants
                                        </strong>
                                        <small>
                                            Alumno GTI · 2ºA
                                        </small>
                                    </span>
                                </label>
                                <label class="item-alumno-secretaria">
                                    <input type="checkbox" value="ana" />
                                    <span>
                                        <strong>
                                            Ana Torres
                                        </strong>
                                        <small>
                                            Alumno GTI · 2ºA
                                        </small>
                                    </span>
                                </label>
                                <label class="item-alumno-secretaria">
                                    <input type="checkbox" value="marc" />
                                    <span>
                                        <strong>
                                            Marc Vidal
                                        </strong>
                                        <small>
                                            Alumno GTI · 2ºA
                                        </small>
                                    </span>
                                </label>
                                <label class="item-alumno-secretaria">
                                    <input type="checkbox" value="nuria" />
                                    <span>
                                        <strong>
                                            Núria Esteve
                                        </strong>
                                        <small>
                                            Alumno GTI · 2ºB
                                        </small>
                                    </span>
                                </label>
                                <label class="item-alumno-secretaria">
                                    <input type="checkbox" value="pablo" />
                                    <span>
                                        <strong>
                                            Pablo Barceló
                                        </strong>
                                        <small>
                                            Alumno GTI · 2ºB
                                        </small>
                                    </span>
                                </label>
                                <label class="item-alumno-secretaria">
                                    <input type="checkbox" value="pedro" />
                                    <span>
                                        <strong>
                                            Pedro Fernández
                                        </strong>
                                        <small>
                                            Alumno GTI · 2ºC
                                        </small>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="mensaje-formulario-secretaria mensaje-formulario-secretaria--oculto" id="mensajeAsignacionesSecretaria">
                            Asignaciones guardadas correctamente en modo demo.
                        </div>
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
                        <h3>
                            Resumen actual
                        </h3>
                        <dl class="resumen-asignacion-secretaria">
                            <div>
                                <dt>
                                    Asignatura
                                </dt>
                                <dd id="resumenNombreAsignatura">
                                    Programación II
                                </dd>
                            </div>
                            <div>
                                <dt>
                                    Código
                                </dt>
                                <dd id="resumenCodigoAsignatura">
                                    GTI-203
                                </dd>
                            </div>
                            <div>
                                <dt>
                                    Profesor
                                </dt>
                                <dd id="resumenProfesorAsignatura">
                                    Kevan Pounds Mainston
                                </dd>
                            </div>
                            <div>
                                <dt>
                                    Alumnos
                                </dt>
                                <dd id="resumenAlumnosAsignatura">
                                    3 asignados
                                </dd>
                            </div>
                            <div>
                                <dt>
                                    Estado
                                </dt>
                                <dd>
                                    <span class="estado-secretaria estado-secretaria--completa" id="resumenEstadoAsignatura">
                                        Completa
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </article>
                    <article class="tarjeta-lateral-secretaria">
                        <h3>
                            Indicaciones
                        </h3>
                        <div class="lista-lateral-secretaria">
                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>
                                    Profesor
                                </strong>
                                <span>
                                    Cada asignatura debe tener un profesor responsable.
                                </span>
                            </div>
                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>
                                    Alumnos
                                </strong>
                                <span>
                                    Los alumnos marcados quedarán asociados al grupo seleccionado.
                                </span>
                            </div>
                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>
                                    Modo demo
                                </strong>
                                <span>
                                    Los cambios se simulan en el navegador mediante localStorage.
                                </span>
                            </div>
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
    <script src="js/doa_layout.js">
    </script>
    <script src="js/asignaciones_secretaria.js">
    </script>
</body>

</html>