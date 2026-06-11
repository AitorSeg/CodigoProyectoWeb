<?php
$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar asignatura...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_GET["id_asignatura"])) {
    header("Location: asignaturas.php");
    exit;
}

$id_alumno = (int) $_SESSION["doa_id_usuario"];
$id_asignatura = (int) $_GET["id_asignatura"];

$consulta_asignatura = $pdo->prepare("
    SELECT
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.descripcion,
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
        a.descripcion,
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

$consulta_proximo_examen = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.titulo,
        ae.fecha_inicio,
        ae.fecha_limite,
        ae.duracion_minutos
    FROM actividades_evaluables ae
    WHERE ae.id_asignatura = :id_asignatura
    AND ae.tipo_actividad = 'examen'
    AND ae.visible = 1
    AND ae.estado = 'publicada'
    AND (
        ae.fecha_limite IS NULL
        OR ae.fecha_limite >= NOW()
    )
    AND NOT EXISTS (
        SELECT 1
        FROM preguntas_examen p
        INNER JOIN respuestas_examen re
            ON re.id_pregunta = p.id_pregunta
            AND re.id_alumno = :id_alumno
        WHERE p.id_actividad = ae.id_actividad
    )
    ORDER BY
        CASE
            WHEN ae.fecha_inicio <= NOW() THEN 0
            ELSE 1
        END,
        ae.fecha_inicio ASC
    LIMIT 1
");

$consulta_proximo_examen->execute([
    "id_asignatura" => $id_asignatura,
    "id_alumno" => $id_alumno
]);

$proximo_examen = $consulta_proximo_examen->fetch();

$consulta_proxima_tarea = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.titulo,
        ae.fecha_limite
    FROM actividades_evaluables ae
    LEFT JOIN entregas e
        ON e.id_actividad = ae.id_actividad
        AND e.id_alumno = :id_alumno
    WHERE ae.id_asignatura = :id_asignatura
    AND ae.tipo_actividad IN ('tarea', 'practica')
    AND ae.visible = 1
    AND ae.estado = 'publicada'
    AND e.id_entrega IS NULL
    ORDER BY ae.fecha_limite ASC
    LIMIT 1
");

$consulta_proxima_tarea->execute([
    "id_alumno" => $id_alumno,
    "id_asignatura" => $id_asignatura
]);

$proxima_tarea = $consulta_proxima_tarea->fetch();

$profesores = $asignatura["profesores"] !== null ? $asignatura["profesores"] : "Pendiente";

$clases_avance_demo = [
    "progreso-asignatura--avance-40-33",
    "progreso-asignatura--avance-40-333"
];

$clase_avance = $clases_avance_demo[$id_asignatura % count($clases_avance_demo)];

$titulo_unidad_actual = "Unidad 03. Aplicación práctica";
$descripcion_unidad_actual = $asignatura["descripcion"] !== null && $asignatura["descripcion"] !== ""
    ? $asignatura["descripcion"]
    : "Continúa trabajando los contenidos principales de la asignatura mediante recursos, tareas y actividades evaluables.";

$url_recursos = "recursos_alumno.php?id_asignatura=" . $id_asignatura;
$url_tareas = "listado_tareas.php?id_asignatura=" . $id_asignatura;
$url_examenes = "examenes.php?id_asignatura=" . $id_asignatura;
$url_calificaciones = "calificaciones.php?id_asignatura=" . $id_asignatura;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title><?php echo limpiar_texto_doa($asignatura["nombre"]); ?> | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-detalle-asignatura">
    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa contenido-detalle-asignatura">
            <div class="detalle-asignatura-grid">
                <section class="detalle-asignatura-principal">
                    <div class="cabecera-detalle-asignatura">
                        <div class="cabecera-detalle-asignatura__texto">
                            <a class="enlace-volver-asignaturas" href="asignaturas.php">
                                <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                    <img alt="" src="img/iconos/grey-chevron-right.svg">
                                </span>

                                <span>Volver a mis asignaturas</span>
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

                                <li>
                                    <img alt="" src="img/iconos/grey-play.svg">
                                    <span>Unidad actual</span>
                                </li>
                            </ul>
                        </div>

                        <div class="cabecera-detalle-asignatura__pestanas">
                            <nav aria-label="Secciones de la asignatura" class="pestanas-asignatura pestanas-asignatura--destacada">
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

                    <article class="tarjeta-progreso-asignatura tarjeta-progreso-asignatura--activa tarjeta-progreso-asignatura--sin-margen">
                        <div class="tarjeta-progreso-asignatura__cabecera">
                            <h2>Ruta de progreso</h2>
                        </div>

                        <div
                            aria-label="Ruta de progreso de la asignatura"
                            class="progreso-asignatura <?php echo limpiar_texto_doa($clase_avance); ?>">

                            <span class="progreso-asignatura__destello" aria-hidden="true"></span>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada progreso-asignatura__unidad--ocultar-movil">
                                <span class="progreso-asignatura__badge"></span>

                                <span aria-hidden="true" class="progreso-asignatura__estado">
                                    <img alt="" src="img/iconos/blue-check.svg">
                                </span>

                                <a class="progreso-asignatura__nombre" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Unidad 01
                                </a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada">
                                <span class="progreso-asignatura__badge"></span>

                                <span aria-hidden="true" class="progreso-asignatura__estado">
                                    <img alt="" src="img/iconos/blue-check.svg">
                                </span>

                                <a class="progreso-asignatura__nombre" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Unidad 02
                                </a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--actual">
                                <span class="progreso-asignatura__badge">
                                    Actual
                                </span>

                                <span aria-hidden="true" class="progreso-asignatura__estado">
                                    <img alt="" src="img/iconos/blue-play.svg">
                                </span>

                                <a class="progreso-asignatura__nombre" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Unidad 03
                                </a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada">
                                <span class="progreso-asignatura__badge"></span>

                                <span aria-hidden="true" class="progreso-asignatura__estado">
                                    <img alt="" src="img/iconos/grey-x.svg">
                                </span>

                                <a class="progreso-asignatura__nombre" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Unidad 04
                                </a>
                            </div>

                            <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada progreso-asignatura__unidad--ocultar-movil">
                                <span class="progreso-asignatura__badge"></span>

                                <span aria-hidden="true" class="progreso-asignatura__estado">
                                    <img alt="" src="img/iconos/grey-x.svg">
                                </span>

                                <a class="progreso-asignatura__nombre" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Unidad 05
                                </a>
                            </div>
                        </div>
                    </article>

                    <article class="tarjeta-unidad-actual">
                        <div class="tarjeta-unidad-actual__contenido">
                            <p class="tarjeta-unidad-actual__etiqueta">
                                Unidad actual
                            </p>

                            <h2>
                                <?php echo limpiar_texto_doa($titulo_unidad_actual); ?>
                            </h2>

                            <p>
                                <?php echo limpiar_texto_doa($descripcion_unidad_actual); ?>
                            </p>

                            <div class="tarjeta-unidad-actual__bloques">
                                <article class="bloque-unidad">
                                    <strong>Objetivo</strong>
                                    <span>Avanzar en los contenidos principales de la asignatura.</span>
                                </article>

                                <article class="bloque-unidad">
                                    <strong>Estado</strong>
                                    <span>Unidad en curso dentro de la ruta de progreso de la asignatura.</span>
                                </article>

                                <article class="bloque-unidad">
                                    <strong>Siguiente paso</strong>
                                    <span>Revisar los recursos disponibles y completar las tareas publicadas.</span>
                                </article>
                            </div>

                            <div class="tarjeta-unidad-actual__acciones">
                                <a class="boton-entrar-asignatura" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                    Recursos del tema
                                </a>

                                <div class="tarjeta-unidad-actual__enlaces">
                                    <a href="<?php echo limpiar_texto_doa($url_tareas); ?>">
                                        Tareas
                                    </a>

                                    <a href="<?php echo limpiar_texto_doa($url_examenes); ?>">
                                        Exámenes
                                    </a>

                                    <a href="<?php echo limpiar_texto_doa($url_calificaciones); ?>">
                                        Calificaciones
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                </section>

                <aside class="panel-derecho-asignatura">
                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">
                            Próxima evaluación
                        </p>

                        <div class="tarjeta-lateral-panel__contenido">
                            <?php if (!$proximo_examen) { ?>
                                <h2>No hay exámenes próximos</h2>

                                <p class="texto-vencimiento">
                                    Cuando se publique un examen de esta asignatura, aparecerá aquí.
                                </p>

                                <a class="boton-secundario-panel" href="<?php echo limpiar_texto_doa($url_examenes); ?>">
                                    Ver exámenes
                                </a>
                            <?php } else { ?>
                                <h2>
                                    <?php echo limpiar_texto_doa($proximo_examen["titulo"]); ?>
                                </h2>

                                <ul class="lista-detalles-panel">
                                    <li>
                                        <img alt="" src="img/iconos/grey-calendar.svg">
                                        <span>
                                            <?php echo date("d/m/Y", strtotime($proximo_examen["fecha_inicio"])); ?>
                                        </span>
                                    </li>

                                    <li>
                                        <img alt="" src="img/iconos/grey-clock.svg">
                                        <span>
                                            <?php echo date("H:i", strtotime($proximo_examen["fecha_inicio"])); ?>
                                        </span>
                                    </li>

                                    <?php if ($proximo_examen["duracion_minutos"] !== null) { ?>
                                        <li>
                                            <img alt="" src="img/iconos/grey-notebook.svg">
                                            <span>
                                                <?php echo (int) $proximo_examen["duracion_minutos"]; ?> minutos
                                            </span>
                                        </li>
                                    <?php } ?>
                                </ul>

                                <a class="boton-secundario-panel" href="detalle_examen.php?id_actividad=<?php echo (int) $proximo_examen["id_actividad"]; ?>">
                                    Ver detalles
                                </a>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">
                            Próxima tarea
                        </p>

                        <div class="tarjeta-lateral-panel__contenido">
                            <?php if (!$proxima_tarea) { ?>
                                <h2>No hay tareas pendientes</h2>

                                <p class="texto-vencimiento">
                                    Las tareas publicadas de esta asignatura aparecerán aquí.
                                </p>

                                <a class="boton-secundario-panel" href="<?php echo limpiar_texto_doa($url_tareas); ?>">
                                    Ver tareas
                                </a>
                            <?php } else { ?>
                                <h2>
                                    <?php echo limpiar_texto_doa($proxima_tarea["titulo"]); ?>
                                </h2>

                                <p class="texto-vencimiento">
                                    <?php if ($proxima_tarea["fecha_limite"] !== null) { ?>
                                        Vence:
                                        <strong>
                                            <?php echo date("d/m/Y", strtotime($proxima_tarea["fecha_limite"])); ?>
                                        </strong>
                                    <?php } else { ?>
                                        Sin fecha límite definida.
                                    <?php } ?>
                                </p>

                                <a class="boton-secundario-panel" href="detalle_tarea.php?id_actividad=<?php echo (int) $proxima_tarea["id_actividad"]; ?>">
                                    Ir a la tarea
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </div>
</body>

</html>