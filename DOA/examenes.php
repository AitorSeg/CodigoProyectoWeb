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

function obtener_estado_examen_alumno($examen) {
    if ($examen["id_calificacion"] !== null || (int) $examen["total_respuestas"] > 0) {
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

function obtener_texto_estado_examen_alumno($estado) {
    return match ($estado) {
        "abierto" => "Abierto",
        "proximo" => "Próximo",
        "cerrado" => "Cerrado",
        "realizado" => "Realizado",
        default => ucfirst($estado),
    };
}

function formatear_fecha_examen_alumno($fecha) {
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y", strtotime($fecha));
}

function formatear_fecha_larga_examen_alumno($fecha) {
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y H:i", strtotime($fecha));
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

$id_alumno = (int) $_SESSION["doa_id_usuario"];
$id_asignatura = isset($_GET["id_asignatura"]) ? (int) $_GET["id_asignatura"] : 0;
$filtro_estado = $_GET["estado"] ?? "todos";

if (!in_array($filtro_estado, ["todos", "abierto", "proximo", "cerrado", "realizado"], true)) {
    $filtro_estado = "todos";
}

if ($id_asignatura === 0) {
    $consulta_primera_asignatura = $pdo->prepare("
        SELECT a.id_asignatura
        FROM asignaturas a
        INNER JOIN usuarios_asignaturas ua
            ON ua.id_asignatura = a.id_asignatura
            AND ua.id_usuario = :id_alumno
            AND ua.rol_asignatura = 'alumno'
            AND ua.estado = 'activa'
        WHERE a.estado = 'activa'
        ORDER BY a.nombre ASC
        LIMIT 1
    ");

    $consulta_primera_asignatura->execute([
        "id_alumno" => $id_alumno
    ]);

    $primera_asignatura = $consulta_primera_asignatura->fetch();

    if (!$primera_asignatura) {
        header("Location: asignaturas.php");
        exit;
    }

    $id_asignatura = (int) $primera_asignatura["id_asignatura"];
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
        GROUP_CONCAT(
            DISTINCT CONCAT(u_profesor.nombre, ' ', u_profesor.apellidos)
            SEPARATOR ', '
        ) AS profesores
    FROM asignaturas a
    INNER JOIN usuarios_asignaturas ua_alumno
        ON ua_alumno.id_asignatura = a.id_asignatura
        AND ua_alumno.id_usuario = :id_alumno
        AND ua_alumno.rol_asignatura = 'alumno'
        AND ua_alumno.estado = 'activa'
    LEFT JOIN usuarios_asignaturas ua_profesor
        ON ua_profesor.id_asignatura = a.id_asignatura
        AND ua_profesor.rol_asignatura = 'profesor'
        AND ua_profesor.estado = 'activa'
    LEFT JOIN usuarios u_profesor
        ON u_profesor.id_usuario = ua_profesor.id_usuario
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
    "id_alumno" => $id_alumno,
    "id_asignatura" => $id_asignatura
]);

$asignatura = $consulta_asignatura->fetch();

if (!$asignatura) {
    header("Location: asignaturas.php");
    exit;
}

$profesores = $asignatura["profesores"] !== null ? $asignatura["profesores"] : "Pendiente";

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
        ae.estado,
        COUNT(DISTINCT p.id_pregunta) AS total_preguntas,
        COUNT(DISTINCT re.id_respuesta) AS total_respuestas,
        MAX(re.fecha_respuesta) AS fecha_realizacion,
        c.id_calificacion,
        c.nota
    FROM actividades_evaluables ae
    LEFT JOIN preguntas_examen p
        ON p.id_actividad = ae.id_actividad
    LEFT JOIN respuestas_examen re
        ON re.id_pregunta = p.id_pregunta
        AND re.id_alumno = :id_alumno_respuestas
    LEFT JOIN calificaciones c
        ON c.id_actividad = ae.id_actividad
        AND c.id_alumno = :id_alumno_calificacion
    WHERE ae.id_asignatura = :id_asignatura
    AND ae.tipo_actividad = 'examen'
    AND ae.visible = 1
    AND ae.estado = 'publicada'
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
        ae.estado,
        c.id_calificacion,
        c.nota
    ORDER BY ae.fecha_inicio ASC, ae.fecha_creacion ASC
");

$consulta_examenes->execute([
    "id_alumno_respuestas" => $id_alumno,
    "id_alumno_calificacion" => $id_alumno,
    "id_asignatura" => $id_asignatura
]);

$examenes_originales = $consulta_examenes->fetchAll();

$examenes = [];
$total_abiertos = 0;
$total_realizados = 0;
$examen_destacado = null;
$proximo_examen = null;

foreach ($examenes_originales as $examen) {
    $estado_examen = obtener_estado_examen_alumno($examen);

    if ($estado_examen === "abierto") {
        $total_abiertos++;
    }

    if ($estado_examen === "realizado") {
        $total_realizados++;
    }

    if (!$proximo_examen && in_array($estado_examen, ["abierto", "proximo"], true)) {
        $proximo_examen = $examen;
        $proximo_examen["estado_calculado"] = $estado_examen;
    }

    if (!$examen_destacado && $estado_examen === "abierto") {
        $examen_destacado = $examen;
        $examen_destacado["estado_calculado"] = $estado_examen;
    }

    if ($filtro_estado !== "todos" && $estado_examen !== $filtro_estado) {
        continue;
    }

    $examen["estado_calculado"] = $estado_examen;
    $examenes[] = $examen;
}

if (!$examen_destacado && $proximo_examen) {
    $examen_destacado = $proximo_examen;
}

if (!$examen_destacado && count($examenes_originales) > 0) {
    $examen_destacado = $examenes_originales[0];
    $examen_destacado["estado_calculado"] = obtener_estado_examen_alumno($examen_destacado);
}

$texto_proximo_examen = $proximo_examen
    ? formatear_fecha_examen_alumno($proximo_examen["fecha_inicio"])
    : "---";

// Fin consulta de exámenes


// Inicio enlaces de navegación

$url_detalle = "detalle_asignatura.php?id_asignatura=" . $id_asignatura;
$url_recursos = "recursos_alumno.php?id_asignatura=" . $id_asignatura;
$url_tareas = "listado_tareas.php?id_asignatura=" . $id_asignatura;
$url_examenes = "examenes.php?id_asignatura=" . $id_asignatura;
$url_calificaciones = "calificaciones.php?id_asignatura=" . $id_asignatura;

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

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-examenes">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido principal -->

        <main class="contenido-doa contenido-detalle-asignatura contenido-examenes">
            <section class="detalle-asignatura-principal">
                <!-- Inicio cabecera de asignatura -->

                <div class="cabecera-detalle-asignatura">
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
                                <img src="img/iconos/grey-user.svg" alt="">
                                <span><?php echo limpiar_texto_doa($profesores); ?></span>
                            </li>

                            <li>
                                <img src="img/iconos/grey-notebook.svg" alt="">
                                <span>
                                    <?php echo limpiar_texto_doa($asignatura["codigo"]); ?>
                                    · <?php echo limpiar_texto_doa($asignatura["curso"]); ?>
                                    · Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                                </span>
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
                </div>

                <!-- Fin cabecera de asignatura -->


                <!-- Inicio resumen de exámenes -->

                <section class="resumen-metricas resumen-metricas--tres resumen-metricas--compacto" aria-label="Resumen de exámenes">
                    <article class="tarjeta-metrica">
                        <span>Exámenes abiertos</span>
                        <strong><?php echo $total_abiertos; ?></strong>
                    </article>

                    <article class="tarjeta-metrica">
                        <span>Próximo examen</span>
                        <strong><?php echo limpiar_texto_doa($texto_proximo_examen); ?></strong>
                    </article>

                    <article class="tarjeta-metrica">
                        <span>Realizados</span>
                        <strong><?php echo $total_realizados; ?></strong>
                    </article>
                </section>

                <!-- Fin resumen de exámenes -->


                <!-- Inicio examen destacado -->

                <?php if ($examen_destacado) { ?>
                    <?php
                    $estado_destacado = $examen_destacado["estado_calculado"];
                    $texto_estado_destacado = obtener_texto_estado_examen_alumno($estado_destacado);
                    $url_destacado = "detalle_examen.php?id_actividad=" . (int) $examen_destacado["id_actividad"];

                    if ($estado_destacado === "proximo") {
                        $texto_fecha_destacada = "Disponible desde";
                        $fecha_destacada = formatear_fecha_larga_examen_alumno($examen_destacado["fecha_inicio"]);
                    } elseif ($estado_destacado === "realizado") {
                        $texto_fecha_destacada = "Realizado";
                        $fecha_destacada = formatear_fecha_larga_examen_alumno($examen_destacado["fecha_realizacion"]);
                    } else {
                        $texto_fecha_destacada = "Disponible hasta";
                        $fecha_destacada = formatear_fecha_larga_examen_alumno($examen_destacado["fecha_limite"]);
                    }

                    $texto_boton_destacado = $estado_destacado === "abierto" ? "Entrar" : "Ver detalles";
                    ?>

                    <section class="examen-destacado" data-estado="<?php echo limpiar_texto_doa($estado_destacado); ?>">
                        <div class="examen-destacado__contenido">
                            <span class="etiqueta-examen etiqueta-examen--<?php echo limpiar_texto_doa($estado_destacado); ?>">
                                <?php echo limpiar_texto_doa($texto_estado_destacado); ?>
                            </span>

                            <h2><?php echo limpiar_texto_doa($examen_destacado["titulo"]); ?></h2>

                            <p>
                                <?php echo limpiar_texto_doa($examen_destacado["descripcion"]); ?>
                            </p>
                        </div>

                        <div class="examen-destacado__accion">
                            <p>
                                <span><?php echo limpiar_texto_doa($texto_fecha_destacada); ?></span>
                                <strong><?php echo limpiar_texto_doa($fecha_destacada); ?></strong>
                            </p>

                            <a class="boton-entrar-examen" href="<?php echo limpiar_texto_doa($url_destacado); ?>">
                                <?php echo limpiar_texto_doa($texto_boton_destacado); ?>
                            </a>
                        </div>
                    </section>
                <?php } ?>

                <!-- Fin examen destacado -->


                <!-- Inicio listado de exámenes -->

                <section class="bloque-listado-examenes">
                    <div class="cabecera-listado-examenes">
                        <h2>Exámenes de la asignatura</h2>

                        <form class="filtros-examenes" method="get" aria-label="Filtros de exámenes">
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

                            <button class="filtro-examen <?php echo $filtro_estado === "realizado" ? "filtro-examen--activo" : ""; ?>" name="estado" value="realizado" type="submit">
                                Realizados
                            </button>
                        </form>
                    </div>

                    <div class="tabla-examenes">
                        <div class="tabla-examenes__cabecera">
                            <p>Examen</p>
                            <p>Fecha</p>
                            <p>Duración</p>
                            <p>Estado</p>
                            <p>Acción</p>
                        </div>

                        <div class="tabla-examenes__contenido">
                            <?php if (count($examenes) === 0) { ?>
                                <p class="mensaje-sin-examenes">
                                    No hay exámenes que coincidan con el filtro seleccionado.
                                </p>
                            <?php } ?>

                            <?php foreach ($examenes as $examen) { ?>
                                <?php
                                $estado_examen = $examen["estado_calculado"];
                                $texto_estado_examen = obtener_texto_estado_examen_alumno($estado_examen);
                                $fecha_examen = formatear_fecha_examen_alumno($examen["fecha_inicio"]);
                                $duracion = $examen["duracion_minutos"] !== null ? (int) $examen["duracion_minutos"] . " min" : "-";
                                $url_examen = "detalle_examen.php?id_actividad=" . (int) $examen["id_actividad"];
                                $texto_accion = $estado_examen === "abierto" ? "Entrar" : "Ver detalles";
                                $clase_accion = $estado_examen === "abierto"
                                    ? "fila-examen__accion fila-examen__accion--principal"
                                    : "fila-examen__accion";
                                ?>

                                <article class="fila-examen">
                                    <div class="fila-examen__nombre">
                                        <strong><?php echo limpiar_texto_doa($examen["titulo"]); ?></strong>

                                        <span>
                                            <?php echo limpiar_texto_doa($examen["unidad"]); ?>
                                            · <?php echo (int) $examen["total_preguntas"]; ?> preguntas
                                        </span>
                                    </div>

                                    <p class="fila-examen__fecha" data-duracion="<?php echo limpiar_texto_doa($duracion); ?>">
                                        <?php echo limpiar_texto_doa($fecha_examen); ?>
                                    </p>

                                    <p class="fila-examen__duracion">
                                        <?php echo limpiar_texto_doa($duracion); ?>
                                    </p>

                                    <p class="fila-examen__estado">
                                        <span class="etiqueta-examen etiqueta-examen--<?php echo limpiar_texto_doa($estado_examen); ?>">
                                            <?php echo limpiar_texto_doa($texto_estado_examen); ?>
                                        </span>
                                    </p>

                                    <a class="<?php echo limpiar_texto_doa($clase_accion); ?>" href="<?php echo limpiar_texto_doa($url_examen); ?>">
                                        <?php echo limpiar_texto_doa($texto_accion); ?>
                                    </a>
                                </article>
                            <?php } ?>
                        </div>
                    </div>
                </section>

                <!-- Fin listado de exámenes -->
            </section>
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>
</html>