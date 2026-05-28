<?php
// Inicio configuración de página

$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar tarea, alumno...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página


// Inicio funciones auxiliares

function formatear_fecha_entrega_profesor($fecha)
{
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y H:i", strtotime($fecha));
}

function formatear_fecha_simple_entrega($fecha)
{
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y", strtotime($fecha));
}

function formatear_tamano_archivo_revision($tamano_bytes)
{
    if ($tamano_bytes === null) {
        return "-";
    }

    if ((int) $tamano_bytes >= 1048576) {
        return round((int) $tamano_bytes / 1048576, 1) . " MB";
    }

    return round((int) $tamano_bytes / 1024, 1) . " KB";
}

function obtener_texto_estado_revision($estado, $calificacion)
{
    if ($calificacion) {
        return "Calificada";
    }

    return match ($estado) {
        "entregada" => "Entregada",
        "tardia" => "Tardía",
        "revisada" => "Revisada",
        default => ucfirst($estado),
    };
}

function obtener_clase_estado_revision($estado, $calificacion)
{
    if ($calificacion) {
        return "calificada";
    }

    return $estado;
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

if (!isset($_GET["id_entrega"]) && !isset($_POST["id_entrega"])) {
    header("Location: asignaturas_profesor.php");
    exit;
}

$id_profesor = (int) $_SESSION["doa_id_usuario"];
$id_entrega = isset($_POST["id_entrega"])
    ? (int) $_POST["id_entrega"]
    : (int) $_GET["id_entrega"];

$errores = [];
$mensaje_ok = "";

if (isset($_GET["calificada"]) && $_GET["calificada"] === "ok") {
    $mensaje_ok = "Calificación guardada correctamente.";
}

// Fin parámetros de pantalla


// Inicio consulta de entrega

$consulta_entrega = $pdo->prepare("
    SELECT
        e.id_entrega,
        e.id_actividad,
        e.id_alumno,
        e.texto_entrega,
        e.fecha_entrega,
        e.estado AS estado_entrega,
        ae.id_asignatura,
        ae.titulo AS titulo_tarea,
        ae.descripcion AS descripcion_tarea,
        ae.unidad,
        ae.fecha_inicio,
        ae.fecha_limite,
        a.nombre AS asignatura_nombre,
        a.codigo,
        a.curso,
        a.grupo,
        u_alumno.nombre AS alumno_nombre,
        u_alumno.apellidos AS alumno_apellidos,
        u_alumno.email AS alumno_email,
        c.id_calificacion,
        c.nota,
        c.comentario_profesor,
        c.fecha_calificacion
    FROM entregas e
    INNER JOIN actividades_evaluables ae
        ON ae.id_actividad = e.id_actividad
    INNER JOIN asignaturas a
        ON a.id_asignatura = ae.id_asignatura
    INNER JOIN usuarios u_alumno
        ON u_alumno.id_usuario = e.id_alumno
    INNER JOIN usuarios_asignaturas ua_profesor
        ON ua_profesor.id_asignatura = ae.id_asignatura
        AND ua_profesor.id_usuario = :id_profesor
        AND ua_profesor.rol_asignatura = 'profesor'
        AND ua_profesor.estado = 'activa'
    LEFT JOIN calificaciones c
        ON c.id_actividad = e.id_actividad
        AND c.id_alumno = e.id_alumno
    WHERE e.id_entrega = :id_entrega
    AND ae.tipo_actividad IN ('tarea', 'practica')
    LIMIT 1
");

$consulta_entrega->execute([
    "id_profesor" => $id_profesor,
    "id_entrega" => $id_entrega
]);

$entrega = $consulta_entrega->fetch();

if (!$entrega) {
    header("Location: asignaturas_profesor.php");
    exit;
}

$id_actividad = (int) $entrega["id_actividad"];
$id_asignatura = (int) $entrega["id_asignatura"];
$id_alumno = (int) $entrega["id_alumno"];

// Fin consulta de entrega


// Inicio consulta de archivos

$consulta_archivos = $pdo->prepare("
    SELECT
        nombre_archivo,
        url_archivo,
        tipo_archivo,
        tamano_bytes
    FROM archivos_entrega
    WHERE id_entrega = :id_entrega
    ORDER BY fecha_subida ASC
");

$consulta_archivos->execute([
    "id_entrega" => $id_entrega
]);

$archivos_entrega = $consulta_archivos->fetchAll();

// Fin consulta de archivos


// Inicio guardado de calificación

$nota_formulario = $entrega["nota"] !== null ? (string) $entrega["nota"] : "";
$comentario_formulario = $entrega["comentario_profesor"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nota_formulario = trim($_POST["nota"]);
    $comentario_formulario = trim($_POST["comentario_profesor"]);

    if ($nota_formulario === "") {
        $errores[] = "La nota es obligatoria.";
    }

    if ($nota_formulario !== "" && (!is_numeric($nota_formulario) || (float) $nota_formulario < 0 || (float) $nota_formulario > 10)) {
        $errores[] = "La nota debe estar entre 0 y 10.";
    }

    if (count($errores) === 0) {
        if ($entrega["id_calificacion"] !== null) {
            $guardar_calificacion = $pdo->prepare("
                UPDATE calificaciones
                SET
                    nota = :nota,
                    comentario_profesor = :comentario_profesor,
                    fecha_calificacion = CURRENT_TIMESTAMP
                WHERE id_calificacion = :id_calificacion
            ");

            $guardar_calificacion->execute([
                "nota" => $nota_formulario,
                "comentario_profesor" => $comentario_formulario !== "" ? $comentario_formulario : null,
                "id_calificacion" => $entrega["id_calificacion"]
            ]);
        } else {
            $guardar_calificacion = $pdo->prepare("
                INSERT INTO calificaciones
                    (
                        id_actividad,
                        id_alumno,
                        id_profesor,
                        nota,
                        comentario_profesor
                    )
                VALUES
                    (
                        :id_actividad,
                        :id_alumno,
                        :id_profesor,
                        :nota,
                        :comentario_profesor
                    )
            ");

            $guardar_calificacion->execute([
                "id_actividad" => $id_actividad,
                "id_alumno" => $id_alumno,
                "id_profesor" => $id_profesor,
                "nota" => $nota_formulario,
                "comentario_profesor" => $comentario_formulario !== "" ? $comentario_formulario : null
            ]);
        }

        header("Location: detalle_tarea_entregada.php?id_entrega=" . $id_entrega . "&calificada=ok");
        exit;
    }
}

// Fin guardado de calificación


// Inicio datos derivados

$nombre_alumno = trim($entrega["alumno_nombre"] . " " . $entrega["alumno_apellidos"]);
$estado_visible = obtener_texto_estado_revision($entrega["estado_entrega"], $entrega["id_calificacion"] !== null);
$clase_estado = obtener_clase_estado_revision($entrega["estado_entrega"], $entrega["id_calificacion"] !== null);

$fecha_entrega = formatear_fecha_entrega_profesor($entrega["fecha_entrega"]);
$fecha_limite = formatear_fecha_simple_entrega($entrega["fecha_limite"]);
$fecha_calificacion = formatear_fecha_entrega_profesor($entrega["fecha_calificacion"]);

$url_tarea_profesor = "detalle_tarea_profesor.php?id_actividad=" . $id_actividad;
$url_recursos = "recursos_profesor.php?id_asignatura=" . $id_asignatura;
$url_tareas = "listado_tareas_profesor.php?id_asignatura=" . $id_asignatura;
$url_examenes = "examenes_profesor.php?id_asignatura=" . $id_asignatura;
$url_calificaciones = "calificaciones_profesor.php?id_asignatura=" . $id_asignatura;

// Fin datos derivados
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Entrega de <?php echo limpiar_texto_doa($nombre_alumno); ?> | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/detalle_tarea_entregada.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-detalle-tarea-entregada">
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
                        <a class="enlace-volver-asignaturas" href="<?php echo limpiar_texto_doa($url_tarea_profesor); ?>">
                            <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                <img alt="" src="img/iconos/grey-chevron-right.svg">
                            </span>

                            <span>Volver a la tarea</span>
                        </a>

                        <h1><?php echo limpiar_texto_doa($entrega["asignatura_nombre"]); ?></h1>

                        <ul class="metadatos-asignatura">
                            <li>
                                <img alt="" src="img/iconos/grey-graduation-cap.svg">
                                <span>
                                    <?php echo limpiar_texto_doa($entrega["curso"]); ?>
                                    · Grupo <?php echo limpiar_texto_doa($entrega["grupo"]); ?>
                                </span>
                            </li>

                            <li>
                                <img alt="" src="img/iconos/grey-user.svg">
                                <span><?php echo limpiar_texto_doa($nombre_alumno); ?></span>
                            </li>

                            <li>
                                <img alt="" src="img/iconos/grey-notebook.svg">
                                <span><?php echo limpiar_texto_doa($entrega["unidad"]); ?></span>
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


                <!-- Inicio entrega del alumno -->

                <section class="entrega-profesor-grid">
                    <article class="tarjeta-entrega-profesor">
                        <div class="cabecera-entrega-profesor">
                            <span class="etiqueta-entrega-profesor etiqueta-entrega-profesor--<?php echo limpiar_texto_doa($clase_estado); ?>">
                                <?php echo limpiar_texto_doa($estado_visible); ?>
                            </span>

                            <h2><?php echo limpiar_texto_doa($entrega["titulo_tarea"]); ?></h2>

                            <p>
                                <?php echo limpiar_texto_doa($entrega["descripcion_tarea"]); ?>
                            </p>
                        </div>

                        <div class="resumen-entrega-profesor">
                            <article class="dato-entrega-profesor dato-entrega-profesor--principal">
                                <span>Alumno</span>
                                <strong><?php echo limpiar_texto_doa($nombre_alumno); ?></strong>
                            </article>

                            <article class="dato-entrega-profesor">
                                <span>Entrega</span>
                                <strong><?php echo limpiar_texto_doa($fecha_entrega); ?></strong>
                            </article>

                            <article class="dato-entrega-profesor">
                                <span>Límite</span>
                                <strong><?php echo limpiar_texto_doa($fecha_limite); ?></strong>
                            </article>

                            <article class="dato-entrega-profesor">
                                <span>Nota</span>
                                <strong>
                                    <?php echo $entrega["nota"] !== null ? limpiar_texto_doa(number_format((float) $entrega["nota"], 1) . "/10") : "-"; ?>
                                </strong>
                            </article>
                        </div>

                        <section class="bloque-archivos-entrega">
                            <h3>Archivos entregados</h3>

                            <ul class="lista-archivos-entrega">
                                <?php if (count($archivos_entrega) === 0) { ?>
                                    <li class="item-sin-archivos">
                                        Esta entrega no tiene archivos registrados.
                                    </li>
                                <?php } ?>

                                <?php foreach ($archivos_entrega as $archivo) { ?>
                                    <li>
                                        <a href="<?php echo limpiar_texto_doa($archivo["url_archivo"]); ?>">
                                            <span>
                                                <img alt="" src="img/iconos/grey-file.svg">
                                                <?php echo limpiar_texto_doa($archivo["nombre_archivo"]); ?>
                                            </span>

                                            <strong>
                                                <?php echo limpiar_texto_doa(formatear_tamano_archivo_revision($archivo["tamano_bytes"])); ?>
                                            </strong>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </section>

                        <section class="bloque-comentario-alumno">
                            <h3>Comentario del alumno</h3>

                            <p>
                                <?php echo $entrega["texto_entrega"] !== null && $entrega["texto_entrega"] !== ""
                                    ? limpiar_texto_doa($entrega["texto_entrega"])
                                    : "El alumno no añadió comentario a la entrega."; ?>
                            </p>
                        </section>
                    </article>

                    <aside class="panel-calificacion-entrega">
                        <article class="tarjeta-calificacion-entrega">
                            <h3>Calificación</h3>

                            <p>
                                Introduce la nota final de esta entrega.
                            </p>

                            <?php if ($mensaje_ok !== "") { ?>
                                <p class="mensaje-calificacion">
                                    <?php echo limpiar_texto_doa($mensaje_ok); ?>
                                </p>
                            <?php } ?>

                            <?php if (count($errores) > 0) { ?>
                                <div class="mensaje-calificacion mensaje-calificacion--error">
                                    <?php foreach ($errores as $error) { ?>
                                        <p><?php echo limpiar_texto_doa($error); ?></p>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <form method="post">
                                <input type="hidden" name="id_entrega" value="<?php echo $id_entrega; ?>">

                                <div class="grupo-campo-entrega">
                                    <label for="notaEntrega">Nota</label>

                                    <input
                                        id="notaEntrega"
                                        name="nota"
                                        type="number"
                                        min="0"
                                        max="10"
                                        step="0.1"
                                        value="<?php echo limpiar_texto_doa($nota_formulario); ?>"
                                        required>
                                </div>

                                <div class="grupo-campo-entrega">
                                    <label for="comentarioProfesor">Comentario</label>

                                    <textarea
                                        id="comentarioProfesor"
                                        name="comentario_profesor"
                                        rows="5"
                                        placeholder="Comentario para el alumno..."><?php echo limpiar_texto_doa($comentario_formulario); ?></textarea>
                                </div>

                                <button class="boton-guardar-calificacion" type="submit">
                                    Guardar calificación
                                </button>

                                <a class="boton-volver-entrega" href="<?php echo limpiar_texto_doa($url_tarea_profesor); ?>">
                                    Volver
                                </a>
                            </form>
                        </article>

                        <article class="tarjeta-info-entrega">
                            <h3>Información</h3>

                            <ul class="lista-info-entrega">
                                <li>
                                    <span>Correo</span>
                                    <strong><?php echo limpiar_texto_doa($entrega["alumno_email"]); ?></strong>
                                </li>

                                <li>
                                    <span>Estado</span>
                                    <strong><?php echo limpiar_texto_doa($estado_visible); ?></strong>
                                </li>

                                <li>
                                    <span>Fecha de calificación</span>
                                    <strong><?php echo limpiar_texto_doa($fecha_calificacion); ?></strong>
                                </li>
                            </ul>
                        </article>
                    </aside>
                </section>

                <!-- Fin entrega del alumno -->
            </section>
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>

</html>