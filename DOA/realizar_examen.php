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

function formatear_fecha_realizar_examen($fecha)
{
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y", strtotime($fecha));
}

function obtener_estado_realizar_examen($examen, $total_respuestas, $id_calificacion)
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

// Fin funciones auxiliares


// Inicio parámetros de pantalla

if (!isset($_GET["id_actividad"]) && !isset($_POST["id_actividad"])) {
    header("Location: asignaturas.php");
    exit;
}

$id_alumno = (int) $_SESSION["doa_id_usuario"];
$id_actividad = isset($_POST["id_actividad"])
    ? (int) $_POST["id_actividad"]
    : (int) $_GET["id_actividad"];

$errores = [];
$respuestas_formulario = [];

// Fin parámetros de pantalla


// Inicio consulta de examen

$consulta_examen = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.id_asignatura,
        ae.id_profesor,
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
        a.grupo
    FROM actividades_evaluables ae
    INNER JOIN asignaturas a
        ON a.id_asignatura = ae.id_asignatura
    INNER JOIN usuarios_asignaturas ua_alumno
        ON ua_alumno.id_asignatura = ae.id_asignatura
        AND ua_alumno.id_usuario = :id_alumno
        AND ua_alumno.rol_asignatura = 'alumno'
        AND ua_alumno.estado = 'activa'
    WHERE ae.id_actividad = :id_actividad
    AND ae.tipo_actividad = 'examen'
    AND ae.visible = 1
    AND ae.estado = 'publicada'
    AND a.estado = 'activa'
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
$id_profesor = (int) $examen["id_profesor"];

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
    INNER JOIN opciones_pregunta o
        ON o.id_pregunta = p.id_pregunta
    WHERE p.id_actividad = :id_actividad
    ORDER BY p.orden ASC, o.orden ASC
");

$consulta_preguntas->execute([
    "id_actividad" => $id_actividad
]);

$filas_preguntas = $consulta_preguntas->fetchAll();
$preguntas = [];

foreach ($filas_preguntas as $fila) {
    $id_pregunta = (int) $fila["id_pregunta"];

    if (!isset($preguntas[$id_pregunta])) {
        $preguntas[$id_pregunta] = [
            "id_pregunta" => $id_pregunta,
            "enunciado" => $fila["enunciado"],
            "orden" => (int) $fila["orden_pregunta"],
            "explicacion" => $fila["explicacion"],
            "id_opcion_correcta" => null,
            "opciones" => []
        ];
    }

    $id_opcion = (int) $fila["id_opcion"];

    if ((int) $fila["es_correcta"] === 1) {
        $preguntas[$id_pregunta]["id_opcion_correcta"] = $id_opcion;
    }

    $preguntas[$id_pregunta]["opciones"][] = [
        "id_opcion" => $id_opcion,
        "texto" => $fila["texto_opcion"],
        "es_correcta" => (int) $fila["es_correcta"] === 1
    ];
}

$total_preguntas = count($preguntas);

// Fin consulta de preguntas


// Inicio consulta de estado previo

$consulta_respuestas_previas = $pdo->prepare("
    SELECT COUNT(DISTINCT re.id_respuesta) AS total_respuestas
    FROM preguntas_examen p
    INNER JOIN respuestas_examen re
        ON re.id_pregunta = p.id_pregunta
        AND re.id_alumno = :id_alumno
    WHERE p.id_actividad = :id_actividad
");

$consulta_respuestas_previas->execute([
    "id_alumno" => $id_alumno,
    "id_actividad" => $id_actividad
]);

$total_respuestas_previas = (int) $consulta_respuestas_previas->fetch()["total_respuestas"];

$consulta_calificacion_previa = $pdo->prepare("
    SELECT id_calificacion
    FROM calificaciones
    WHERE id_actividad = :id_actividad
    AND id_alumno = :id_alumno
    LIMIT 1
");

$consulta_calificacion_previa->execute([
    "id_actividad" => $id_actividad,
    "id_alumno" => $id_alumno
]);

$calificacion_previa = $consulta_calificacion_previa->fetch();

$estado_examen = obtener_estado_realizar_examen(
    $examen,
    $total_respuestas_previas,
    $calificacion_previa["id_calificacion"] ?? null
);

if ($estado_examen !== "abierto" || $total_preguntas === 0) {
    header("Location: detalle_examen.php?id_actividad=" . $id_actividad);
    exit;
}

// Fin consulta de estado previo


// Inicio guardado de respuestas

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($_POST["respuestas"] ?? [] as $id_pregunta_post => $id_opcion_post) {
        $respuestas_formulario[(int) $id_pregunta_post] = (int) $id_opcion_post;
    }

    $faltan_respuestas = false;
    $respuesta_no_valida = false;

    foreach ($preguntas as $id_pregunta => $pregunta) {
        if (!isset($respuestas_formulario[$id_pregunta])) {
            $faltan_respuestas = true;
            continue;
        }

        $opciones_validas = array_column($pregunta["opciones"], "id_opcion");

        if (!in_array($respuestas_formulario[$id_pregunta], $opciones_validas, true)) {
            $respuesta_no_valida = true;
        }
    }

    if ($faltan_respuestas) {
        $errores[] = "Responde todas las preguntas antes de entregar el examen.";
    }

    if ($respuesta_no_valida) {
        $errores[] = "Una de las respuestas seleccionadas no pertenece a este examen.";
    }

    if (count($errores) === 0) {
        $respuestas_correctas = 0;

        foreach ($preguntas as $id_pregunta => $pregunta) {
            if ($respuestas_formulario[$id_pregunta] === (int) $pregunta["id_opcion_correcta"]) {
                $respuestas_correctas++;
            }
        }

        $nota = round(($respuestas_correctas / $total_preguntas) * 10, 2);
        $comentario_profesor = "Corrección automática: " . $respuestas_correctas . " de " . $total_preguntas . " respuestas correctas.";

        $pdo->beginTransaction();

        try {
            $insertar_respuesta = $pdo->prepare("
                INSERT INTO respuestas_examen
                    (id_alumno, id_pregunta, id_opcion)
                VALUES
                    (:id_alumno, :id_pregunta, :id_opcion)
            ");

            foreach ($preguntas as $id_pregunta => $pregunta) {
                $insertar_respuesta->execute([
                    "id_alumno" => $id_alumno,
                    "id_pregunta" => $id_pregunta,
                    "id_opcion" => $respuestas_formulario[$id_pregunta]
                ]);
            }

            $insertar_calificacion = $pdo->prepare("
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

            $insertar_calificacion->execute([
                "id_actividad" => $id_actividad,
                "id_alumno" => $id_alumno,
                "id_profesor" => $id_profesor,
                "nota" => number_format($nota, 2, ".", ""),
                "comentario_profesor" => $comentario_profesor
            ]);

            $pdo->commit();

            header("Location: detalle_examen.php?id_actividad=" . $id_actividad . "&realizado=ok");
            exit;
        } catch (Throwable $error) {
            $pdo->rollBack();
            throw $error;
        }
    }
}

// Fin guardado de respuestas


// Inicio datos derivados

$fecha_examen = formatear_fecha_realizar_examen($examen["fecha_inicio"]);
$duracion = $examen["duracion_minutos"] !== null ? (int) $examen["duracion_minutos"] . " min" : "-";
$intentos = "1 intento";

$url_detalle_examen = "detalle_examen.php?id_actividad=" . $id_actividad;

// Fin datos derivados
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Realizar · <?php echo limpiar_texto_doa($examen["titulo"]); ?> | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/realizar_examen.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-realizar-examen">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido principal -->

        <main class="contenido-doa">
            <!-- Inicio cabecera del examen -->

            <section class="cabecera-detalle-asignatura">
                <div class="cabecera-detalle-asignatura__texto">
                    <a class="enlace-volver-asignaturas" href="<?php echo limpiar_texto_doa($url_detalle_examen); ?>">
                        <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                            <img alt="" src="img/iconos/grey-chevron-right.svg">
                        </span>

                        <span>Volver al detalle del examen</span>
                    </a>

                    <h1><?php echo limpiar_texto_doa($examen["titulo"]); ?></h1>

                    <ul class="metadatos-asignatura">
                        <li>
                            <img alt="" src="img/iconos/grey-notebook.svg">
                            <span><?php echo limpiar_texto_doa($examen["asignatura_nombre"]); ?></span>
                        </li>

                        <li>
                            <img alt="" src="img/iconos/grey-calendar.svg">
                            <span><?php echo limpiar_texto_doa($fecha_examen); ?></span>
                        </li>

                        <li>
                            <img alt="" src="img/iconos/grey-clock.svg">
                            <span><?php echo limpiar_texto_doa($duracion); ?></span>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Fin cabecera del examen -->


            <!-- Inicio formulario de examen -->

            <section class="realizar-examen-grid">
                <form class="formulario-examen" method="post">
                    <input type="hidden" name="id_actividad" value="<?php echo $id_actividad; ?>">

                    <div class="tarjeta-instrucciones-examen">
                        <h2>Preguntas tipo test</h2>

                        <p>
                            Selecciona una respuesta por pregunta. Al entregar el examen, tus respuestas
                            se guardarán en la base de datos y se calculará la calificación automáticamente.
                        </p>
                    </div>

                    <div class="lista-preguntas-examen">
                        <?php foreach ($preguntas as $pregunta) { ?>
                            <article class="pregunta-examen">
                                <div class="pregunta-examen__cabecera">
                                    <span class="pregunta-examen__numero">
                                        <?php echo (int) $pregunta["orden"]; ?>
                                    </span>

                                    <h3><?php echo limpiar_texto_doa($pregunta["enunciado"]); ?></h3>
                                </div>

                                <div class="opciones-pregunta">
                                    <?php foreach ($pregunta["opciones"] as $opcion) { ?>
                                        <?php
                                        $id_pregunta = (int) $pregunta["id_pregunta"];
                                        $id_opcion = (int) $opcion["id_opcion"];
                                        $opcion_marcada = isset($respuestas_formulario[$id_pregunta]) && $respuestas_formulario[$id_pregunta] === $id_opcion;
                                        ?>

                                        <label class="opcion-pregunta">
                                            <input
                                                type="radio"
                                                name="respuestas[<?php echo $id_pregunta; ?>]"
                                                value="<?php echo $id_opcion; ?>"
                                                <?php echo $opcion_marcada ? "checked" : ""; ?>
                                                required>

                                            <span><?php echo limpiar_texto_doa($opcion["texto"]); ?></span>
                                        </label>
                                    <?php } ?>
                                </div>
                            </article>
                        <?php } ?>
                    </div>

                    <?php if (count($errores) > 0) { ?>
                        <p class="mensaje-error-examen">
                            <?php echo limpiar_texto_doa($errores[0]); ?>
                        </p>
                    <?php } ?>

                    <div class="acciones-examen">
                        <button class="boton-entregar-examen" type="submit">
                            Entregar examen
                        </button>

                        <a class="boton-secundario-examen" href="<?php echo limpiar_texto_doa($url_detalle_examen); ?>">
                            Cancelar
                        </a>
                    </div>
                </form>

                <aside class="panel-estado-examen">
                    <div class="tarjeta-estado-examen">
                        <p class="tarjeta-estado-examen__titulo">Progreso</p>

                        <div class="progreso-realizacion-examen">
                            <span>
                                <?php echo $total_preguntas; ?> preguntas por responder
                            </span>

                            <span>
                                El examen se guarda al pulsar Entregar examen.
                            </span>
                        </div>
                    </div>

                    <div class="tarjeta-estado-examen">
                        <p class="tarjeta-estado-examen__titulo">Información</p>

                        <ul class="lista-info-examen">
                            <li>
                                <span>Preguntas</span>
                                <strong><?php echo $total_preguntas; ?></strong>
                            </li>

                            <li>
                                <span>Intentos</span>
                                <strong><?php echo limpiar_texto_doa($intentos); ?></strong>
                            </li>

                            <li>
                                <span>Estado</span>
                                <strong>En curso</strong>
                            </li>
                        </ul>
                    </div>
                </aside>
            </section>

            <!-- Fin formulario de examen -->
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>
</html>