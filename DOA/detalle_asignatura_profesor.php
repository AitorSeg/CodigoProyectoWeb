<?php
// Inicio configuración de página

$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar recurso, tarea...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página


// Inicio funciones auxiliares

function formatear_fecha_detalle_profesor($fecha)
{
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y", strtotime($fecha));
}

function formatear_hora_detalle_profesor($fecha)
{
    if ($fecha === null) {
        return "-";
    }

    return date("H:i", strtotime($fecha));
}

function obtener_texto_vencimiento_detalle_profesor($fecha)
{
    if ($fecha === null) {
        return "Sin fecha límite";
    }

    $fecha_limite = new DateTime($fecha);
    $hoy = new DateTime("today");

    if ($fecha_limite < $hoy) {
        return "Vencida el " . date("d/m/Y", strtotime($fecha));
    }

    if ($fecha_limite->format("Y-m-d") === $hoy->format("Y-m-d")) {
        return "Vence hoy";
    }

    return "Vence el " . date("d/m/Y", strtotime($fecha));
}

function obtener_texto_examen_detalle_profesor($fecha_inicio, $fecha_limite)
{
    $ahora = new DateTime();

    if ($fecha_inicio !== null && new DateTime($fecha_inicio) > $ahora) {
        return "Abre el " . date("d/m/Y", strtotime($fecha_inicio));
    }

    if ($fecha_limite !== null) {
        return "Cierra el " . date("d/m/Y", strtotime($fecha_limite));
    }

    return "Publicado";
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

if (!isset($_GET["id_asignatura"])) {
    header("Location: asignaturas_profesor.php");
    exit;
}

$id_profesor = (int) $_SESSION["doa_id_usuario"];
$id_asignatura = (int) $_GET["id_asignatura"];

// Fin parámetros de pantalla


// Inicio consulta de asignatura

$consulta_asignatura = $pdo->prepare("
    SELECT
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.descripcion,
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
        a.descripcion,
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


// Inicio resumen docente

$consulta_tareas_activas = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM actividades_evaluables
    WHERE id_asignatura = :id_asignatura
    AND id_profesor = :id_profesor
    AND tipo_actividad IN ('tarea', 'practica')
    AND visible = 1
    AND estado = 'publicada'
    AND (
        fecha_limite IS NULL
        OR fecha_limite >= NOW()
    )
");

$consulta_tareas_activas->execute([
    "id_asignatura" => $id_asignatura,
    "id_profesor" => $id_profesor
]);

$total_tareas_activas = (int) $consulta_tareas_activas->fetch()["total"];


$consulta_entregas_pendientes = $pdo->prepare("
    SELECT COUNT(DISTINCT e.id_entrega) AS total
    FROM entregas e
    INNER JOIN actividades_evaluables ae
        ON ae.id_actividad = e.id_actividad
    WHERE ae.id_asignatura = :id_asignatura
    AND ae.id_profesor = :id_profesor
    AND ae.tipo_actividad IN ('tarea', 'practica')
    AND e.estado IN ('entregada', 'tardia')
");

$consulta_entregas_pendientes->execute([
    "id_asignatura" => $id_asignatura,
    "id_profesor" => $id_profesor
]);

$total_entregas_pendientes = (int) $consulta_entregas_pendientes->fetch()["total"];


$consulta_recursos_publicados = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM recursos
    WHERE id_asignatura = :id_asignatura
    AND id_profesor = :id_profesor
    AND visible = 1
");

$consulta_recursos_publicados->execute([
    "id_asignatura" => $id_asignatura,
    "id_profesor" => $id_profesor
]);

$total_recursos_publicados = (int) $consulta_recursos_publicados->fetch()["total"];

// Fin resumen docente


// Inicio consulta de tarea destacada

$consulta_tarea_destacada = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.titulo,
        ae.fecha_limite,
        COUNT(DISTINCT e.id_entrega) AS entregas_pendientes
    FROM actividades_evaluables ae
    LEFT JOIN entregas e
        ON e.id_actividad = ae.id_actividad
        AND e.estado IN ('entregada', 'tardia')
    WHERE ae.id_asignatura = :id_asignatura
    AND ae.id_profesor = :id_profesor
    AND ae.tipo_actividad IN ('tarea', 'practica')
    AND ae.visible = 1
    AND ae.estado = 'publicada'
    GROUP BY
        ae.id_actividad,
        ae.titulo,
        ae.fecha_limite
    ORDER BY
        entregas_pendientes DESC,
        CASE
            WHEN ae.fecha_limite IS NULL THEN 1
            ELSE 0
        END,
        ae.fecha_limite ASC,
        ae.fecha_creacion DESC
    LIMIT 1
");

$consulta_tarea_destacada->execute([
    "id_asignatura" => $id_asignatura,
    "id_profesor" => $id_profesor
]);

$tarea_destacada = $consulta_tarea_destacada->fetch();

// Fin consulta de tarea destacada


// Inicio consulta de próximo examen

$consulta_proximo_examen = $pdo->prepare("
    SELECT
        id_actividad,
        titulo,
        fecha_inicio,
        fecha_limite,
        duracion_minutos
    FROM actividades_evaluables
    WHERE id_asignatura = :id_asignatura
    AND id_profesor = :id_profesor
    AND tipo_actividad = 'examen'
    AND visible = 1
    AND estado = 'publicada'
    AND (
        fecha_limite IS NULL
        OR fecha_limite >= NOW()
    )
    ORDER BY
        CASE
            WHEN fecha_inicio <= NOW() THEN 0
            ELSE 1
        END,
        fecha_inicio ASC,
        fecha_creacion DESC
    LIMIT 1
");

$consulta_proximo_examen->execute([
    "id_asignatura" => $id_asignatura,
    "id_profesor" => $id_profesor
]);

$proximo_examen = $consulta_proximo_examen->fetch();

$fecha_proximo_examen = $proximo_examen
    ? formatear_fecha_detalle_profesor($proximo_examen["fecha_inicio"])
    : "-";

// Fin consulta de próximo examen


// Inicio consulta de actividad reciente

$consulta_actividad_reciente = $pdo->prepare("
    (
        SELECT
            'Recurso' AS tipo,
            titulo,
            fecha_publicacion AS fecha,
            'Publicado en recursos' AS detalle
        FROM recursos
        WHERE id_asignatura = :id_asignatura_recursos
        AND id_profesor = :id_profesor_recursos
        AND visible = 1
    )
    UNION ALL
    (
        SELECT
            CASE
                WHEN tipo_actividad = 'examen' THEN 'Examen'
                WHEN tipo_actividad = 'practica' THEN 'Práctica'
                ELSE 'Tarea'
            END AS tipo,
            titulo,
            fecha_creacion AS fecha,
            CASE
                WHEN estado = 'publicada' THEN 'Publicado'
                WHEN estado = 'borrador' THEN 'Guardado como borrador'
                ELSE 'Cerrado'
            END AS detalle
        FROM actividades_evaluables
        WHERE id_asignatura = :id_asignatura_actividades
        AND id_profesor = :id_profesor_actividades
        AND tipo_actividad IN ('tarea', 'practica', 'examen')
    )
    ORDER BY fecha DESC
    LIMIT 4
");

$consulta_actividad_reciente->execute([
    "id_asignatura_recursos" => $id_asignatura,
    "id_profesor_recursos" => $id_profesor,
    "id_asignatura_actividades" => $id_asignatura,
    "id_profesor_actividades" => $id_profesor
]);

$actividad_reciente = $consulta_actividad_reciente->fetchAll();

// Fin consulta de actividad reciente


// Inicio datos derivados

$clases_avance_demo = [
    "progreso-asignatura--avance-40-33",
    "progreso-asignatura--avance-40-333"
];

$clase_avance = $clases_avance_demo[$id_asignatura % count($clases_avance_demo)];

$titulo_unidad_actual = "Unidad 03. Aplicación práctica";
$descripcion_unidad_actual = $asignatura["descripcion"] !== null && $asignatura["descripcion"] !== ""
    ? $asignatura["descripcion"]
    : "Continúa gestionando recursos, tareas, exámenes y calificaciones de esta asignatura.";

$url_recursos = "recursos_profesor.php?id_asignatura=" . $id_asignatura;
$url_tareas = "listado_tareas_profesor.php?id_asignatura=" . $id_asignatura;
$url_examenes = "examenes_profesor.php?id_asignatura=" . $id_asignatura;
$url_calificaciones = "calificaciones_profesor.php?id_asignatura=" . $id_asignatura;
$url_crear_tarea = "crear_tarea.php?id_asignatura=" . $id_asignatura;
$url_crear_examen = "crear_examen.php?id_asignatura=" . $id_asignatura;

// Fin datos derivados
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title><?php echo limpiar_texto_doa($asignatura["nombre"]); ?> | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/detalle_asignatura_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-detalle-asignatura pagina-detalle-asignatura-profesor">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido principal -->

        <main class="contenido-doa contenido-detalle-asignatura contenido-detalle-asignatura-profesor">
            <div class="detalle-asignatura-grid">
                <section class="detalle-asignatura-principal">
                    <!-- Inicio cabecera de asignatura -->

                    <div class="cabecera-detalle-asignatura">
                        <div class="cabecera-detalle-asignatura__texto">
                            <a class="enlace-volver-asignaturas" href="asignaturas_profesor.php">
                                <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                    <img alt="" src="img/iconos/grey-chevron-right.svg">
                                </span>

                                <span>Volver a mis asignaturas</span>
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
                            <nav class="pestanas-asignatura pestanas-asignatura--destacada" aria-label="Secciones de la asignatura">
                                <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Recursos
                                </a>

                                <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_tareas); ?>">
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


                    <!-- Inicio resumen docente -->

                    <section class="resumen-metricas resumen-metricas--compacto" aria-label="Resumen docente de la asignatura">
                        <article class="tarjeta-metrica tarjeta-metrica--principal">
                            <span>Tareas activas</span>
                            <strong><?php echo $total_tareas_activas; ?></strong>
                        </article>

                        <article class="tarjeta-metrica">
                            <span>Entregas pendientes</span>
                            <strong><?php echo $total_entregas_pendientes; ?></strong>
                        </article>

                        <article class="tarjeta-metrica">
                            <span>Recursos publicados</span>
                            <strong><?php echo $total_recursos_publicados; ?></strong>
                        </article>

                        <article class="tarjeta-metrica">
                            <span>Próximo examen</span>
                            <strong><?php echo limpiar_texto_doa($fecha_proximo_examen); ?></strong>
                        </article>
                    </section>

                    <!-- Fin resumen docente -->


                    <!-- Inicio ruta de progreso -->

                    <article class="tarjeta-progreso-asignatura tarjeta-progreso-asignatura--activa tarjeta-progreso-asignatura--sin-margen">
                        <div class="tarjeta-progreso-asignatura__cabecera">
                            <h2>Ruta de progreso</h2>
                        </div>

                        <div class="progreso-asignatura <?php echo limpiar_texto_doa($clase_avance); ?>" aria-label="Ruta de progreso de la asignatura">
                            <span class="progreso-asignatura__destello" aria-hidden="true"></span>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada progreso-asignatura__unidad--ocultar-movil">
                                <span class="progreso-asignatura__badge"></span>

                                <span class="progreso-asignatura__estado" aria-hidden="true">
                                    <img alt="" src="img/iconos/blue-check.svg">
                                </span>

                                <a class="progreso-asignatura__nombre" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Unidad 01
                                </a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada">
                                <span class="progreso-asignatura__badge"></span>

                                <span class="progreso-asignatura__estado" aria-hidden="true">
                                    <img alt="" src="img/iconos/blue-check.svg">
                                </span>

                                <a class="progreso-asignatura__nombre" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Unidad 02
                                </a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--actual">
                                <span class="progreso-asignatura__badge">Actual</span>

                                <span class="progreso-asignatura__estado" aria-hidden="true">
                                    <img alt="" src="img/iconos/blue-play.svg">
                                </span>

                                <a class="progreso-asignatura__nombre" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Unidad 03
                                </a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada">
                                <span class="progreso-asignatura__badge"></span>

                                <span class="progreso-asignatura__estado" aria-hidden="true">
                                    <img alt="" src="img/iconos/grey-x.svg">
                                </span>

                                <a class="progreso-asignatura__nombre" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Unidad 04
                                </a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada progreso-asignatura__unidad--ocultar-movil">
                                <span class="progreso-asignatura__badge"></span>

                                <span class="progreso-asignatura__estado" aria-hidden="true">
                                    <img alt="" src="img/iconos/grey-x.svg">
                                </span>

                                <a class="progreso-asignatura__nombre" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Unidad 05
                                </a>
                            </div>
                        </div>
                    </article>

                    <!-- Fin ruta de progreso -->


                    <!-- Inicio unidad actual -->

                    <article class="tarjeta-unidad-actual">
                        <div class="tarjeta-unidad-actual__contenido">
                            <p class="tarjeta-unidad-actual__etiqueta">Unidad actual</p>

                            <h2><?php echo limpiar_texto_doa($titulo_unidad_actual); ?></h2>

                            <p>
                                <?php echo limpiar_texto_doa($descripcion_unidad_actual); ?>
                            </p>

                            <div class="acciones-docente-asignatura">
                                <a class="boton-docente-detalle boton-docente-detalle--principal" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Gestionar recursos
                                </a>

                                <a class="boton-docente-detalle" href="<?php echo limpiar_texto_doa($url_crear_tarea); ?>">
                                    Crear tarea
                                </a>

                                <a class="boton-docente-detalle" href="<?php echo limpiar_texto_doa($url_crear_examen); ?>">
                                    Crear examen
                                </a>
                            </div>
                        </div>
                    </article>

                    <!-- Fin unidad actual -->
                </section>

                <!-- Inicio panel lateral -->

                <aside class="detalle-asignatura-lateral">
                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">Tarea destacada</p>

                        <div class="tarjeta-lateral-panel__contenido">
                            <?php if (!$tarea_destacada) { ?>
                                <h2>Sin tareas publicadas</h2>

                                <p class="texto-vencimiento">
                                    Cuando crees una tarea, aparecerá aquí.
                                </p>

                                <a class="boton-secundario-panel" href="<?php echo limpiar_texto_doa($url_crear_tarea); ?>">
                                    Crear tarea
                                </a>
                            <?php } else { ?>
                                <h2><?php echo limpiar_texto_doa($tarea_destacada["titulo"]); ?></h2>

                                <p class="texto-vencimiento">
                                    <?php echo limpiar_texto_doa(obtener_texto_vencimiento_detalle_profesor($tarea_destacada["fecha_limite"])); ?>
                                </p>

                                <p>
                                    Entregas pendientes:
                                    <strong><?php echo (int) $tarea_destacada["entregas_pendientes"]; ?></strong>
                                </p>

                                <a class="boton-secundario-panel" href="detalle_tarea_profesor.php?id_actividad=<?php echo (int) $tarea_destacada["id_actividad"]; ?>">
                                    Revisar entregas
                                </a>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">Próximo examen</p>

                        <div class="tarjeta-lateral-panel__contenido">
                            <?php if (!$proximo_examen) { ?>
                                <h2>Sin exámenes próximos</h2>

                                <p class="texto-vencimiento">
                                    Los exámenes publicados aparecerán aquí.
                                </p>

                                <a class="boton-secundario-panel" href="<?php echo limpiar_texto_doa($url_crear_examen); ?>">
                                    Crear examen
                                </a>
                            <?php } else { ?>
                                <h2><?php echo limpiar_texto_doa($proximo_examen["titulo"]); ?></h2>

                                <ul class="lista-detalles-panel">
                                    <li>
                                        <img alt="" src="img/iconos/grey-calendar.svg">

                                        <span>
                                            <?php echo limpiar_texto_doa(obtener_texto_examen_detalle_profesor($proximo_examen["fecha_inicio"], $proximo_examen["fecha_limite"])); ?>
                                        </span>
                                    </li>

                                    <li>
                                        <img alt="" src="img/iconos/grey-clock.svg">

                                        <span>
                                            <?php echo limpiar_texto_doa(formatear_hora_detalle_profesor($proximo_examen["fecha_inicio"])); ?>
                                        </span>
                                    </li>

                                    <li>
                                        <img alt="" src="img/iconos/grey-notebook.svg">

                                        <span><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></span>
                                    </li>
                                </ul>

                                <a class="boton-secundario-panel" href="detalle_examen_profesor.php?id_actividad=<?php echo (int) $proximo_examen["id_actividad"]; ?>">
                                    Detalles
                                </a>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">Actividad reciente</p>

                        <div class="lista-actividad-docente">
                            <?php if (count($actividad_reciente) === 0) { ?>
                                <p>No hay actividad reciente en esta asignatura.</p>
                            <?php } ?>

                            <?php foreach ($actividad_reciente as $actividad) { ?>
                                <p>
                                    <strong><?php echo limpiar_texto_doa($actividad["tipo"]); ?>:</strong>
                                    <?php echo limpiar_texto_doa($actividad["titulo"]); ?>
                                    <br>
                                    <?php echo limpiar_texto_doa($actividad["detalle"]); ?>
                                    · <?php echo limpiar_texto_doa(formatear_fecha_detalle_profesor($actividad["fecha"])); ?>
                                </p>
                            <?php } ?>
                        </div>
                    </div>
                </aside>

                <!-- Fin panel lateral -->
            </div>
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>
</html>