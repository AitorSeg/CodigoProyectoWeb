<?php
// Inicio configuración de página

$rol_pagina = "profesor";
$pagina_activa = "panel";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar asignatura, tarea...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

$id_profesor = (int) $_SESSION["doa_id_usuario"];
$primer_nombre_profesor = preg_split("/\s+/", trim($_SESSION["doa_nombre"]))[0];

// Fin configuración de página


// Inicio funciones auxiliares

function formatear_fecha_panel_profesor($fecha)
{
    if ($fecha === null) {
        return "Sin fecha";
    }

    return date("d/m", strtotime($fecha));
}

function obtener_texto_vencimiento_panel_profesor($fecha)
{
    if ($fecha === null) {
        return "Sin fecha límite";
    }

    $fecha_limite = new DateTime($fecha);
    $hoy = new DateTime("today");

    if ($fecha_limite < $hoy) {
        return "Vencida el " . date("d/m", strtotime($fecha));
    }

    if ($fecha_limite->format("Y-m-d") === $hoy->format("Y-m-d")) {
        return "Vence hoy";
    }

    return "Vence el " . date("d/m", strtotime($fecha));
}

function obtener_texto_examen_panel_profesor($fecha_inicio, $fecha_limite)
{
    $ahora = new DateTime();

    if ($fecha_inicio !== null && new DateTime($fecha_inicio) > $ahora) {
        return "Abre el " . date("d/m", strtotime($fecha_inicio));
    }

    if ($fecha_limite !== null) {
        return "Cierra el " . date("d/m", strtotime($fecha_limite));
    }

    return "Publicado";
}

// Fin funciones auxiliares


// Inicio resumen general

$consulta_total_asignaturas = $pdo->prepare("
    SELECT COUNT(DISTINCT a.id_asignatura) AS total
    FROM asignaturas a
    INNER JOIN usuarios_asignaturas ua
        ON ua.id_asignatura = a.id_asignatura
        AND ua.id_usuario = :id_profesor
        AND ua.rol_asignatura = 'profesor'
        AND ua.estado = 'activa'
    WHERE a.estado = 'activa'
");

$consulta_total_asignaturas->execute([
    "id_profesor" => $id_profesor
]);

$total_asignaturas = (int) $consulta_total_asignaturas->fetch()["total"];


$consulta_tareas_activas = $pdo->prepare("
    SELECT COUNT(DISTINCT ae.id_actividad) AS total
    FROM actividades_evaluables ae
    INNER JOIN usuarios_asignaturas ua
        ON ua.id_asignatura = ae.id_asignatura
        AND ua.id_usuario = :id_profesor
        AND ua.rol_asignatura = 'profesor'
        AND ua.estado = 'activa'
    WHERE ae.tipo_actividad IN ('tarea', 'practica')
    AND ae.visible = 1
    AND ae.estado = 'publicada'
    AND (
        ae.fecha_limite IS NULL
        OR ae.fecha_limite >= NOW()
    )
");

$consulta_tareas_activas->execute([
    "id_profesor" => $id_profesor
]);

$total_tareas_activas = (int) $consulta_tareas_activas->fetch()["total"];


$consulta_entregas_pendientes = $pdo->prepare("
    SELECT COUNT(DISTINCT e.id_entrega) AS total
    FROM entregas e
    INNER JOIN actividades_evaluables ae
        ON ae.id_actividad = e.id_actividad
    INNER JOIN usuarios_asignaturas ua
        ON ua.id_asignatura = ae.id_asignatura
        AND ua.id_usuario = :id_profesor
        AND ua.rol_asignatura = 'profesor'
        AND ua.estado = 'activa'
    WHERE ae.tipo_actividad IN ('tarea', 'practica')
    AND e.estado IN ('entregada', 'tardia')
");

$consulta_entregas_pendientes->execute([
    "id_profesor" => $id_profesor
]);

$total_entregas_pendientes = (int) $consulta_entregas_pendientes->fetch()["total"];


$consulta_recursos_publicados = $pdo->prepare("
    SELECT COUNT(DISTINCT r.id_recurso) AS total
    FROM recursos r
    INNER JOIN usuarios_asignaturas ua
        ON ua.id_asignatura = r.id_asignatura
        AND ua.id_usuario = :id_profesor
        AND ua.rol_asignatura = 'profesor'
        AND ua.estado = 'activa'
    WHERE r.visible = 1
");

$consulta_recursos_publicados->execute([
    "id_profesor" => $id_profesor
]);

$total_recursos_publicados = (int) $consulta_recursos_publicados->fetch()["total"];

// Fin resumen general


// Inicio consulta de asignaturas destacadas

$consulta_asignaturas = $pdo->prepare("
    SELECT
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.curso,
        a.grupo,
        COUNT(DISTINCT ua_alumno.id_usuario) AS total_alumnos,
        COUNT(DISTINCT ae.id_actividad) AS total_tareas,
        COUNT(DISTINCT e.id_entrega) AS entregas_pendientes
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
    LEFT JOIN actividades_evaluables ae
        ON ae.id_asignatura = a.id_asignatura
        AND ae.tipo_actividad IN ('tarea', 'practica')
        AND ae.visible = 1
        AND ae.estado = 'publicada'
    LEFT JOIN entregas e
        ON e.id_actividad = ae.id_actividad
        AND e.estado IN ('entregada', 'tardia')
    WHERE a.estado = 'activa'
    GROUP BY
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.curso,
        a.grupo
    ORDER BY entregas_pendientes DESC, a.nombre ASC
    LIMIT 2
");

$consulta_asignaturas->execute([
    "id_profesor" => $id_profesor
]);

$asignaturas_destacadas = $consulta_asignaturas->fetchAll();

// Fin consulta de asignaturas destacadas


// Inicio consulta de tareas activas

$consulta_tareas_laterales = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.id_asignatura,
        ae.titulo,
        ae.fecha_limite,
        a.nombre AS asignatura_nombre,
        COUNT(DISTINCT e.id_entrega) AS entregas_pendientes
    FROM actividades_evaluables ae
    INNER JOIN asignaturas a
        ON a.id_asignatura = ae.id_asignatura
    INNER JOIN usuarios_asignaturas ua
        ON ua.id_asignatura = ae.id_asignatura
        AND ua.id_usuario = :id_profesor
        AND ua.rol_asignatura = 'profesor'
        AND ua.estado = 'activa'
    LEFT JOIN entregas e
        ON e.id_actividad = ae.id_actividad
        AND e.estado IN ('entregada', 'tardia')
    WHERE ae.tipo_actividad IN ('tarea', 'practica')
    AND ae.visible = 1
    AND ae.estado = 'publicada'
    AND (
        ae.fecha_limite IS NULL
        OR ae.fecha_limite >= NOW()
    )
    GROUP BY
        ae.id_actividad,
        ae.id_asignatura,
        ae.titulo,
        ae.fecha_limite,
        a.nombre
    ORDER BY
        ae.fecha_limite ASC,
        entregas_pendientes DESC
    LIMIT 3
");

$consulta_tareas_laterales->execute([
    "id_profesor" => $id_profesor
]);

$tareas_laterales = $consulta_tareas_laterales->fetchAll();

// Fin consulta de tareas activas


// Inicio consulta de próximos exámenes

$consulta_examenes_laterales = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.id_asignatura,
        ae.titulo,
        ae.fecha_inicio,
        ae.fecha_limite,
        a.nombre AS asignatura_nombre
    FROM actividades_evaluables ae
    INNER JOIN asignaturas a
        ON a.id_asignatura = ae.id_asignatura
    INNER JOIN usuarios_asignaturas ua
        ON ua.id_asignatura = ae.id_asignatura
        AND ua.id_usuario = :id_profesor
        AND ua.rol_asignatura = 'profesor'
        AND ua.estado = 'activa'
    WHERE ae.tipo_actividad = 'examen'
    AND ae.visible = 1
    AND ae.estado = 'publicada'
    AND (
        ae.fecha_limite IS NULL
        OR ae.fecha_limite >= NOW()
    )
    ORDER BY
        CASE
            WHEN ae.fecha_inicio <= NOW() THEN 0
            ELSE 1
        END,
        ae.fecha_inicio ASC
    LIMIT 3
");

$consulta_examenes_laterales->execute([
    "id_profesor" => $id_profesor
]);

$examenes_laterales = $consulta_examenes_laterales->fetchAll();

// Fin consulta de próximos exámenes
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Panel del profesor | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/panel_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-panel-profesor">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido principal -->

        <main class="contenido-doa contenido-panel-profesor">
            <section class="cabecera-panel-profesor">
                <div class="cabecera-panel-profesor__texto">
                    <p class="cabecera-panel-profesor__eyebrow">Panel del profesor</p>

                    <h1>Hola, <?php echo limpiar_texto_doa($primer_nombre_profesor); ?></h1>

                    <p>
                        Gestiona tus asignaturas, revisa entregas pendientes y consulta el estado
                        actual de tus grupos.
                    </p>
                </div>
            </section>

            <section class="resumen-docente resumen-docente--sin-margen" aria-label="Resumen general del profesor">
                <article class="tarjeta-resumen-docente tarjeta-resumen-docente--principal">
                    <span>Asignaturas</span>
                    <strong><?php echo $total_asignaturas; ?></strong>
                </article>

                <article class="tarjeta-resumen-docente">
                    <span>Tareas activas</span>
                    <strong><?php echo $total_tareas_activas; ?></strong>
                </article>

                <article class="tarjeta-resumen-docente">
                    <span>Entregas pendientes</span>
                    <strong><?php echo $total_entregas_pendientes; ?></strong>
                </article>

                <article class="tarjeta-resumen-docente">
                    <span>Recursos publicados</span>
                    <strong><?php echo $total_recursos_publicados; ?></strong>
                </article>
                </article>
            </section>

            <div class="panel-profesor-grid">
                <section class="panel-profesor-principal">
                    <article class="bloque-panel-profesor">
                        <div class="bloque-panel-profesor__cabecera">
                            <div>
                                <h2>Asignaturas destacadas</h2>

                                <p>
                                    Vista rápida de las asignaturas con más entregas pendientes.
                                </p>
                            </div>

                            <a class="bloque-panel-profesor__enlace" href="asignaturas_profesor.php">
                                Ver mis asignaturas
                            </a>
                        </div>

                        <div class="grid-asignaturas-profesor">
                            <?php if (count($asignaturas_destacadas) === 0) { ?>
                                <article class="tarjeta-asignatura-profesor">
                                    <div class="tarjeta-asignatura-profesor__cabecera">
                                        <div>
                                            <h3>Sin asignaturas asignadas</h3>

                                            <p>
                                                Secretaría debe asignarte a una asignatura para que aparezca aquí.
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            <?php } ?>

                            <?php foreach ($asignaturas_destacadas as $indice => $asignatura) { ?>
                                <?php
                                $clase_tarjeta = $indice === 0
                                    ? "tarjeta-asignatura-profesor tarjeta-asignatura-profesor--activa"
                                    : "tarjeta-asignatura-profesor";

                                $url_asignatura = "detalle_asignatura_profesor.php?id_asignatura=" . (int) $asignatura["id_asignatura"];
                                ?>

                                <article class="<?php echo limpiar_texto_doa($clase_tarjeta); ?>">
                                    <div class="tarjeta-asignatura-profesor__cabecera">
                                        <div>
                                            <h3><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></h3>

                                            <p>
                                                <?php echo limpiar_texto_doa($asignatura["codigo"]); ?>
                                                · <?php echo limpiar_texto_doa($asignatura["curso"]); ?>
                                                · Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                                            </p>
                                        </div>

                                        <span class="estado-asignatura-docente">Activa</span>
                                    </div>

                                    <div class="tarjeta-asignatura-profesor__stats">
                                        <div class="mini-stat-profesor">
                                            <span>Alumnos</span>
                                            <strong><?php echo (int) $asignatura["total_alumnos"]; ?></strong>
                                        </div>

                                        <div class="mini-stat-profesor">
                                            <span>Tareas</span>
                                            <strong><?php echo (int) $asignatura["total_tareas"]; ?></strong>
                                        </div>

                                        <div class="mini-stat-profesor">
                                            <span>Entregas</span>
                                            <strong><?php echo (int) $asignatura["entregas_pendientes"]; ?></strong>
                                        </div>
                                    </div>

                                    <div class="tarjeta-asignatura-profesor__acciones">
                                        <a class="boton-docente boton-docente--principal" href="<?php echo limpiar_texto_doa($url_asignatura); ?>">
                                            Entrar
                                        </a>
                                    </div>
                                </article>
                            <?php } ?>
                        </div>
                    </article>
                </section>

                <aside class="panel-profesor-lateral">
                    <article class="tarjeta-panel-lateral">
                        <div class="tarjeta-panel-lateral__cabecera">
                            <h3>Tareas activas</h3>
                        </div>

                        <div class="lista-panel-lateral">
                            <?php if (count($tareas_laterales) === 0) { ?>
                                <div class="item-panel-lateral">
                                    <div class="item-panel-lateral__texto">
                                        <strong>No hay tareas activas</strong>

                                        <span>
                                            Las tareas publicadas aparecerán aquí.
                                        </span>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php foreach ($tareas_laterales as $tarea) { ?>
                                <a class="item-panel-lateral" href="detalle_tarea_profesor.php?id_actividad=<?php echo (int) $tarea["id_actividad"]; ?>">
                                    <div class="item-panel-lateral__texto">
                                        <strong><?php echo limpiar_texto_doa($tarea["titulo"]); ?></strong>

                                        <span>
                                            <?php echo limpiar_texto_doa($tarea["asignatura_nombre"]); ?>
                                            · <?php echo (int) $tarea["entregas_pendientes"]; ?> pendientes de revisar
                                        </span>
                                    </div>

                                    <small>
                                        <?php echo limpiar_texto_doa(obtener_texto_vencimiento_panel_profesor($tarea["fecha_limite"])); ?>
                                    </small>
                                </a>
                            <?php } ?>
                        </div>
                    </article>

                    <article class="tarjeta-panel-lateral">
                        <div class="tarjeta-panel-lateral__cabecera">
                            <h3>Próximos exámenes</h3>
                        </div>

                        <div class="lista-panel-lateral">
                            <?php if (count($examenes_laterales) === 0) { ?>
                                <div class="item-panel-lateral">
                                    <div class="item-panel-lateral__texto">
                                        <strong>No hay exámenes próximos</strong>

                                        <span>
                                            Los exámenes publicados aparecerán aquí.
                                        </span>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php foreach ($examenes_laterales as $examen) { ?>
                                <a class="item-panel-lateral" href="detalle_examen_profesor.php?id_actividad=<?php echo (int) $examen["id_actividad"]; ?>">
                                    <div class="item-panel-lateral__texto">
                                        <strong><?php echo limpiar_texto_doa($examen["titulo"]); ?></strong>

                                        <span>
                                            <?php echo limpiar_texto_doa($examen["asignatura_nombre"]); ?>
                                        </span>
                                    </div>

                                    <small>
                                        <?php echo limpiar_texto_doa(obtener_texto_examen_panel_profesor($examen["fecha_inicio"], $examen["fecha_limite"])); ?>
                                    </small>
                                </a>
                            <?php } ?>
                        </div>
                    </article>
                </aside>
            </div>
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>

</html>