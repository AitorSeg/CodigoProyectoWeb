<?php
$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar asignatura...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

$id_alumno = (int) $_SESSION["doa_id_usuario"];

$consulta_asignaturas = $pdo->prepare("
    SELECT
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.descripcion,
        a.curso,
        a.grupo,
        a.estado,
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
    WHERE a.estado = 'activa'
    GROUP BY
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.descripcion,
        a.curso,
        a.grupo,
        a.estado
    ORDER BY a.nombre ASC, a.grupo ASC
");

$consulta_asignaturas->execute([
    "id_alumno" => $id_alumno
]);

$asignaturas = $consulta_asignaturas->fetchAll();

$consulta_tareas_pendientes = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.titulo,
        a.nombre AS asignatura_nombre
    FROM actividades_evaluables ae
    INNER JOIN usuarios_asignaturas ua_alumno
        ON ua_alumno.id_asignatura = ae.id_asignatura
        AND ua_alumno.id_usuario = :id_alumno
        AND ua_alumno.rol_asignatura = 'alumno'
        AND ua_alumno.estado = 'activa'
    INNER JOIN asignaturas a
        ON a.id_asignatura = ae.id_asignatura
    LEFT JOIN entregas e
        ON e.id_actividad = ae.id_actividad
        AND e.id_alumno = :id_alumno_entrega
    WHERE ae.tipo_actividad IN ('tarea', 'practica')
    AND ae.visible = 1
    AND ae.estado = 'publicada'
    AND e.id_entrega IS NULL
    ORDER BY ae.fecha_limite ASC
    LIMIT 5
");

$consulta_tareas_pendientes->execute([
    "id_alumno" => $id_alumno,
    "id_alumno_entrega" => $id_alumno
]);

$tareas_pendientes = $consulta_tareas_pendientes->fetchAll();

$consulta_proxima_evaluacion = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.titulo,
        ae.fecha_inicio,
        ae.fecha_limite,
        a.nombre AS asignatura_nombre
    FROM actividades_evaluables ae
    INNER JOIN usuarios_asignaturas ua_alumno
        ON ua_alumno.id_asignatura = ae.id_asignatura
        AND ua_alumno.id_usuario = :id_alumno
        AND ua_alumno.rol_asignatura = 'alumno'
        AND ua_alumno.estado = 'activa'
    INNER JOIN asignaturas a
        ON a.id_asignatura = ae.id_asignatura
    WHERE ae.tipo_actividad = 'examen'
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
            AND re.id_alumno = :id_alumno_respuestas
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

$consulta_proxima_evaluacion->execute([
    "id_alumno" => $id_alumno,
    "id_alumno_respuestas" => $id_alumno
]);

$proxima_evaluacion = $consulta_proxima_evaluacion->fetch();

$total_asignaturas = count($asignaturas);
$total_tareas_pendientes = count($tareas_pendientes);
$total_proximas_evaluaciones = $proxima_evaluacion ? 1 : 0;

function calcular_progreso_demo($id_asignatura)
{
    $progresos_demo = [58, 46, 72, 35, 81, 63, 49, 67];

    return $progresos_demo[$id_asignatura % count($progresos_demo)];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Mis asignaturas | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/asignaturas.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-asignaturas">
    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa">
            <section class="cabecera-asignaturas">
                <h1>Mis asignaturas</h1>
            </section>

            <section aria-label="Resumen de asignaturas" class="resumen-general-asignaturas">
                <article class="dato-asignaturas">
                    <button aria-controls="detalleAsignaturasActivas" aria-expanded="false" class="dato-asignaturas__boton" type="button">
                        <span class="dato-asignaturas__numero">
                            <?php echo $total_asignaturas; ?>
                        </span>

                        <span class="dato-asignaturas__contenido">
                            <span class="dato-asignaturas__texto">Asignaturas activas</span>
                            <span class="dato-asignaturas__accion">Ver listado</span>
                        </span>

                        <span aria-hidden="true" class="dato-asignaturas__flecha">
                            <img alt="" src="img/iconos/grey-chevron-right.svg">
                        </span>
                    </button>

                    <div class="dato-asignaturas__detalle" hidden id="detalleAsignaturasActivas">
                        <?php if ($total_asignaturas === 0) { ?>
                            <span class="enlace-detalle-asignatura">
                                No tienes asignaturas asignadas.
                            </span>
                        <?php } ?>

                        <?php foreach ($asignaturas as $asignatura) { ?>
                            <a class="enlace-detalle-asignatura" href="detalle_asignatura.php?id_asignatura=<?php echo (int) $asignatura["id_asignatura"]; ?>">
                                <?php echo limpiar_texto_doa($asignatura["nombre"]); ?>
                            </a>
                        <?php } ?>
                    </div>
                </article>

                <article class="dato-asignaturas">
                    <button aria-controls="detalleTareasPendientes" aria-expanded="false" class="dato-asignaturas__boton" type="button">
                        <span class="dato-asignaturas__numero">
                            <?php echo $total_tareas_pendientes; ?>
                        </span>

                        <span class="dato-asignaturas__contenido">
                            <span class="dato-asignaturas__texto">Tareas pendientes</span>
                            <span class="dato-asignaturas__accion">Revisar tareas</span>
                        </span>

                        <span aria-hidden="true" class="dato-asignaturas__flecha">
                            <img alt="" src="img/iconos/grey-chevron-right.svg">
                        </span>
                    </button>

                    <div class="dato-asignaturas__detalle" hidden id="detalleTareasPendientes">
                        <?php if ($total_tareas_pendientes === 0) { ?>
                            <span class="enlace-detalle-asignatura">
                                No hay tareas pendientes.
                            </span>
                        <?php } ?>

                        <?php foreach ($tareas_pendientes as $tarea) { ?>
                            <a class="enlace-detalle-asignatura" href="detalle_tarea.php?id_actividad=<?php echo (int) $tarea["id_actividad"]; ?>">
                                <?php echo limpiar_texto_doa($tarea["titulo"]); ?>
                                · <?php echo limpiar_texto_doa($tarea["asignatura_nombre"]); ?>
                            </a>
                        <?php } ?>
                    </div>
                </article>

                <article class="dato-asignaturas">
                    <button aria-controls="detalleProximaEvaluacion" aria-expanded="false" class="dato-asignaturas__boton" type="button">
                        <span class="dato-asignaturas__numero">
                            <?php echo $total_proximas_evaluaciones; ?>
                        </span>

                        <span class="dato-asignaturas__contenido">
                            <span class="dato-asignaturas__texto">Próxima evaluación</span>
                            <span class="dato-asignaturas__accion">Ver examen</span>
                        </span>

                        <span aria-hidden="true" class="dato-asignaturas__flecha">
                            <img alt="" src="img/iconos/grey-chevron-right.svg">
                        </span>
                    </button>

                    <div class="dato-asignaturas__detalle" hidden id="detalleProximaEvaluacion">
                        <?php if (!$proxima_evaluacion) { ?>
                            <span class="enlace-detalle-asignatura">
                                No hay exámenes próximos.
                            </span>
                        <?php } else { ?>
                            <a class="enlace-detalle-asignatura" href="detalle_examen.php?id_actividad=<?php echo (int) $proxima_evaluacion["id_actividad"]; ?>">
                                <?php echo limpiar_texto_doa($proxima_evaluacion["titulo"]); ?>
                                · <?php echo limpiar_texto_doa($proxima_evaluacion["asignatura_nombre"]); ?>
                            </a>
                        <?php } ?>
                    </div>
                </article>
            </section>

            <section aria-label="Listado de asignaturas" class="lista-asignaturas">
                <?php if ($total_asignaturas === 0) { ?>
                    <article class="tarjeta-asignatura">
                        <div class="tarjeta-asignatura__cabecera">
                            <div>
                                <h2>No hay asignaturas asignadas</h2>
                                <p>Secretaría debe asignarte a una asignatura para que aparezca aquí.</p>
                            </div>
                        </div>
                    </article>
                <?php } ?>

                <?php foreach ($asignaturas as $indice => $asignatura) { ?>
                    <?php
                    $profesores = $asignatura["profesores"] !== null ? $asignatura["profesores"] : "Pendiente";
                    $clase_tarjeta_activa = $indice === 0 ? " tarjeta-asignatura--activa" : "";
                    $progreso_demo = calcular_progreso_demo((int) $asignatura["id_asignatura"]);
                    ?>

                    <article class="tarjeta-asignatura<?php echo $clase_tarjeta_activa; ?>">
                        <div class="tarjeta-asignatura__cabecera">
                            <div>
                                <h2>
                                    <?php echo limpiar_texto_doa($asignatura["nombre"]); ?>
                                </h2>

                                <p>
                                    Profesor:
                                    <?php echo limpiar_texto_doa($profesores); ?>
                                </p>
                            </div>

                            <span class="estado-asignatura">
                                Activa
                            </span>
                        </div>

                        <div class="tarjeta-asignatura__metadatos">
                            <div class="tarjeta-asignatura__detalle">
                                <span>Código</span>
                                <strong>
                                    <?php echo limpiar_texto_doa($asignatura["codigo"]); ?>
                                </strong>
                            </div>

                            <div class="tarjeta-asignatura__detalle">
                                <span>Grupo</span>
                                <strong>
                                    <?php echo limpiar_texto_doa($asignatura["curso"]); ?>
                                    · Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                                </strong>
                            </div>
                        </div>

                        <?php if ($asignatura["descripcion"] !== null && $asignatura["descripcion"] !== "") { ?>
                            <div class="tarjeta-asignatura__detalle tarjeta-asignatura__detalle--descripcion">
                                <span>Descripción</span>
                                <strong>
                                    <?php echo limpiar_texto_doa($asignatura["descripcion"]); ?>
                                </strong>
                            </div>
                        <?php } ?>

                        <div class="tarjeta-asignatura__progreso">
                            <div class="tarjeta-asignatura__progreso-texto">
                                <span>Progreso del curso</span>
                                <strong><?php echo $progreso_demo; ?>%</strong>
                            </div>

                            <progress
                                class="barra-asignatura"
                                value="<?php echo $progreso_demo; ?>"
                                max="100">
                                <?php echo $progreso_demo; ?>%
                            </progress>
                        </div>

                        <div class="tarjeta-asignatura__acciones">
                            <a class="boton-asignatura boton-asignatura--principal" href="detalle_asignatura.php?id_asignatura=<?php echo (int) $asignatura["id_asignatura"]; ?>">
                                Entrar
                            </a>

                            <a class="boton-asignatura" href="recursos_alumno.php?id_asignatura=<?php echo (int) $asignatura["id_asignatura"]; ?>">
                                Recursos
                            </a>

                            <a class="boton-asignatura" href="listado_tareas.php?id_asignatura=<?php echo (int) $asignatura["id_asignatura"]; ?>">
                                Tareas
                            </a>
                        </div>
                    </article>
                <?php } ?>
            </section>
        </main>
    </div>

    <script src="js/asignaturas.js"></script>
</body>

</html>