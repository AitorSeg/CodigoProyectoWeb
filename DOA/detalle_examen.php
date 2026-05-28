<?php
// Inicio configuración de página

$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar examen...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página


// Inicio funciones auxiliares

function formatear_fecha_examen_detalle($fecha)
{
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y", strtotime($fecha));
}

function formatear_fecha_hora_examen_detalle($fecha)
{
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y H:i", strtotime($fecha));
}

function obtener_temas_examen_detalle($temas)
{
    $temas_limpios = trim((string) $temas);

    if ($temas_limpios === "") {
        return [];
    }

    return array_values(array_filter(array_map("trim", preg_split("/[\r\n,;]+/", $temas_limpios))));
}

function obtener_estado_examen_detalle_alumno($examen, $total_respuestas, $id_calificacion)
{
    if ((int) $total_respuestas > 0 || $id_calificacion !== null) {
        return "realizado";
    }

    if ($examen["estado"] === "cerrada") {
        return "cerrado";
    }

    $fecha_actual = new DateTime();
    $fecha_inicio = $examen["fecha_inicio"] !== null ? new DateTime($examen["fecha_inicio"]) : null;
    $fecha_limite = $examen["fecha_limite"] !== null ? new DateTime($examen["fecha_limite"]) : null;

    if ($fecha_inicio !== null && $fecha_inicio > $fecha_actual) {
        return "proximo";
    }

    if ($fecha_limite !== null && $fecha_limite < $fecha_actual) {
        return "cerrado";
    }

    return "abierto";
}

function obtener_texto_estado_examen_detalle($estado)
{
    return match ($estado) {
        "abierto" => "Abierto",
        "proximo" => "Próximo",
        "cerrado" => "Cerrado",
        "realizado" => "Realizado",
        default => ucfirst($estado),
    };
}

function obtener_mensaje_acceso_examen($estado)
{
    return match ($estado) {
        "abierto" => "El examen está disponible. Puedes empezar cuando quieras.",
        "proximo" => "El examen todavía no está disponible.",
        "cerrado" => "El plazo para realizar este examen ha finalizado.",
        "realizado" => "Ya has realizado este examen.",
        default => "Consulta la información del examen.",
    };
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

if (!isset($_GET["id_actividad"])) {
    header("Location: asignaturas.php");
    exit;
}

$id_alumno = (int) $_SESSION["doa_id_usuario"];
$id_actividad = (int) $_GET["id_actividad"];

// Fin parámetros de pantalla


// Inicio consulta de examen

$consulta_examen = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.id_asignatura,
        ae.titulo,
        ae.descripcion,
        ae.temas,
        ae.unidad,
        ae.fecha_inicio,
        ae.fecha_limite,
        ae.duracion_minutos,
        ae.intentos_maximos,
        ae.estado,
        a.nombre AS asignatura_nombre,
        a.codigo,
        a.curso,
        a.grupo,
        GROUP_CONCAT(
            DISTINCT CONCAT(u_profesor.nombre, ' ', u_profesor.apellidos)
            SEPARATOR ', '
        ) AS profesores
    FROM actividades_evaluables ae
    INNER JOIN asignaturas a
        ON a.id_asignatura = ae.id_asignatura
    INNER JOIN usuarios_asignaturas ua_alumno
        ON ua_alumno.id_asignatura = ae.id_asignatura
        AND ua_alumno.id_usuario = :id_alumno
        AND ua_alumno.rol_asignatura = 'alumno'
        AND ua_alumno.estado = 'activa'
    LEFT JOIN usuarios_asignaturas ua_profesor
        ON ua_profesor.id_asignatura = ae.id_asignatura
        AND ua_profesor.rol_asignatura = 'profesor'
        AND ua_profesor.estado = 'activa'
    LEFT JOIN usuarios u_profesor
        ON u_profesor.id_usuario = ua_profesor.id_usuario
    WHERE ae.id_actividad = :id_actividad
    AND ae.tipo_actividad = 'examen'
    AND ae.visible = 1
    AND ae.estado = 'publicada'
    AND a.estado = 'activa'
    GROUP BY
        ae.id_actividad,
        ae.id_asignatura,
        ae.titulo,
        ae.descripcion,
        ae.temas,
        ae.unidad,
        ae.fecha_inicio,
        ae.fecha_limite,
        ae.duracion_minutos,
        ae.intentos_maximos,
        ae.estado,
        a.nombre,
        a.codigo,
        a.curso,
        a.grupo
    LIMIT 1
");

$consulta_examen->execute([
    "id_alumno" => $id_alumno,
    "id_actividad" => $id_actividad
]);

$examen = $consulta_examen->fetch();

if (!$examen) {
    header("Location: asignaturas.php");
    exit;
}

$id_asignatura = (int) $examen["id_asignatura"];
$profesores = $examen["profesores"] !== null ? $examen["profesores"] : "Pendiente";

// Fin consulta de examen


// Inicio consulta de preguntas y respuestas

$consulta_total_preguntas = $pdo->prepare("
    SELECT COUNT(*) AS total_preguntas
    FROM preguntas_examen
    WHERE id_actividad = :id_actividad
");

$consulta_total_preguntas->execute([
    "id_actividad" => $id_actividad
]);

$total_preguntas = (int) $consulta_total_preguntas->fetch()["total_preguntas"];

$consulta_respuestas = $pdo->prepare("
    SELECT
        COUNT(DISTINCT re.id_respuesta) AS total_respuestas,
        MAX(re.fecha_respuesta) AS fecha_realizacion
    FROM preguntas_examen p
    LEFT JOIN respuestas_examen re
        ON re.id_pregunta = p.id_pregunta
        AND re.id_alumno = :id_alumno
    WHERE p.id_actividad = :id_actividad
");

$consulta_respuestas->execute([
    "id_alumno" => $id_alumno,
    "id_actividad" => $id_actividad
]);

$resumen_respuestas = $consulta_respuestas->fetch();

$total_respuestas = (int) $resumen_respuestas["total_respuestas"];
$fecha_realizacion = $resumen_respuestas["fecha_realizacion"];

// Fin consulta de preguntas y respuestas


// Inicio consulta de calificación

$consulta_calificacion = $pdo->prepare("
    SELECT
        id_calificacion,
        nota,
        comentario_profesor,
        fecha_calificacion
    FROM calificaciones
    WHERE id_actividad = :id_actividad
    AND id_alumno = :id_alumno
    LIMIT 1
");

$consulta_calificacion->execute([
    "id_actividad" => $id_actividad,
    "id_alumno" => $id_alumno
]);

$calificacion = $consulta_calificacion->fetch();

// Fin consulta de calificación

// Inicio consulta de revisión de respuestas

$revision_respuestas = [];

if ((int) $total_respuestas > 0 || $calificacion) {
    $consulta_revision = $pdo->prepare("
        SELECT
            p.id_pregunta,
            p.orden,
            p.enunciado,
            p.explicacion,
            re.id_opcion AS id_opcion_alumno,
            opcion_alumno.texto AS texto_opcion_alumno,
            opcion_correcta.id_opcion AS id_opcion_correcta,
            opcion_correcta.texto AS texto_opcion_correcta
        FROM preguntas_examen p
        LEFT JOIN respuestas_examen re
            ON re.id_pregunta = p.id_pregunta
            AND re.id_alumno = :id_alumno
        LEFT JOIN opciones_pregunta opcion_alumno
            ON opcion_alumno.id_opcion = re.id_opcion
        LEFT JOIN opciones_pregunta opcion_correcta
            ON opcion_correcta.id_pregunta = p.id_pregunta
            AND opcion_correcta.es_correcta = 1
        WHERE p.id_actividad = :id_actividad
        ORDER BY p.orden ASC
    ");

    $consulta_revision->execute([
        "id_alumno" => $id_alumno,
        "id_actividad" => $id_actividad
    ]);

    $revision_respuestas = $consulta_revision->fetchAll();
}

// Fin consulta de revisión de respuestas

// Inicio datos derivados

$estado_examen = obtener_estado_examen_detalle_alumno(
    $examen,
    $total_respuestas,
    $calificacion["id_calificacion"] ?? null
);

$texto_estado_examen = obtener_texto_estado_examen_detalle($estado_examen);
$mensaje_acceso = obtener_mensaje_acceso_examen($estado_examen);

$fecha_inicio = formatear_fecha_examen_detalle($examen["fecha_inicio"]);
$fecha_limite = formatear_fecha_examen_detalle($examen["fecha_limite"]);
$duracion = $examen["duracion_minutos"] !== null ? (int) $examen["duracion_minutos"] . " min" : "-";
$intentos = (int) $examen["intentos_maximos"] === 1 ? "1 intento" : (int) $examen["intentos_maximos"] . " intentos";
$temas_examen = obtener_temas_examen_detalle($examen["temas"]);

$nota_examen = $calificacion
    ? number_format((float) $calificacion["nota"], 1) . "/10"
    : "-";

$puede_realizar_examen = $estado_examen === "abierto" && $total_preguntas > 0;

$url_volver = "examenes.php?id_asignatura=" . $id_asignatura;
$url_recursos = "recursos_alumno.php?id_asignatura=" . $id_asignatura;
$url_tareas = "listado_tareas.php?id_asignatura=" . $id_asignatura;
$url_examenes = "examenes.php?id_asignatura=" . $id_asignatura;
$url_calificaciones = "calificaciones.php?id_asignatura=" . $id_asignatura;
$url_realizar = "realizar_examen.php?id_actividad=" . $id_actividad;

// Fin datos derivados
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title><?php echo limpiar_texto_doa($examen["titulo"]); ?> | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/detalle_examen.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-detalle-examen">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido principal -->

        <main class="contenido-doa">
            <!-- Inicio cabecera de examen -->

            <section class="cabecera-detalle-asignatura">
                <div class="cabecera-detalle-asignatura__texto">
                    <a class="enlace-volver-asignaturas" href="<?php echo limpiar_texto_doa($url_volver); ?>">
                        <span class="enlace-volver-asignaturas__icono" aria-hidden="true">
                            <img src="img/iconos/grey-chevron-right.svg" alt="">
                        </span>

                        <span>Volver a exámenes</span>
                    </a>

                    <h1><?php echo limpiar_texto_doa($examen["titulo"]); ?></h1>

                    <ul class="metadatos-asignatura">
                        <li>
                            <img src="img/iconos/grey-notebook.svg" alt="">
                            <span><?php echo limpiar_texto_doa($examen["asignatura_nombre"]); ?></span>
                        </li>

                        <li>
                            <img src="img/iconos/grey-user.svg" alt="">
                            <span><?php echo limpiar_texto_doa($profesores); ?></span>
                        </li>

                        <li>
                            <img src="img/iconos/grey-calendar.svg" alt="">
                            <span><?php echo limpiar_texto_doa($fecha_inicio); ?></span>
                        </li>
                    </ul>
                </div>

                <div class="cabecera-detalle-asignatura__pestanas">
                    <nav class="pestanas-asignatura" aria-label="Secciones de la asignatura">
                        <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                            Recursos
                        </a>

                        <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_tareas); ?>">
                            Tareas
                        </a>

                        <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="<?php echo limpiar_texto_doa($url_examenes); ?>">
                            Exámenes
                        </a>

                        <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_calificaciones); ?>">
                            Calificaciones
                        </a>
                    </nav>
                </div>
            </section>

            <!-- Fin cabecera de examen -->


            <!-- Inicio detalle de examen -->

            <section class="detalle-examen-grid">
                <article class="tarjeta-detalle-examen" data-estado="<?php echo limpiar_texto_doa($estado_examen); ?>">
                    <div class="tarjeta-detalle-examen__cabecera">
                        <span class="estado-detalle-examen estado-detalle-examen--<?php echo limpiar_texto_doa($estado_examen); ?>">
                            <?php echo limpiar_texto_doa($texto_estado_examen); ?>
                        </span>

                        <h2>Información del examen</h2>

                        <p>
                            <?php echo limpiar_texto_doa($examen["descripcion"]); ?>
                        </p>
                    </div>

                    <div class="datos-examen">
                        <div class="dato-examen">
                            <span>Apertura</span>
                            <strong><?php echo limpiar_texto_doa($fecha_inicio); ?></strong>
                        </div>

                        <div class="dato-examen">
                            <span>Cierre</span>
                            <strong><?php echo limpiar_texto_doa($fecha_limite); ?></strong>
                        </div>

                        <div class="dato-examen">
                            <span>Duración</span>
                            <strong><?php echo limpiar_texto_doa($duracion); ?></strong>
                        </div>

                        <div class="dato-examen">
                            <span>Preguntas</span>
                            <strong><?php echo $total_preguntas; ?> preguntas</strong>
                        </div>

                        <div class="dato-examen">
                            <span>Intentos</span>
                            <strong><?php echo limpiar_texto_doa($intentos); ?></strong>
                        </div>
                    </div>

                    <div class="bloque-detalle-examen">
                        <h3>Contenido evaluado</h3>

                        <ul class="lista-temas-examen">
                            <?php if (count($temas_examen) === 0) { ?>
                                <li>Sin contenido indicado.</li>
                            <?php } ?>

                            <?php foreach ($temas_examen as $tema) { ?>
                                <li><?php echo limpiar_texto_doa($tema); ?></li>
                            <?php } ?>
                        </ul>
                    </div>

                    <div class="bloque-detalle-examen">
                        <h3>Indicaciones</h3>

                        <p>
                            Lee con atención cada pregunta antes de responder. Al entregar el examen,
                            se guardarán tus respuestas y se calculará la calificación correspondiente.
                        </p>
                    </div>

                    <?php if ($estado_examen === "realizado") { ?>
                        <div class="bloque-detalle-examen">
                            <h3>Resultado</h3>

                            <p>
                                Respuestas guardadas:
                                <strong><?php echo $total_respuestas; ?>/<?php echo $total_preguntas; ?></strong>
                            </p>

                            <p>
                                Fecha de realización:
                                <strong><?php echo limpiar_texto_doa(formatear_fecha_hora_examen_detalle($fecha_realizacion)); ?></strong>
                            </p>

                            <p>
                                Calificación:
                                <strong><?php echo limpiar_texto_doa($nota_examen); ?></strong>
                            </p>

                            <?php if ($calificacion && $calificacion["comentario_profesor"] !== null && $calificacion["comentario_profesor"] !== "") { ?>
                                <p>
                                    Comentario:
                                    <strong><?php echo limpiar_texto_doa($calificacion["comentario_profesor"]); ?></strong>
                                </p>
                            <?php } ?>
                        </div>

                        <div class="bloque-detalle-examen">
                            <h3>Revisión de respuestas</h3>

                            <div class="revision-respuestas-examen">
                                <?php foreach ($revision_respuestas as $respuesta) { ?>
                                    <?php
                                    $respuesta_correcta = (int) $respuesta["id_opcion_alumno"] === (int) $respuesta["id_opcion_correcta"];
                                    $clase_revision = $respuesta_correcta ? "correcta" : "incorrecta";
                                    $texto_revision = $respuesta_correcta ? "Correcta" : "Incorrecta";
                                    ?>

                                    <article class="pregunta-revision-examen pregunta-revision-examen--<?php echo limpiar_texto_doa($clase_revision); ?>">
                                        <div class="pregunta-revision-examen__cabecera">
                                            <span>Pregunta <?php echo (int) $respuesta["orden"]; ?></span>

                                            <strong class="etiqueta-revision-respuesta etiqueta-revision-respuesta--<?php echo limpiar_texto_doa($clase_revision); ?>">
                                                <?php echo limpiar_texto_doa($texto_revision); ?>
                                            </strong>
                                        </div>

                                        <p class="pregunta-revision-examen__enunciado">
                                            <?php echo limpiar_texto_doa($respuesta["enunciado"]); ?>
                                        </p>

                                        <div class="respuestas-revision-examen">
                                            <p>
                                                <span>Tu respuesta:</span>

                                                <strong class="respuesta-alumno--<?php echo limpiar_texto_doa($clase_revision); ?>">
                                                    <?php echo limpiar_texto_doa($respuesta["texto_opcion_alumno"] ?? "Sin respuesta"); ?>
                                                </strong>
                                            </p>

                                            <?php if (!$respuesta_correcta) { ?>
                                                <p>
                                                    <span>Respuesta correcta:</span>

                                                    <strong class="respuesta-correcta-revision">
                                                        <?php echo limpiar_texto_doa($respuesta["texto_opcion_correcta"]); ?>
                                                    </strong>
                                                </p>
                                            <?php } ?>
                                        </div>

                                        <?php if ($respuesta["explicacion"] !== null && $respuesta["explicacion"] !== "") { ?>
                                            <p class="explicacion-revision-examen">
                                                <?php echo limpiar_texto_doa($respuesta["explicacion"]); ?>
                                            </p>
                                        <?php } ?>
                                    </article>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </article>

                <aside class="panel-accion-examen">
                    <div class="tarjeta-accion-examen">
                        <p class="tarjeta-accion-examen__titulo">Acceso al examen</p>

                        <p class="tarjeta-accion-examen__texto">
                            <?php echo limpiar_texto_doa($mensaje_acceso); ?>
                        </p>

                        <?php if ($puede_realizar_examen) { ?>
                            <a class="boton-realizar-examen" href="<?php echo limpiar_texto_doa($url_realizar); ?>">
                                Realizar examen
                            </a>
                        <?php } else { ?>
                            <span class="boton-realizar-examen boton-realizar-examen--desactivado">
                                No disponible
                            </span>
                        <?php } ?>
                    </div>

                    <div class="tarjeta-aviso-examen">
                        <strong>Estado</strong>

                        <p>
                            <?php echo limpiar_texto_doa($texto_estado_examen); ?>
                        </p>
                    </div>

                    <div class="tarjeta-aviso-examen">
                        <strong>Asignatura</strong>

                        <p>
                            <?php echo limpiar_texto_doa($examen["asignatura_nombre"]); ?>
                        </p>
                    </div>
                </aside>
            </section>

            <!-- Fin detalle de examen -->
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>

</html>