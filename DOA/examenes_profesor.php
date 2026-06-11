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

function obtener_estado_examen_profesor($examen)
{
    if ($examen["estado"] === "borrador") {
        return "borrador";
    }

    if ($examen["estado"] === "cerrada") {
        return "cerrado";
    }

    $ahora = new DateTime();
    $fecha_inicio = $examen["fecha_inicio"] !== null ? new DateTime($examen["fecha_inicio"]) : null;
    $fecha_limite = $examen["fecha_limite"] !== null ? new DateTime($examen["fecha_limite"]) : null;

    if ($fecha_inicio !== null && $fecha_inicio > $ahora) {
        return "proximo";
    }

    if ($fecha_limite !== null && $fecha_limite < $ahora) {
        return "cerrado";
    }

    return "abierto";
}

function obtener_texto_estado_examen_profesor($estado)
{
    return match ($estado) {
        "abierto" => "Abierto",
        "proximo" => "Próximo",
        "cerrado" => "Cerrado",
        "borrador" => "Borrador",
        default => ucfirst($estado),
    };
}

function formatear_fecha_examen_profesor($fecha)
{
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y", strtotime($fecha));
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

$id_profesor = (int) $_SESSION["doa_id_usuario"];
$id_asignatura = isset($_GET["id_asignatura"]) ? (int) $_GET["id_asignatura"] : 0;
$filtro_estado = $_GET["estado"] ?? "todos";

if (!in_array($filtro_estado, ["todos", "abierto", "proximo", "cerrado", "borrador"], true)) {
    $filtro_estado = "todos";
}

if ($id_asignatura === 0) {
    $consulta_primera_asignatura = $pdo->prepare("
        SELECT a.id_asignatura
        FROM asignaturas a
        INNER JOIN usuarios_asignaturas ua
            ON ua.id_asignatura = a.id_asignatura
            AND ua.id_usuario = :id_profesor
            AND ua.rol_asignatura = 'profesor'
            AND ua.estado = 'activa'
        WHERE a.estado = 'activa'
        ORDER BY a.nombre ASC
        LIMIT 1
    ");

    $consulta_primera_asignatura->execute([
        "id_profesor" => $id_profesor
    ]);

    $primera_asignatura = $consulta_primera_asignatura->fetch();

    if (!$primera_asignatura) {
        header("Location: asignaturas_profesor.php");
        exit;
    }

    $id_asignatura = (int) $primera_asignatura["id_asignatura"];
}

$mensaje_ok = "";

if (isset($_GET["creado"]) && $_GET["creado"] === "ok") {
    $mensaje_ok = "Examen creado correctamente.";
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

$total_alumnos = (int) $asignatura["total_alumnos"];

// Fin consulta de asignatura


// Inicio consulta de exámenes

$consulta_examenes = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.titulo,
        ae.descripcion,
        ae.unidad,
        ae.temas,
        ae.fecha_inicio,
        ae.fecha_limite,
        ae.duracion_minutos,
        ae.intentos_maximos,
        ae.visible,
        ae.estado,
        COUNT(DISTINCT p.id_pregunta) AS total_preguntas,
        COUNT(DISTINCT re.id_alumno) AS total_realizados,
        COUNT(DISTINCT c.id_calificacion) AS total_calificados
    FROM actividades_evaluables ae
    LEFT JOIN preguntas_examen p
        ON p.id_actividad = ae.id_actividad
    LEFT JOIN respuestas_examen re
        ON re.id_pregunta = p.id_pregunta
    LEFT JOIN calificaciones c
        ON c.id_actividad = ae.id_actividad
    WHERE ae.id_asignatura = :id_asignatura
    AND ae.id_profesor = :id_profesor
    AND ae.tipo_actividad = 'examen'
    GROUP BY
        ae.id_actividad,
        ae.titulo,
        ae.descripcion,
        ae.unidad,
        ae.temas,
        ae.fecha_inicio,
        ae.fecha_limite,
        ae.duracion_minutos,
        ae.intentos_maximos,
        ae.visible,
        ae.estado
    ORDER BY ae.fecha_inicio DESC, ae.fecha_creacion DESC
");

$consulta_examenes->execute([
    "id_asignatura" => $id_asignatura,
    "id_profesor" => $id_profesor
]);

$examenes_originales = $consulta_examenes->fetchAll();

$examenes = [];
$total_examenes_publicados = 0;
$total_examenes_abiertos = 0;
$total_examenes_realizados = 0;
$total_sin_calificar = 0;

foreach ($examenes_originales as $examen) {
    $estado_calculado = obtener_estado_examen_profesor($examen);
    $total_realizados = (int) $examen["total_realizados"];
    $total_calificados = (int) $examen["total_calificados"];
    $sin_calificar = max(0, $total_realizados - $total_calificados);

    if ($examen["estado"] !== "borrador") {
        $total_examenes_publicados++;
    }

    if ($estado_calculado === "abierto") {
        $total_examenes_abiertos++;
    }

    $total_examenes_realizados += $total_realizados;
    $total_sin_calificar += $sin_calificar;

    if ($filtro_estado !== "todos" && $estado_calculado !== $filtro_estado) {
        continue;
    }

    $examen["estado_calculado"] = $estado_calculado;
    $examen["sin_calificar"] = $sin_calificar;
    $examenes[] = $examen;
}

// Fin consulta de exámenes


// Inicio enlaces de navegación

$url_detalle = "detalle_asignatura_profesor.php?id_asignatura=" . $id_asignatura;
$url_recursos = "recursos_profesor.php?id_asignatura=" . $id_asignatura;
$url_tareas = "listado_tareas_profesor.php?id_asignatura=" . $id_asignatura;
$url_examenes = "examenes_profesor.php?id_asignatura=" . $id_asignatura;
$url_crear_examen = "crear_examen.php?id_asignatura=" . $id_asignatura;
$url_calificaciones = "calificaciones_profesor.php?id_asignatura=" . $id_asignatura;

// Fin enlaces de navegación
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Exámenes · <?php echo limpiar_texto_doa($asignatura["nombre"]); ?> | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/examenes.css" rel="stylesheet">
    <link href="css/examenes_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-examenes pagina-examenes-profesor">
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
                    <a class="enlace-volver-asignaturas" href="<?php echo limpiar_texto_doa($url_detalle); ?>">
                        <span class="enlace-volver-asignaturas__icono" aria-hidden="true">
                            <img src="img/iconos/grey-chevron-right.svg" alt="">
                        </span>

                        <span>Volver a detalles de la asignatura</span>
                    </a>

                    <h1><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></h1>

                    <ul class="metadatos-asignatura">
                        <li>
                            <img src="img/iconos/grey-graduation-cap.svg" alt="">
                            <span>
                                <?php echo limpiar_texto_doa($asignatura["curso"]); ?>
                                · Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                            </span>
                        </li>

                        <li>
                            <img src="img/iconos/grey-user.svg" alt="">
                            <span><?php echo $total_alumnos; ?> alumnos</span>
                        </li>

                        <li>
                            <img src="img/iconos/grey-notebook.svg" alt="">
                            <span><?php echo limpiar_texto_doa($asignatura["codigo"]); ?></span>
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


            <!-- Inicio acciones y resumen -->

            <section class="cabecera-seccion-profesor">
                <div>
                    <h2>Exámenes de la asignatura</h2>

                    <p>
                        Consulta los exámenes publicados, revisa su estado y accede al detalle de cada prueba.
                    </p>
                </div>

                <a class="boton-profesor-principal" href="<?php echo limpiar_texto_doa($url_crear_examen); ?>">
                    Crear examen
                </a>
            </section>

            <?php if ($mensaje_ok !== "") { ?>
                <p class="mensaje-sin-examenes">
                    <?php echo limpiar_texto_doa($mensaje_ok); ?>
                </p>
            <?php } ?>

            <section class="resumen-docente" aria-label="Resumen de exámenes">
                <article class="tarjeta-resumen-docente tarjeta-resumen-docente--principal">
                    <span>Exámenes publicados</span>
                    <strong><?php echo $total_examenes_publicados; ?></strong>
                </article>

                <article class="tarjeta-resumen-docente">
                    <span>Abiertos</span>
                    <strong><?php echo $total_examenes_abiertos; ?></strong>
                </article>

                <article class="tarjeta-resumen-docente">
                    <span>Realizados</span>
                    <strong><?php echo $total_examenes_realizados; ?></strong>
                </article>

                <article class="tarjeta-resumen-docente">
                    <span>Sin calificar</span>
                    <strong><?php echo $total_sin_calificar; ?></strong>
                </article>
            </section>

            <!-- Fin acciones y resumen -->


            <!-- Inicio listado de exámenes -->

            <section class="seccion-examenes-profesor">
                <div class="cabecera-listado-examenes">
                    <h2>Listado de exámenes</h2>

                    <form class="filtros-examenes-profesor" method="get">
                        <input type="hidden" name="id_asignatura" value="<?php echo $id_asignatura; ?>">

                        <button class="filtro-examen <?php echo $filtro_estado === "todos" ? "filtro-examen--activo" : ""; ?>" name="estado" value="todos" type="submit">
                            Todos
                        </button>

                        <button class="filtro-examen <?php echo $filtro_estado === "abierto" ? "filtro-examen--activo" : ""; ?>" name="estado" value="abierto" type="submit">
                            Abiertos
                        </button>

                        <button class="filtro-examen <?php echo $filtro_estado === "proximo" ? "filtro-examen--activo" : ""; ?>" name="estado" value="proximo" type="submit">
                            Próximos
                        </button>

                        <button class="filtro-examen <?php echo $filtro_estado === "cerrado" ? "filtro-examen--activo" : ""; ?>" name="estado" value="cerrado" type="submit">
                            Cerrados
                        </button>

                        <button class="filtro-examen <?php echo $filtro_estado === "borrador" ? "filtro-examen--activo" : ""; ?>" name="estado" value="borrador" type="submit">
                            Borradores
                        </button>
                    </form>
                </div>

                <div class="tabla-examenes-profesor">
                    <div class="tabla-examenes-profesor__cabecera">
                        <span>Examen</span>
                        <span>Fecha</span>
                        <span>Duración</span>
                        <span>Realizados</span>
                        <span>Sin calificar</span>
                        <span>Estado</span>
                        <span>Acción</span>
                    </div>

                    <div>
                        <?php if (count($examenes) === 0) { ?>
                            <p class="mensaje-sin-examenes">
                                No hay exámenes que coincidan con el filtro seleccionado.
                            </p>
                        <?php } ?>

                        <?php foreach ($examenes as $examen) { ?>
                            <?php
                            $estado_examen = $examen["estado_calculado"];
                            $texto_estado = obtener_texto_estado_examen_profesor($estado_examen);
                            $fecha_examen = formatear_fecha_examen_profesor($examen["fecha_inicio"]);
                            $duracion = $examen["duracion_minutos"] !== null ? (int) $examen["duracion_minutos"] . " min" : "-";
                            $realizados = (int) $examen["total_realizados"];
                            $sin_calificar = (int) $examen["sin_calificar"];
                            $url_detalle_examen = "detalle_examen_profesor.php?id_actividad=" . (int) $examen["id_actividad"];
                            ?>

                            <article class="fila-examen-profesor">
                                <div class="fila-examen-profesor__nombre">
                                    <strong><?php echo limpiar_texto_doa($examen["titulo"]); ?></strong>

                                    <span>
                                        <?php echo limpiar_texto_doa($examen["unidad"]); ?>
                                        · <?php echo (int) $examen["total_preguntas"]; ?> preguntas
                                    </span>
                                </div>

                                <span><?php echo limpiar_texto_doa($fecha_examen); ?></span>
                                <span><?php echo limpiar_texto_doa($duracion); ?></span>
                                <span><?php echo $realizados; ?>/<?php echo $total_alumnos; ?></span>
                                <span><?php echo $sin_calificar; ?></span>

                                <span>
                                    <span class="etiqueta-examen etiqueta-examen--<?php echo limpiar_texto_doa($estado_examen); ?>">
                                        <?php echo limpiar_texto_doa($texto_estado); ?>
                                    </span>
                                </span>

                                <a class="fila-examen-profesor__accion" href="<?php echo limpiar_texto_doa($url_detalle_examen); ?>">
                                    Detalles
                                </a>
                            </article>
                        <?php } ?>
                    </div>
                </div>
            </section>

            <!-- Fin listado de exámenes -->
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>

</html>