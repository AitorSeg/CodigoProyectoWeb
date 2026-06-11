<?php
// Inicio configuración de página

$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar asignatura...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página


// Inicio funciones auxiliares

function formatear_nota_calificacion($nota) {
    if ($nota === null) {
        return "-";
    }

    return number_format((float) $nota, 1);
}

function formatear_media_calificacion($notas) {
    if (count($notas) === 0) {
        return "-";
    }

    return number_format(array_sum($notas) / count($notas), 1);
}

function obtener_texto_tipo_calificacion($tipo_actividad) {
    return match ($tipo_actividad) {
        "tarea" => "Tarea",
        "practica" => "Práctica",
        "examen" => "Examen",
        default => ucfirst($tipo_actividad),
    };
}

function obtener_estado_calificacion($id_calificacion) {
    return $id_calificacion !== null ? "corregida" : "pendiente";
}

function obtener_texto_estado_calificacion($estado) {
    return match ($estado) {
        "corregida" => "Corregida",
        "pendiente" => "Pendiente",
        default => ucfirst($estado),
    };
}

function obtener_fecha_calificacion($actividad) {
    if ($actividad["fecha_calificacion"] !== null) {
        return date("d/m/Y", strtotime($actividad["fecha_calificacion"]));
    }

    if ($actividad["fecha_limite"] !== null) {
        return date("d/m/Y", strtotime($actividad["fecha_limite"]));
    }

    if ($actividad["fecha_inicio"] !== null) {
        return date("d/m/Y", strtotime($actividad["fecha_inicio"]));
    }

    return "-";
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

$id_alumno = (int) $_SESSION["doa_id_usuario"];

$filtro_tipo = $_GET["tipo"] ?? "todas";
$filtro_estado = $_GET["estado"] ?? "todos";
$orden_calificaciones = $_GET["orden"] ?? "fecha";

if (!in_array($filtro_tipo, ["todas", "examen", "tarea", "practica"], true)) {
    $filtro_tipo = "todas";
}

if (!in_array($filtro_estado, ["todos", "corregida", "pendiente"], true)) {
    $filtro_estado = "todos";
}

if (!in_array($orden_calificaciones, ["fecha", "nota", "nombre"], true)) {
    $orden_calificaciones = "fecha";
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

// Fin selección de asignatura


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


// Inicio consulta de calificaciones

$consulta_actividades = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.tipo_actividad,
        ae.unidad,
        ae.titulo,
        ae.fecha_inicio,
        ae.fecha_limite,
        c.id_calificacion,
        c.nota,
        c.comentario_profesor,
        c.fecha_calificacion
    FROM actividades_evaluables ae
    LEFT JOIN calificaciones c
        ON c.id_actividad = ae.id_actividad
        AND c.id_alumno = :id_alumno
    WHERE ae.id_asignatura = :id_asignatura
    AND ae.tipo_actividad IN ('tarea', 'practica', 'examen')
    AND ae.visible = 1
    AND ae.estado = 'publicada'
");

$consulta_actividades->execute([
    "id_alumno" => $id_alumno,
    "id_asignatura" => $id_asignatura
]);

$actividades_originales = $consulta_actividades->fetchAll();

$actividades = [];
$notas_totales = [];
$notas_examenes = [];
$notas_tareas = [];
$notas_practicas = [];

foreach ($actividades_originales as $actividad) {
    $estado_calificacion = obtener_estado_calificacion($actividad["id_calificacion"]);

    if ($actividad["nota"] !== null) {
        $nota = (float) $actividad["nota"];
        $notas_totales[] = $nota;

        if ($actividad["tipo_actividad"] === "examen") {
            $notas_examenes[] = $nota;
        }

        if ($actividad["tipo_actividad"] === "tarea") {
            $notas_tareas[] = $nota;
        }

        if ($actividad["tipo_actividad"] === "practica") {
            $notas_practicas[] = $nota;
        }
    }

    if ($filtro_tipo !== "todas" && $actividad["tipo_actividad"] !== $filtro_tipo) {
        continue;
    }

    if ($filtro_estado !== "todos" && $estado_calificacion !== $filtro_estado) {
        continue;
    }

    $actividad["estado_calificacion"] = $estado_calificacion;
    $actividades[] = $actividad;
}

usort($actividades, function ($a, $b) use ($orden_calificaciones) {
    if ($orden_calificaciones === "nombre") {
        return strcmp($a["titulo"], $b["titulo"]);
    }

    if ($orden_calificaciones === "nota") {
        $nota_a = $a["nota"] !== null ? (float) $a["nota"] : -1;
        $nota_b = $b["nota"] !== null ? (float) $b["nota"] : -1;

        return $nota_b <=> $nota_a;
    }

    $fecha_a = $a["fecha_calificacion"] ?? $a["fecha_limite"] ?? $a["fecha_inicio"] ?? "";
    $fecha_b = $b["fecha_calificacion"] ?? $b["fecha_limite"] ?? $b["fecha_inicio"] ?? "";

    return strcmp($fecha_b, $fecha_a);
});

$nota_media = formatear_media_calificacion($notas_totales);
$nota_media_examenes = formatear_media_calificacion($notas_examenes);
$nota_media_tareas = formatear_media_calificacion($notas_tareas);
$nota_media_practicas = formatear_media_calificacion($notas_practicas);

// Fin consulta de calificaciones


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

    <title>Calificaciones · <?php echo limpiar_texto_doa($asignatura["nombre"]); ?> | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/calificaciones.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-calificaciones">
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
                            <img alt="" src="img/iconos/grey-user.svg">
                            <span><?php echo limpiar_texto_doa($profesores); ?></span>
                        </li>

                        <li>
                            <img alt="" src="img/iconos/grey-notebook.svg">
                            <span>
                                <?php echo limpiar_texto_doa($asignatura["codigo"]); ?>
                                · <?php echo limpiar_texto_doa($asignatura["curso"]); ?>
                                · Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                            </span>
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

            <h2 class="titulo-seccion-calificaciones">Calificaciones de la asignatura</h2>

            <section aria-label="Resumen de calificaciones" class="resumen-metricas">
                <article class="tarjeta-metrica tarjeta-metrica--principal">
                    <span>Nota media</span>
                    <strong><?php echo limpiar_texto_doa($nota_media); ?></strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Nota media exámenes</span>
                    <strong><?php echo limpiar_texto_doa($nota_media_examenes); ?></strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Nota media tareas</span>
                    <strong><?php echo limpiar_texto_doa($nota_media_tareas); ?></strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Nota media prácticas</span>
                    <strong><?php echo limpiar_texto_doa($nota_media_practicas); ?></strong>
                </article>
            </section>

            <!-- Fin resumen -->


            <!-- Inicio tabla de calificaciones -->

            <section class="seccion-calificaciones">
                <div class="cabecera-tabla-calificaciones">
                    <h2>Calificaciones</h2>

                    <form class="grupo-filtros" method="get">
                        <input type="hidden" name="id_asignatura" value="<?php echo $id_asignatura; ?>">

                        <label class="filtro-select">
                            <select name="tipo">
                                <option value="todas" <?php echo $filtro_tipo === "todas" ? "selected" : ""; ?>>
                                    Tipo: todas
                                </option>

                                <option value="examen" <?php echo $filtro_tipo === "examen" ? "selected" : ""; ?>>
                                    Exámenes
                                </option>

                                <option value="tarea" <?php echo $filtro_tipo === "tarea" ? "selected" : ""; ?>>
                                    Tareas
                                </option>

                                <option value="practica" <?php echo $filtro_tipo === "practica" ? "selected" : ""; ?>>
                                    Prácticas
                                </option>
                            </select>
                        </label>

                        <label class="filtro-select">
                            <select name="estado">
                                <option value="todos" <?php echo $filtro_estado === "todos" ? "selected" : ""; ?>>
                                    Estado: todos
                                </option>

                                <option value="corregida" <?php echo $filtro_estado === "corregida" ? "selected" : ""; ?>>
                                    Corregidas
                                </option>

                                <option value="pendiente" <?php echo $filtro_estado === "pendiente" ? "selected" : ""; ?>>
                                    Pendientes
                                </option>
                            </select>
                        </label>

                        <label class="filtro-select">
                            <select name="orden">
                                <option value="fecha" <?php echo $orden_calificaciones === "fecha" ? "selected" : ""; ?>>
                                    Ordenar por fecha
                                </option>

                                <option value="nota" <?php echo $orden_calificaciones === "nota" ? "selected" : ""; ?>>
                                    Ordenar por nota
                                </option>

                                <option value="nombre" <?php echo $orden_calificaciones === "nombre" ? "selected" : ""; ?>>
                                    Ordenar por nombre
                                </option>
                            </select>
                        </label>

                        <button class="btn btn-primary boton-filtro-aplicar" type="submit">
                            Aplicar
                        </button>
                    </form>
                </div>

                <div class="tabla-calificaciones">
                    <table>
                        <thead>
                            <tr>
                                <th>Actividad</th>
                                <th>Tipo</th>
                                <th>Unidad</th>
                                <th>Nota</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($actividades) === 0) { ?>
                                <tr class="fila-sin-resultados">
                                    <td colspan="7">
                                        No hay calificaciones que coincidan con los filtros seleccionados.
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php foreach ($actividades as $actividad) { ?>
                                <?php
                                $estado_calificacion = $actividad["estado_calificacion"];
                                $texto_estado = obtener_texto_estado_calificacion($estado_calificacion);
                                $nota = formatear_nota_calificacion($actividad["nota"]);
                                $clase_nota = $actividad["nota"] !== null && (float) $actividad["nota"] < 5 ? "nota-negativa" : "nota-positiva";
                                $fecha = obtener_fecha_calificacion($actividad);

                                if ($actividad["tipo_actividad"] === "examen") {
                                    $url_actividad = "detalle_examen.php?id_actividad=" . (int) $actividad["id_actividad"];
                                } else {
                                    $url_actividad = "detalle_tarea.php?id_actividad=" . (int) $actividad["id_actividad"];
                                }
                                ?>

                                <tr>
                                    <td><?php echo limpiar_texto_doa($actividad["titulo"]); ?></td>

                                    <td>
                                        <?php echo limpiar_texto_doa(obtener_texto_tipo_calificacion($actividad["tipo_actividad"])); ?>
                                    </td>

                                    <td>
                                        <?php echo limpiar_texto_doa($actividad["unidad"] ?? "-"); ?>
                                    </td>

                                    <td>
                                        <?php if ($actividad["nota"] === null) { ?>
                                            <span class="barra-nota-pendiente"></span>
                                        <?php } else { ?>
                                            <span class="<?php echo limpiar_texto_doa($clase_nota); ?>">
                                                <?php echo limpiar_texto_doa($nota); ?>
                                            </span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <span class="estado-calificacion estado-calificacion--<?php echo limpiar_texto_doa($estado_calificacion); ?>">
                                            <?php echo limpiar_texto_doa($texto_estado); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php echo limpiar_texto_doa($fecha); ?>
                                    </td>

                                    <td>
                                        <a class="enlace-tabla" href="<?php echo limpiar_texto_doa($url_actividad); ?>">
                                            Ver detalles
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Fin tabla de calificaciones -->
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>
</html>