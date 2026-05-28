<?php
// Inicio configuración de página

$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar examen...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página


// Inicio funciones auxiliares

function obtener_estado_examen_detalle_profesor($examen) {
    if ($examen["estado"] === "borrador") {
        return "borrador";
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

function obtener_texto_estado_examen_detalle_profesor($estado) {
    return match ($estado) {
        "abierto" => "Abierto",
        "proximo" => "Próximo",
        "cerrado" => "Cerrado",
        "borrador" => "Borrador",
        default => ucfirst($estado),
    };
}

function formatear_fecha_examen_detalle_profesor($fecha) {
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y", strtotime($fecha));
}

function formatear_fecha_hora_examen_detalle_profesor($fecha) {
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y H:i", strtotime($fecha));
}

function obtener_temas_examen_profesor($temas) {
    $temas_limpios = trim((string) $temas);

    if ($temas_limpios === "") {
        return [];
    }

    return array_values(array_filter(array_map("trim", preg_split("/[\r\n,;]+/", $temas_limpios))));
}

function obtener_estado_alumno_examen_profesor($total_respuestas, $id_calificacion) {
    if ($id_calificacion !== null) {
        return "corregida";
    }

    if ((int) $total_respuestas > 0) {
        return "realizado";
    }

    return "pendiente";
}

function obtener_texto_estado_alumno_examen_profesor($estado) {
    return match ($estado) {
        "corregida" => "Calificado",
        "realizado" => "Realizado",
        "pendiente" => "Pendiente",
        default => ucfirst($estado),
    };
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

if (!isset($_GET["id_actividad"])) {
    header("Location: asignaturas_profesor.php");
    exit;
}

$id_profesor = (int) $_SESSION["doa_id_usuario"];
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
        ae.visible,
        ae.estado,
        a.nombre AS asignatura_nombre,
        a.codigo,
        a.curso,
        a.grupo
    FROM actividades_evaluables ae
    INNER JOIN asignaturas a
        ON a.id_asignatura = ae.id_asignatura
    INNER JOIN usuarios_asignaturas ua_profesor
        ON ua_profesor.id_asignatura = ae.id_asignatura
        AND ua_profesor.id_usuario = :id_profesor
        AND ua_profesor.rol_asignatura = 'profesor'
        AND ua_profesor.estado = 'activa'
    WHERE ae.id_actividad = :id_actividad
    AND ae.id_profesor = :id_profesor_creador
    AND ae.tipo_actividad = 'examen'
    LIMIT 1
");

$consulta_examen->execute([
    "id_profesor" => $id_profesor,
    "id_profesor_creador" => $id_profesor,
    "id_actividad" => $id_actividad
]);

$examen = $consulta_examen->fetch();

if (!$examen) {
    header("Location: asignaturas_profesor.php");
    exit;
}

$id_asignatura = (int) $examen["id_asignatura"];

// Fin consulta de examen


// Inicio consulta de preguntas

$consulta_preguntas = $pdo->prepare("
    SELECT
        p.id_pregunta,
        p.enunciado,
        p.orden AS orden_pregunta,
        p.explicacion,
        o.id_opcion,
        o.texto AS texto_opcion,
        o.es_correcta,
        o.orden AS orden_opcion
    FROM preguntas_examen p
    LEFT JOIN opciones_pregunta o
        ON o.id_pregunta = p.id_pregunta
    WHERE p.id_actividad = :id_actividad
    ORDER BY p.orden ASC, o.orden ASC
");

$consulta_preguntas->execute([
    "id_actividad" => $id_actividad
]);

$filas_preguntas = $consulta_preguntas->fetchAll();
$preguntas = [];

foreach ($filas_preguntas as $fila_pregunta) {
    $id_pregunta = (int) $fila_pregunta["id_pregunta"];

    if (!isset($preguntas[$id_pregunta])) {
        $preguntas[$id_pregunta] = [
            "enunciado" => $fila_pregunta["enunciado"],
            "orden" => (int) $fila_pregunta["orden_pregunta"],
            "explicacion" => $fila_pregunta["explicacion"],
            "opciones" => []
        ];
    }

    if ($fila_pregunta["id_opcion"] !== null) {
        $preguntas[$id_pregunta]["opciones"][] = [
            "texto" => $fila_pregunta["texto_opcion"],
            "es_correcta" => (int) $fila_pregunta["es_correcta"] === 1
        ];
    }
}

$total_preguntas = count($preguntas);

// Fin consulta de preguntas


// Inicio consulta de seguimiento

$consulta_seguimiento = $pdo->prepare("
    SELECT
        u.id_usuario,
        u.nombre,
        u.apellidos,
        u.email,
        COUNT(DISTINCT re.id_respuesta) AS total_respuestas,
        MAX(re.fecha_respuesta) AS fecha_realizacion,
        c.id_calificacion,
        c.nota
    FROM usuarios_asignaturas ua
    INNER JOIN usuarios u
        ON u.id_usuario = ua.id_usuario
    LEFT JOIN preguntas_examen p
        ON p.id_actividad = :id_actividad_preguntas
    LEFT JOIN respuestas_examen re
        ON re.id_pregunta = p.id_pregunta
        AND re.id_alumno = u.id_usuario
    LEFT JOIN calificaciones c
        ON c.id_actividad = :id_actividad_calificacion
        AND c.id_alumno = u.id_usuario
    WHERE ua.id_asignatura = :id_asignatura
    AND ua.rol_asignatura = 'alumno'
    AND ua.estado = 'activa'
    GROUP BY
        u.id_usuario,
        u.nombre,
        u.apellidos,
        u.email,
        c.id_calificacion,
        c.nota
    ORDER BY u.nombre ASC, u.apellidos ASC
");

$consulta_seguimiento->execute([
    "id_actividad_preguntas" => $id_actividad,
    "id_actividad_calificacion" => $id_actividad,
    "id_asignatura" => $id_asignatura
]);

$seguimiento_alumnos = $consulta_seguimiento->fetchAll();

$total_alumnos = count($seguimiento_alumnos);
$total_realizados = 0;
$total_calificados = 0;

foreach ($seguimiento_alumnos as $alumno) {
    if ((int) $alumno["total_respuestas"] > 0) {
        $total_realizados++;
    }

    if ($alumno["id_calificacion"] !== null) {
        $total_calificados++;
    }
}

$total_sin_realizar = max(0, $total_alumnos - $total_realizados);
$total_sin_calificar = max(0, $total_realizados - $total_calificados);

// Fin consulta de seguimiento


// Inicio datos derivados

$estado_examen = obtener_estado_examen_detalle_profesor($examen);
$texto_estado_examen = obtener_texto_estado_examen_detalle_profesor($estado_examen);
$temas_examen = obtener_temas_examen_profesor($examen["temas"]);

$fecha_inicio = formatear_fecha_examen_detalle_profesor($examen["fecha_inicio"]);
$fecha_limite = formatear_fecha_examen_detalle_profesor($examen["fecha_limite"]);
$duracion = $examen["duracion_minutos"] !== null ? (int) $examen["duracion_minutos"] . " min" : "-";

$url_volver = "examenes_profesor.php?id_asignatura=" . $id_asignatura;
$url_recursos = "recursos_profesor.php?id_asignatura=" . $id_asignatura;
$url_tareas = "listado_tareas_profesor.php?id_asignatura=" . $id_asignatura;
$url_examenes = "examenes_profesor.php?id_asignatura=" . $id_asignatura;
$url_calificaciones = "calificaciones_profesor.php?id_asignatura=" . $id_asignatura;
$url_crear_examen = "crear_examen.php?id_asignatura=" . $id_asignatura;

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
    <link href="css/examenes.css" rel="stylesheet">
    <link href="css/detalle_examen.css" rel="stylesheet">
    <link href="css/calificaciones.css" rel="stylesheet">
    <link href="css/detalle_examen_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-detalle-examen pagina-detalle-examen-profesor">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido principal -->

        <main class="contenido-doa">
            <!-- Inicio cabecera de asignatura -->

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
                            <img src="img/iconos/grey-graduation-cap.svg" alt="">
                            <span>
                                <?php echo limpiar_texto_doa($examen["curso"]); ?>
                                · Grupo <?php echo limpiar_texto_doa($examen["grupo"]); ?>
                            </span>
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

            <!-- Fin cabecera de asignatura -->


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

                    <div class="datos-examen datos-examen--profesor">
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
                            <span>Realizados</span>
                            <strong><?php echo $total_realizados; ?>/<?php echo $total_alumnos; ?></strong>
                        </div>

                        <div class="dato-examen">
                            <span>Sin calificar</span>
                            <strong><?php echo $total_sin_calificar; ?></strong>
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
                        <h3>Preguntas configuradas</h3>

                        <div class="lista-preguntas-profesor">
                            <?php if (count($preguntas) === 0) { ?>
                                <p>No hay preguntas configuradas para este examen.</p>
                            <?php } ?>

                            <?php foreach ($preguntas as $pregunta) { ?>
                                <article class="pregunta-profesor">
                                    <span>Pregunta <?php echo (int) $pregunta["orden"]; ?></span>

                                    <p><?php echo limpiar_texto_doa($pregunta["enunciado"]); ?></p>

                                    <ul class="pregunta-profesor__opciones">
                                        <?php foreach ($pregunta["opciones"] as $opcion) { ?>
                                            <li class="opcion-pregunta-profesor <?php echo $opcion["es_correcta"] ? "opcion-pregunta-profesor--correcta" : ""; ?>">
                                                <?php echo limpiar_texto_doa($opcion["texto"]); ?>

                                                <?php if ($opcion["es_correcta"]) { ?>
                                                    <strong>Correcta</strong>
                                                <?php } ?>
                                            </li>
                                        <?php } ?>
                                    </ul>

                                    <?php if ($pregunta["explicacion"] !== null && $pregunta["explicacion"] !== "") { ?>
                                        <p class="pregunta-profesor__explicacion">
                                            <?php echo limpiar_texto_doa($pregunta["explicacion"]); ?>
                                        </p>
                                    <?php } ?>
                                </article>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="bloque-detalle-examen">
                        <h3>Seguimiento del alumnado</h3>

                        <div class="tabla-calificaciones">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Alumno</th>
                                        <th>Respuestas</th>
                                        <th>Fecha</th>
                                        <th>Nota</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (count($seguimiento_alumnos) === 0) { ?>
                                        <tr class="fila-sin-resultados">
                                            <td colspan="5">
                                                No hay alumnos asignados a esta asignatura.
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    <?php foreach ($seguimiento_alumnos as $alumno) { ?>
                                        <?php
                                        $nombre_alumno = trim($alumno["nombre"] . " " . $alumno["apellidos"]);
                                        $estado_alumno = obtener_estado_alumno_examen_profesor($alumno["total_respuestas"], $alumno["id_calificacion"]);
                                        $texto_estado_alumno = obtener_texto_estado_alumno_examen_profesor($estado_alumno);
                                        $fecha_realizacion = formatear_fecha_hora_examen_detalle_profesor($alumno["fecha_realizacion"]);
                                        $nota = $alumno["nota"] !== null ? number_format((float) $alumno["nota"], 1) : "-";
                                        ?>

                                        <tr>
                                            <td>
                                                <div class="alumno-calificacion">
                                                    <strong><?php echo limpiar_texto_doa($nombre_alumno); ?></strong>
                                                    <small><?php echo limpiar_texto_doa($alumno["email"]); ?></small>
                                                </div>
                                            </td>

                                            <td>
                                                <?php echo (int) $alumno["total_respuestas"]; ?>/<?php echo $total_preguntas; ?>
                                            </td>

                                            <td>
                                                <?php echo limpiar_texto_doa($fecha_realizacion); ?>
                                            </td>

                                            <td>
                                                <?php if ($alumno["nota"] === null) { ?>
                                                    <span class="barra-nota-pendiente"></span>
                                                <?php } else { ?>
                                                    <span class="<?php echo (float) $alumno["nota"] >= 5 ? "nota-positiva" : "nota-negativa"; ?>">
                                                        <?php echo limpiar_texto_doa($nota); ?>
                                                    </span>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <span class="estado-calificacion estado-calificacion--<?php echo limpiar_texto_doa($estado_alumno); ?>">
                                                    <?php echo limpiar_texto_doa($texto_estado_alumno); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </article>

                <aside class="panel-accion-examen">
                    <div class="tarjeta-accion-examen">
                        <p class="tarjeta-accion-examen__titulo">Gestión del examen</p>

                        <p class="tarjeta-accion-examen__texto">
                            Revisa la configuración del examen y el seguimiento del alumnado.
                        </p>

                        <a class="boton-realizar-examen" href="<?php echo limpiar_texto_doa($url_crear_examen); ?>">
                            Crear otro examen
                        </a>
                    </div>

                    <div class="tarjeta-aviso-examen">
                        <strong>Seguimiento</strong>

                        <ul class="lista-seguimiento-examen">
                            <li>
                                <span>Alumnos del grupo</span>
                                <strong><?php echo $total_alumnos; ?></strong>
                            </li>

                            <li>
                                <span>Realizados</span>
                                <strong><?php echo $total_realizados; ?></strong>
                            </li>

                            <li>
                                <span>Sin realizar</span>
                                <strong><?php echo $total_sin_realizar; ?></strong>
                            </li>
                        </ul>
                    </div>

                    <div class="tarjeta-aviso-examen">
                        <strong>Accesos rápidos</strong>

                        <div class="acciones-rapidas-examen">
                            <a href="<?php echo limpiar_texto_doa($url_calificaciones); ?>">
                                Ver calificaciones
                            </a>

                            <a href="<?php echo limpiar_texto_doa($url_examenes); ?>">
                                Ver todos los exámenes
                            </a>
                        </div>
                    </div>
                </aside>
            </section>

            <!-- Fin detalle de examen -->
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>
</html>