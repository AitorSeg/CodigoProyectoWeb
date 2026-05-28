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

function obtener_estado_tarea_alumno($tarea)
{
    if ($tarea["id_calificacion"] !== null) {
        return "calificada";
    }

    if ($tarea["id_entrega"] === null) {
        return "pendiente";
    }

    return $tarea["estado_entrega"];
}

function obtener_texto_estado_tarea($estado)
{
    return match ($estado) {
        "pendiente" => "Pendiente",
        "entregada" => "Entregada",
        "tardia" => "Tardía",
        "revisada" => "Revisada",
        "calificada" => "Calificada",
        default => ucfirst($estado),
    };
}

function calcular_tiempo_restante($fecha_limite)
{
    if ($fecha_limite === null) {
        return "Sin fecha límite";
    }

    $fecha_actual = new DateTime();
    $fecha_entrega = new DateTime($fecha_limite);

    if ($fecha_entrega < $fecha_actual) {
        return "Plazo finalizado";
    }

    $diferencia = $fecha_actual->diff($fecha_entrega);

    if ($diferencia->days === 0) {
        return "Entrega hoy";
    }

    if ($diferencia->days === 1) {
        return "Queda 1 día";
    }

    return "Quedan " . $diferencia->days . " días";
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

$id_alumno = (int) $_SESSION["doa_id_usuario"];

$filtro_tipo = $_GET["tipo"] ?? "todas";
$filtro_estado = $_GET["estado"] ?? "todos";
$orden_tareas = $_GET["orden"] ?? "fecha_entrega";

if (!in_array($filtro_tipo, ["todas", "tarea", "practica"], true)) {
    $filtro_tipo = "todas";
}

if (!in_array($filtro_estado, ["todos", "pendiente", "entregada", "tardia", "calificada"], true)) {
    $filtro_estado = "todos";
}

if (!in_array($orden_tareas, ["fecha_entrega", "nombre", "estado"], true)) {
    $orden_tareas = "fecha_entrega";
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


// Inicio consulta de tareas

$consulta_tareas = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.tipo_actividad,
        ae.unidad,
        ae.titulo,
        ae.descripcion,
        ae.fecha_inicio,
        ae.fecha_limite,
        e.id_entrega,
        e.estado AS estado_entrega,
        c.id_calificacion,
        c.nota
    FROM actividades_evaluables ae
    LEFT JOIN entregas e
        ON e.id_actividad = ae.id_actividad
        AND e.id_alumno = :id_alumno_entrega
    LEFT JOIN calificaciones c
        ON c.id_actividad = ae.id_actividad
        AND c.id_alumno = :id_alumno_calificacion
    WHERE ae.id_asignatura = :id_asignatura
    AND ae.tipo_actividad IN ('tarea', 'practica')
    AND ae.visible = 1
    AND ae.estado = 'publicada'
");

$consulta_tareas->execute([
    "id_alumno_entrega" => $id_alumno,
    "id_alumno_calificacion" => $id_alumno,
    "id_asignatura" => $id_asignatura
]);

$tareas_originales = $consulta_tareas->fetchAll();

$tareas = [];

foreach ($tareas_originales as $tarea) {
    $estado_alumno = obtener_estado_tarea_alumno($tarea);

    if ($filtro_tipo !== "todas" && $tarea["tipo_actividad"] !== $filtro_tipo) {
        continue;
    }

    if ($filtro_estado !== "todos" && $estado_alumno !== $filtro_estado) {
        continue;
    }

    $tarea["estado_alumno"] = $estado_alumno;
    $tareas[] = $tarea;
}

usort($tareas, function ($a, $b) use ($orden_tareas) {
    if ($orden_tareas === "nombre") {
        return strcmp($a["titulo"], $b["titulo"]);
    }

    if ($orden_tareas === "estado") {
        return strcmp($a["estado_alumno"], $b["estado_alumno"]);
    }

    return strcmp((string) $a["fecha_limite"], (string) $b["fecha_limite"]);
});

$proxima_tarea = null;

foreach ($tareas_originales as $tarea) {
    $estado_alumno = obtener_estado_tarea_alumno($tarea);

    if ($estado_alumno === "pendiente") {
        $proxima_tarea = $tarea;
        break;
    }
}

// Fin consulta de tareas


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

    <title>Tareas · <?php echo limpiar_texto_doa($asignatura["nombre"]); ?> | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/listado_tareas.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-listado-tareas">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido principal -->

        <main class="contenido-doa contenido-detalle-asignatura contenido-listado-tareas">
            <section class="detalle-asignatura-principal">
                <!-- Inicio cabecera de asignatura -->

                <div class="cabecera-detalle-asignatura">
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


                <!-- Inicio próxima entrega -->

                <section class="proxima-entrega">
                    <div class="proxima-entrega__contenido">
                        <?php if (!$proxima_tarea) { ?>
                            <span class="etiqueta-estado etiqueta-estado--entregada">
                                Sin pendientes
                            </span>

                            <h2>Próxima entrega</h2>

                            <p class="proxima-entrega__titulo">
                                No hay tareas pendientes
                            </p>

                            <p class="proxima-entrega__descripcion">
                                Cuando el profesor publique una tarea, aparecerá aquí.
                            </p>
                        <?php } else { ?>
                            <span class="etiqueta-estado etiqueta-estado--pendiente">
                                Pendiente
                            </span>

                            <h2>Próxima entrega</h2>

                            <p class="proxima-entrega__titulo">
                                <?php echo limpiar_texto_doa($proxima_tarea["titulo"]); ?>
                            </p>

                            <p class="proxima-entrega__descripcion">
                                <?php echo limpiar_texto_doa($proxima_tarea["descripcion"]); ?>
                            </p>
                        <?php } ?>
                    </div>

                    <div class="proxima-entrega__accion">
                        <?php if (!$proxima_tarea) { ?>
                            <p class="proxima-entrega__tiempo">
                                Sin entregas
                            </p>

                            <a class="boton-ver-detalles" href="<?php echo limpiar_texto_doa($url_tareas); ?>">
                                Ver tareas
                            </a>
                        <?php } else { ?>
                            <p class="proxima-entrega__tiempo">
                                <?php echo limpiar_texto_doa(calcular_tiempo_restante($proxima_tarea["fecha_limite"])); ?>
                            </p>

                            <a class="boton-ver-detalles" href="detalle_tarea.php?id_actividad=<?php echo (int) $proxima_tarea["id_actividad"]; ?>">
                                Ver detalles
                            </a>
                        <?php } ?>
                    </div>
                </section>

                <!-- Fin próxima entrega -->


                <!-- Inicio listado de tareas -->

                <section class="bloque-listado-tareas">
                    <div class="cabecera-listado-tareas">
                        <h2>Tareas</h2>

                        <form class="grupo-filtros" method="get">
                            <input type="hidden" name="id_asignatura" value="<?php echo $id_asignatura; ?>">

                            <label class="filtro-select">
                                <select name="tipo">
                                    <option value="todas" <?php echo $filtro_tipo === "todas" ? "selected" : ""; ?>>
                                        Tipo: todas
                                    </option>

                                    <option value="tarea" <?php echo $filtro_tipo === "tarea" ? "selected" : ""; ?>>
                                        Tipo: tareas
                                    </option>

                                    <option value="practica" <?php echo $filtro_tipo === "practica" ? "selected" : ""; ?>>
                                        Tipo: prácticas
                                    </option>
                                </select>
                            </label>

                            <label class="filtro-select">
                                <select name="estado">
                                    <option value="todos" <?php echo $filtro_estado === "todos" ? "selected" : ""; ?>>
                                        Estado: todos
                                    </option>

                                    <option value="pendiente" <?php echo $filtro_estado === "pendiente" ? "selected" : ""; ?>>
                                        Estado: pendiente
                                    </option>

                                    <option value="entregada" <?php echo $filtro_estado === "entregada" ? "selected" : ""; ?>>
                                        Estado: entregada
                                    </option>

                                    <option value="tardia" <?php echo $filtro_estado === "tardia" ? "selected" : ""; ?>>
                                        Estado: tardía
                                    </option>

                                    <option value="calificada" <?php echo $filtro_estado === "calificada" ? "selected" : ""; ?>>
                                        Estado: calificada
                                    </option>
                                </select>
                            </label>

                            <label class="filtro-select">
                                <select name="orden">
                                    <option value="fecha_entrega" <?php echo $orden_tareas === "fecha_entrega" ? "selected" : ""; ?>>
                                        Ordenar por fecha
                                    </option>

                                    <option value="nombre" <?php echo $orden_tareas === "nombre" ? "selected" : ""; ?>>
                                        Ordenar por nombre
                                    </option>

                                    <option value="estado" <?php echo $orden_tareas === "estado" ? "selected" : ""; ?>>
                                        Ordenar por estado
                                    </option>
                                </select>
                            </label>

                            <button class="btn btn-primary boton-filtro-aplicar" type="submit">
                                Aplicar
                            </button>
                        </form>
                    </div>

                    <div class="tabla-tareas">
                        <div class="tabla-tareas__cabecera">
                            <p>Tarea</p>
                            <p>Fecha de emisión</p>
                            <p>Fecha de entrega</p>
                            <p>Estado</p>
                            <p>Calificación</p>
                        </div>

                        <div>
                            <?php if (count($tareas) === 0) { ?>
                                <p class="mensaje-tabla-vacia">
                                    No hay tareas que coincidan con los filtros seleccionados.
                                </p>
                            <?php } ?>

                            <?php foreach ($tareas as $tarea) { ?>
                                <?php
                                $estado_alumno = $tarea["estado_alumno"];
                                $texto_estado = obtener_texto_estado_tarea($estado_alumno);

                                $fecha_inicio = $tarea["fecha_inicio"] !== null
                                    ? date("d/m/Y", strtotime($tarea["fecha_inicio"]))
                                    : "-";

                                $fecha_limite = $tarea["fecha_limite"] !== null
                                    ? date("d/m/Y", strtotime($tarea["fecha_limite"]))
                                    : "-";

                                $nota = $tarea["nota"] !== null
                                    ? number_format((float) $tarea["nota"], 1) . "/10"
                                    : "-";

                                $url_detalle_tarea = "detalle_tarea.php?id_actividad=" . (int) $tarea["id_actividad"];
                                ?>

                                <article class="fila-tarea">
                                    <a class="fila-tarea__nombre" href="<?php echo limpiar_texto_doa($url_detalle_tarea); ?>">
                                        <?php echo limpiar_texto_doa($tarea["titulo"]); ?>
                                    </a>

                                    <p>
                                        <strong><?php echo limpiar_texto_doa($fecha_inicio); ?></strong>
                                    </p>

                                    <p>
                                        <strong><?php echo limpiar_texto_doa($fecha_limite); ?></strong>
                                    </p>

                                    <p>
                                        <span class="etiqueta-estado etiqueta-estado--<?php echo limpiar_texto_doa($estado_alumno); ?>">
                                            <?php echo limpiar_texto_doa($texto_estado); ?>
                                        </span>
                                    </p>

                                    <p class="fila-tarea__nota">
                                        <?php echo limpiar_texto_doa($nota); ?>
                                    </p>
                                </article>
                            <?php } ?>
                        </div>
                    </div>
                </section>

                <!-- Fin listado de tareas -->
            </section>
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>

</html>