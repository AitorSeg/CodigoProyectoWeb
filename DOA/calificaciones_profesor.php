<?php
// Inicio configuración de página

$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar alumno, calificación...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página


// Inicio funciones auxiliares

function formatear_media_profesor($nota) {
    if ($nota === null) {
        return "-";
    }

    return number_format((float) $nota, 1);
}

function obtener_estado_alumno_calificacion($nota_media, $total_actividades, $total_calificadas) {
    if ((int) $total_actividades === 0 || (int) $total_calificadas === 0) {
        return "pendiente";
    }

    if ((int) $total_calificadas < (int) $total_actividades) {
        return "pendiente";
    }

    return (float) $nota_media >= 5 ? "aprobado" : "suspendido";
}

function obtener_texto_estado_alumno_calificacion($estado) {
    return match ($estado) {
        "aprobado" => "Aprobado",
        "suspendido" => "Suspendido",
        "pendiente" => "Pendiente",
        default => ucfirst($estado),
    };
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

$id_profesor = (int) $_SESSION["doa_id_usuario"];

$filtro_estado = $_GET["estado"] ?? "todos";
$orden_calificaciones = $_GET["orden"] ?? "nota";

if (!in_array($filtro_estado, ["todos", "aprobado", "suspendido", "pendiente"], true)) {
    $filtro_estado = "todos";
}

if (!in_array($orden_calificaciones, ["nota", "nombre", "pendientes"], true)) {
    $orden_calificaciones = "nota";
}

// Fin parámetros de pantalla


// Inicio selección de asignatura

$id_asignatura = isset($_GET["id_asignatura"]) ? (int) $_GET["id_asignatura"] : 0;

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

// Fin selección de asignatura


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


// Inicio consulta de calificaciones del grupo

$consulta_alumnos = $pdo->prepare("
    SELECT
        u.id_usuario,
        u.nombre,
        u.apellidos,
        u.email,
        COUNT(DISTINCT ae.id_actividad) AS total_actividades,
        COUNT(DISTINCT c.id_calificacion) AS total_calificadas,
        AVG(c.nota) AS nota_media,
        AVG(CASE WHEN ae.tipo_actividad IN ('tarea', 'practica') THEN c.nota END) AS media_tareas,
        AVG(CASE WHEN ae.tipo_actividad = 'examen' THEN c.nota END) AS media_examenes
    FROM usuarios_asignaturas ua_alumno
    INNER JOIN usuarios u
        ON u.id_usuario = ua_alumno.id_usuario
    LEFT JOIN actividades_evaluables ae
        ON ae.id_asignatura = ua_alumno.id_asignatura
        AND ae.tipo_actividad IN ('tarea', 'practica', 'examen')
        AND ae.visible = 1
        AND ae.estado = 'publicada'
    LEFT JOIN calificaciones c
        ON c.id_actividad = ae.id_actividad
        AND c.id_alumno = u.id_usuario
    WHERE ua_alumno.id_asignatura = :id_asignatura
    AND ua_alumno.rol_asignatura = 'alumno'
    AND ua_alumno.estado = 'activa'
    GROUP BY
        u.id_usuario,
        u.nombre,
        u.apellidos,
        u.email
");

$consulta_alumnos->execute([
    "id_asignatura" => $id_asignatura
]);

$alumnos_originales = $consulta_alumnos->fetchAll();

$alumnos = [];
$notas_grupo = [];
$total_aprobados = 0;
$total_suspendidos = 0;
$total_pendientes_correccion = 0;

foreach ($alumnos_originales as $alumno) {
    $pendientes_alumno = (int) $alumno["total_actividades"] - (int) $alumno["total_calificadas"];
    $estado_alumno = obtener_estado_alumno_calificacion(
        $alumno["nota_media"],
        $alumno["total_actividades"],
        $alumno["total_calificadas"]
    );

    if ($alumno["nota_media"] !== null) {
        $notas_grupo[] = (float) $alumno["nota_media"];
    }

    if ($estado_alumno === "aprobado") {
        $total_aprobados++;
    }

    if ($estado_alumno === "suspendido") {
        $total_suspendidos++;
    }

    $total_pendientes_correccion += max(0, $pendientes_alumno);

    if ($filtro_estado !== "todos" && $estado_alumno !== $filtro_estado) {
        continue;
    }

    $alumno["pendientes"] = max(0, $pendientes_alumno);
    $alumno["estado_alumno"] = $estado_alumno;
    $alumnos[] = $alumno;
}

usort($alumnos, function ($a, $b) use ($orden_calificaciones) {
    if ($orden_calificaciones === "nombre") {
        return strcmp($a["nombre"] . " " . $a["apellidos"], $b["nombre"] . " " . $b["apellidos"]);
    }

    if ($orden_calificaciones === "pendientes") {
        return (int) $b["pendientes"] <=> (int) $a["pendientes"];
    }

    $nota_a = $a["nota_media"] !== null ? (float) $a["nota_media"] : -1;
    $nota_b = $b["nota_media"] !== null ? (float) $b["nota_media"] : -1;

    return $nota_b <=> $nota_a;
});

$nota_media_grupo = count($notas_grupo) > 0
    ? number_format(array_sum($notas_grupo) / count($notas_grupo), 1)
    : "-";

// Fin consulta de calificaciones del grupo


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

    <title>Calificaciones · <?php echo limpiar_texto_doa($asignatura["nombre"]); ?> | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/calificaciones.css" rel="stylesheet">
    <link href="css/calificaciones_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-calificaciones pagina-calificaciones-profesor">
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
                        <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                            <img alt="" src="img/iconos/grey-chevron-right.svg">
                        </span>

                        <span>Volver a detalles de la asignatura</span>
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

                        <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_tareas); ?>">
                            Tareas
                        </a>

                        <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_examenes); ?>">
                            Exámenes
                        </a>

                        <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="<?php echo limpiar_texto_doa($url_calificaciones); ?>">
                            Calificaciones
                        </a>
                    </nav>
                </div>
            </section>

            <!-- Fin cabecera de asignatura -->


            <!-- Inicio resumen -->

            <h2 class="titulo-seccion-calificaciones">Calificaciones del grupo</h2>

            <section class="resumen-metricas" aria-label="Resumen de calificaciones del grupo">
                <article class="tarjeta-metrica tarjeta-metrica--principal">
                    <span>Nota media del grupo</span>
                    <strong><?php echo limpiar_texto_doa($nota_media_grupo); ?></strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Alumnos aprobados</span>
                    <strong><?php echo $total_aprobados; ?></strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Pendientes de corregir</span>
                    <strong><?php echo $total_pendientes_correccion; ?></strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Alumnos suspendidos</span>
                    <strong><?php echo $total_suspendidos; ?></strong>
                </article>
            </section>

            <!-- Fin resumen -->


            <!-- Inicio tabla de seguimiento -->

            <section class="seccion-calificaciones">
                <div class="cabecera-tabla-calificaciones">
                    <h2>Seguimiento del alumnado</h2>

                    <form class="grupo-filtros" method="get">
                        <input type="hidden" name="id_asignatura" value="<?php echo $id_asignatura; ?>">

                        <label class="filtro-select">
                            <select name="estado">
                                <option value="todos" <?php echo $filtro_estado === "todos" ? "selected" : ""; ?>>
                                    Estado: todos
                                </option>

                                <option value="aprobado" <?php echo $filtro_estado === "aprobado" ? "selected" : ""; ?>>
                                    Aprobados
                                </option>

                                <option value="suspendido" <?php echo $filtro_estado === "suspendido" ? "selected" : ""; ?>>
                                    Suspendidos
                                </option>

                                <option value="pendiente" <?php echo $filtro_estado === "pendiente" ? "selected" : ""; ?>>
                                    Pendientes
                                </option>
                            </select>
                        </label>

                        <label class="filtro-select">
                            <select name="orden">
                                <option value="nota" <?php echo $orden_calificaciones === "nota" ? "selected" : ""; ?>>
                                    Ordenar por nota
                                </option>

                                <option value="nombre" <?php echo $orden_calificaciones === "nombre" ? "selected" : ""; ?>>
                                    Ordenar por nombre
                                </option>

                                <option value="pendientes" <?php echo $orden_calificaciones === "pendientes" ? "selected" : ""; ?>>
                                    Ordenar por pendientes
                                </option>
                            </select>
                        </label>

                        <button class="btn btn-primary boton-filtro-aplicar" type="submit">
                            Aplicar
                        </button>
                    </form>
                </div>

                <div class="tabla-calificaciones tabla-calificaciones--profesor">
                    <table>
                        <thead>
                            <tr>
                                <th>Alumno</th>
                                <th>Tareas</th>
                                <th>Exámenes</th>
                                <th>Nota final</th>
                                <th>Pendientes</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($alumnos) === 0) { ?>
                                <tr class="fila-sin-resultados">
                                    <td colspan="6">
                                        No hay alumnos que coincidan con los filtros seleccionados.
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php foreach ($alumnos as $alumno) { ?>
                                <?php
                                $nombre_alumno = trim($alumno["nombre"] . " " . $alumno["apellidos"]);
                                $estado_alumno = $alumno["estado_alumno"];
                                $texto_estado = obtener_texto_estado_alumno_calificacion($estado_alumno);
                                ?>

                                <tr>
                                    <td>
                                        <div class="alumno-calificacion">
                                            <strong><?php echo limpiar_texto_doa($nombre_alumno); ?></strong>
                                            <small><?php echo limpiar_texto_doa($alumno["email"]); ?></small>
                                        </div>
                                    </td>

                                    <td>
                                        <?php echo limpiar_texto_doa(formatear_media_profesor($alumno["media_tareas"])); ?>
                                    </td>

                                    <td>
                                        <?php echo limpiar_texto_doa(formatear_media_profesor($alumno["media_examenes"])); ?>
                                    </td>

                                    <td>
                                        <?php if ($alumno["nota_media"] === null) { ?>
                                            <span class="barra-nota-pendiente"></span>
                                        <?php } else { ?>
                                            <span class="<?php echo (float) $alumno["nota_media"] >= 5 ? "nota-positiva" : "nota-negativa"; ?>">
                                                <?php echo limpiar_texto_doa(formatear_media_profesor($alumno["nota_media"])); ?>
                                            </span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <?php echo (int) $alumno["pendientes"]; ?>
                                    </td>

                                    <td>
                                        <span class="estado-calificacion estado-calificacion--<?php echo limpiar_texto_doa($estado_alumno); ?>">
                                            <?php echo limpiar_texto_doa($texto_estado); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Fin tabla de seguimiento -->
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>
</html>