<?php
// Inicio configuración de página

$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar tarea...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página


// Inicio parámetros de pantalla

if (!isset($_GET["id_asignatura"]) && !isset($_POST["id_asignatura"])) {
    header("Location: asignaturas_profesor.php");
    exit;
}

$id_profesor = (int) $_SESSION["doa_id_usuario"];
$id_asignatura = isset($_POST["id_asignatura"])
    ? (int) $_POST["id_asignatura"]
    : (int) $_GET["id_asignatura"];

$errores = [];
$mensaje_ok = "";

$datos_tarea = [
    "titulo" => "",
    "tipo_actividad" => "tarea",
    "unidad" => "Unidad 03",
    "descripcion" => "",
    "fecha_inicio" => date("Y-m-d"),
    "fecha_limite" => "",
    "estado" => "publicada"
];

if (isset($_GET["guardada"]) && $_GET["guardada"] === "ok") {
    $mensaje_ok = "Tarea guardada correctamente.";
}

// Fin parámetros de pantalla


// Inicio consulta de asignatura

$consulta_asignatura = $pdo->prepare("
    SELECT
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.curso,
        a.grupo,
        COUNT(DISTINCT ua_alumno.id_usuario) AS total_alumnos
    FROM asignaturas a
    INNER JOIN usuarios_asignaturas ua_profesor
        ON ua_profesor.id_asignatura = a.id_asignatura
        AND ua_profesor.id_usuario = :id_profesor
        AND ua_profesor.rol_asignatura = 'profesor'
        AND ua_profesor.estado = 'activa'
    LEFT JOIN usuarios_asignaturas ua_alumno
        ON ua_alumno.id_asignatura = a.id_asignatura
        AND ua_alumno.rol_asignatura = 'alumno'
        AND ua_alumno.estado = 'activa'
    WHERE a.id_asignatura = :id_asignatura
    AND a.estado = 'activa'
    GROUP BY
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.curso,
        a.grupo
    LIMIT 1
");

$consulta_asignatura->execute([
    "id_profesor" => $id_profesor,
    "id_asignatura" => $id_asignatura
]);

$asignatura = $consulta_asignatura->fetch();

if (!$asignatura) {
    header("Location: asignaturas_profesor.php");
    exit;
}

// Fin consulta de asignatura


// Inicio guardado de tarea

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $datos_tarea["titulo"] = trim($_POST["titulo"]);
    $datos_tarea["tipo_actividad"] = $_POST["tipo_actividad"];
    $datos_tarea["unidad"] = trim($_POST["unidad"]);
    $datos_tarea["descripcion"] = trim($_POST["descripcion"]);
    $datos_tarea["fecha_inicio"] = $_POST["fecha_inicio"];
    $datos_tarea["fecha_limite"] = $_POST["fecha_limite"];
    $datos_tarea["estado"] = $_POST["estado"];

    if ($datos_tarea["titulo"] === "") {
        $errores[] = "El título de la tarea es obligatorio.";
    }

    if (!in_array($datos_tarea["tipo_actividad"], ["tarea", "practica"], true)) {
        $errores[] = "El tipo de actividad no es válido.";
    }

    if ($datos_tarea["unidad"] === "") {
        $errores[] = "La unidad es obligatoria.";
    }

    if ($datos_tarea["descripcion"] === "") {
        $errores[] = "La descripción es obligatoria.";
    }

    if ($datos_tarea["fecha_inicio"] === "") {
        $errores[] = "La fecha de emisión es obligatoria.";
    }

    if ($datos_tarea["fecha_limite"] === "") {
        $errores[] = "La fecha de entrega es obligatoria.";
    }

    if ($datos_tarea["fecha_inicio"] !== "" && $datos_tarea["fecha_limite"] !== "" && $datos_tarea["fecha_limite"] < $datos_tarea["fecha_inicio"]) {
        $errores[] = "La fecha de entrega no puede ser anterior a la fecha de emisión.";
    }

    if (!in_array($datos_tarea["estado"], ["publicada", "borrador"], true)) {
        $errores[] = "El estado seleccionado no es válido.";
    }

    if (count($errores) === 0) {
        $visible = $datos_tarea["estado"] === "publicada" ? 1 : 0;

        $insertar_tarea = $pdo->prepare("
            INSERT INTO actividades_evaluables
                (
                    id_asignatura,
                    id_profesor,
                    tipo_actividad,
                    unidad,
                    titulo,
                    descripcion,
                    fecha_inicio,
                    fecha_limite,
                    puntuacion_maxima,
                    visible,
                    estado
                )
            VALUES
                (
                    :id_asignatura,
                    :id_profesor,
                    :tipo_actividad,
                    :unidad,
                    :titulo,
                    :descripcion,
                    :fecha_inicio,
                    :fecha_limite,
                    10.00,
                    :visible,
                    :estado
                )
        ");

        $insertar_tarea->execute([
            "id_asignatura" => $id_asignatura,
            "id_profesor" => $id_profesor,
            "tipo_actividad" => $datos_tarea["tipo_actividad"],
            "unidad" => $datos_tarea["unidad"],
            "titulo" => $datos_tarea["titulo"],
            "descripcion" => $datos_tarea["descripcion"],
            "fecha_inicio" => $datos_tarea["fecha_inicio"] . " 00:00:00",
            "fecha_limite" => $datos_tarea["fecha_limite"] . " 23:59:59",
            "visible" => $visible,
            "estado" => $datos_tarea["estado"]
        ]);

        header("Location: crear_tarea.php?id_asignatura=" . $id_asignatura . "&guardada=ok");
        exit;
    }
}

// Fin guardado de tarea


// Inicio enlaces de navegación

$url_detalle = "detalle_asignatura_profesor.php?id_asignatura=" . $id_asignatura;
$url_recursos = "recursos_profesor.php?id_asignatura=" . $id_asignatura;
$url_tareas = "listado_tareas_profesor.php?id_asignatura=" . $id_asignatura;
$url_examenes = "examenes_profesor.php?id_asignatura=" . $id_asignatura;
$url_calificaciones = "calificaciones_profesor.php?id_asignatura=" . $id_asignatura;

// Fin enlaces de navegación
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Crear tarea | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/crear_tarea.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-crear-tarea">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido principal -->

        <main class="contenido-doa">
            <section class="detalle-asignatura-principal">
                <!-- Inicio cabecera de asignatura -->

                <div class="cabecera-detalle-asignatura">
                    <div class="cabecera-detalle-asignatura__texto">
                        <a class="enlace-volver-asignaturas" href="<?php echo limpiar_texto_doa($url_tareas); ?>">
                            <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                <img alt="" src="img/iconos/grey-chevron-right.svg">
                            </span>

                            <span>Volver a tareas</span>
                        </a>

                        <h1><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></h1>

                        <ul class="metadatos-asignatura">
                            <li>
                                <img alt="" src="img/iconos/grey-graduation-cap.svg">
                                <span>
                                    <?php echo limpiar_texto_doa($asignatura["curso"]); ?>
                                    · Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                                </span>
                            </li>

                            <li>
                                <img alt="" src="img/iconos/grey-user.svg">
                                <span><?php echo (int) $asignatura["total_alumnos"]; ?> alumnos</span>
                            </li>

                            <li>
                                <img alt="" src="img/iconos/grey-notebook.svg">
                                <span><?php echo limpiar_texto_doa($asignatura["codigo"]); ?></span>
                            </li>
                        </ul>
                    </div>

                    <div class="cabecera-detalle-asignatura__pestanas">
                        <nav aria-label="Secciones de la asignatura" class="pestanas-asignatura">
                            <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                Recursos
                            </a>

                            <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="<?php echo limpiar_texto_doa($url_tareas); ?>">
                                Tareas
                            </a>

                            <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_examenes); ?>">
                                Exámenes
                            </a>

                            <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_calificaciones); ?>">
                                Calificaciones
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Fin cabecera de asignatura -->


                <!-- Inicio formulario de tarea -->

                <form class="crear-tarea-grid" method="post">
                    <input type="hidden" name="id_asignatura" value="<?php echo $id_asignatura; ?>">

                    <section class="columna-principal-tarea">
                        <article class="tarjeta-formulario-tarea">
                            <h2>Crear tarea</h2>

                            <?php if (count($errores) > 0) { ?>
                                <div class="mensaje-tarea-guardada mensaje-tarea-guardada--error">
                                    <?php foreach ($errores as $error) { ?>
                                        <p><?php echo limpiar_texto_doa($error); ?></p>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <?php if ($mensaje_ok !== "") { ?>
                                <p class="mensaje-tarea-guardada">
                                    <?php echo limpiar_texto_doa($mensaje_ok); ?>
                                </p>
                            <?php } ?>

                            <div class="grupo-campo-tarea">
                                <label for="inputTituloTarea">Título de la tarea</label>

                                <input
                                    id="inputTituloTarea"
                                    name="titulo"
                                    type="text"
                                    placeholder="Ej. Práctica: cooperación de webs"
                                    value="<?php echo limpiar_texto_doa($datos_tarea["titulo"]); ?>"
                                    required>
                            </div>

                            <div class="formulario-doble-tarea">
                                <div class="grupo-campo-tarea">
                                    <label for="selectTipoTarea">Tipo</label>

                                    <select id="selectTipoTarea" name="tipo_actividad" required>
                                        <option value="tarea" <?php echo $datos_tarea["tipo_actividad"] === "tarea" ? "selected" : ""; ?>>
                                            Tarea
                                        </option>

                                        <option value="practica" <?php echo $datos_tarea["tipo_actividad"] === "practica" ? "selected" : ""; ?>>
                                            Práctica
                                        </option>
                                    </select>
                                </div>

                                <div class="grupo-campo-tarea">
                                    <label for="inputUnidadTarea">Unidad</label>

                                    <input
                                        id="inputUnidadTarea"
                                        name="unidad"
                                        type="text"
                                        placeholder="Ej. Unidad 03"
                                        value="<?php echo limpiar_texto_doa($datos_tarea["unidad"]); ?>"
                                        required>
                                </div>
                            </div>

                            <div class="grupo-campo-tarea">
                                <label for="inputDescripcionTarea">Descripción</label>

                                <textarea
                                    id="inputDescripcionTarea"
                                    name="descripcion"
                                    rows="5"
                                    placeholder="Explica qué debe entregar el alumnado..."
                                    required><?php echo limpiar_texto_doa($datos_tarea["descripcion"]); ?></textarea>
                            </div>
                        </article>

                        <article class="tarjeta-formulario-tarea">
                            <h2>Fechas y entrega</h2>

                            <div class="formulario-doble-tarea">
                                <div class="grupo-campo-tarea">
                                    <label for="inputFechaEmision">Fecha de emisión</label>

                                    <input
                                        id="inputFechaEmision"
                                        name="fecha_inicio"
                                        type="date"
                                        value="<?php echo limpiar_texto_doa($datos_tarea["fecha_inicio"]); ?>"
                                        required>
                                </div>

                                <div class="grupo-campo-tarea">
                                    <label for="inputFechaEntrega">Fecha de entrega</label>

                                    <input
                                        id="inputFechaEntrega"
                                        name="fecha_limite"
                                        type="date"
                                        value="<?php echo limpiar_texto_doa($datos_tarea["fecha_limite"]); ?>"
                                        required>
                                </div>
                            </div>

                            <div class="grupo-campo-tarea">
                                <label for="selectEstadoTarea">Estado</label>

                                <select id="selectEstadoTarea" name="estado" required>
                                    <option value="publicada" <?php echo $datos_tarea["estado"] === "publicada" ? "selected" : ""; ?>>
                                        Publicada
                                    </option>

                                    <option value="borrador" <?php echo $datos_tarea["estado"] === "borrador" ? "selected" : ""; ?>>
                                        Borrador
                                    </option>
                                </select>
                            </div>
                        </article>
                    </section>

                    <aside class="columna-lateral-tarea">
                        <article class="tarjeta-publicacion-tarea">
                            <h2>Publicación</h2>

                            <p>
                                Guarda la tarea para que aparezca en la base de datos y, si está publicada, en la vista del alumnado.
                            </p>

                            <ul class="resumen-publicacion-tarea">
                                <li>
                                    <span>Asignatura</span>
                                    <strong><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></strong>
                                </li>

                                <li>
                                    <span>Grupo</span>
                                    <strong><?php echo limpiar_texto_doa($asignatura["grupo"]); ?></strong>
                                </li>

                                <li>
                                    <span>Estado</span>
                                    <strong><?php echo limpiar_texto_doa($datos_tarea["estado"]); ?></strong>
                                </li>
                            </ul>

                            <button class="boton-publicar-tarea" type="submit">
                                Guardar tarea
                            </button>

                            <a class="boton-descartar-tarea" href="<?php echo limpiar_texto_doa($url_tareas); ?>">
                                Descartar
                            </a>
                        </article>
                    </aside>
                </form>

                <!-- Fin formulario de tarea -->
            </section>
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>
</html>