<?php
$rol_pagina = "alumno";
$pagina_activa = "panel";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar asignatura...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

$id_alumno = (int) $_SESSION["doa_id_usuario"];

function calcular_progreso_panel_demo($indice)
{
    $progresos_demo = [
        ["valor" => 58, "clase_barra" => "relleno-progreso--58", "clase_avance" => "progreso-asignatura--avance-40-33"],
        ["valor" => 46, "clase_barra" => "relleno-progreso--46", "clase_avance" => "progreso-asignatura--avance-40-333"],
        ["valor" => 42, "clase_barra" => "relleno-progreso--42", "clase_avance" => "progreso-asignatura--avance-40-333"]
    ];

    return $progresos_demo[$indice % count($progresos_demo)];
}

$consulta_asignaturas = $pdo->prepare("
    SELECT
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.grupo
    FROM asignaturas a
    INNER JOIN usuarios_asignaturas ua
        ON ua.id_asignatura = a.id_asignatura
        AND ua.id_usuario = :id_alumno
        AND ua.rol_asignatura = 'alumno'
        AND ua.estado = 'activa'
    WHERE a.estado = 'activa'
    ORDER BY a.nombre ASC
    LIMIT 3
");

$consulta_asignaturas->execute([
    "id_alumno" => $id_alumno
]);

$asignaturas_panel = $consulta_asignaturas->fetchAll();

$consulta_proxima_evaluacion = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.titulo,
        ae.fecha_inicio,
        ae.duracion_minutos,
        a.nombre AS asignatura_nombre
    FROM actividades_evaluables ae
    INNER JOIN usuarios_asignaturas ua
        ON ua.id_asignatura = ae.id_asignatura
        AND ua.id_usuario = :id_alumno
        AND ua.rol_asignatura = 'alumno'
        AND ua.estado = 'activa'
    INNER JOIN asignaturas a
        ON a.id_asignatura = ae.id_asignatura
    WHERE ae.tipo_actividad = 'examen'
    AND ae.visible = 1
    AND ae.estado = 'publicada'
    AND ae.fecha_inicio IS NOT NULL
    AND ae.fecha_inicio >= NOW()
    ORDER BY ae.fecha_inicio ASC
    LIMIT 1
");

$consulta_proxima_evaluacion->execute([
    "id_alumno" => $id_alumno
]);

$proxima_evaluacion = $consulta_proxima_evaluacion->fetch();

$consulta_tareas_pendientes = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.titulo,
        ae.fecha_limite,
        a.nombre AS asignatura_nombre
    FROM actividades_evaluables ae
    INNER JOIN usuarios_asignaturas ua
        ON ua.id_asignatura = ae.id_asignatura
        AND ua.id_usuario = :id_alumno
        AND ua.rol_asignatura = 'alumno'
        AND ua.estado = 'activa'
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
    LIMIT 2
");

$consulta_tareas_pendientes->execute([
    "id_alumno" => $id_alumno,
    "id_alumno_entrega" => $id_alumno
]);

$tareas_pendientes = $consulta_tareas_pendientes->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Panel principal | DOA
    </title>
    <!-- Enlaces a hojas de estilo -->
    <link href="css/doa.css" rel="stylesheet" />
    <link href="css/doa_layout.css" rel="stylesheet" />
    <link href="css/doa_componentes.css" rel="stylesheet" />
    <link href="css/panel_principal.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet" />
    <!-- Fin enlaces a hojas de estilo -->
</head>

<body class="pagina-doa pagina-panel-principal">
    <!-- Header -->
    <?php include "includes/header-doa.php"; ?>
    <!-- Inicio del contenido principal -->
    <div class="layout-doa">
        <!-- Barra Lateral -->
        <?php include "includes/barra-lateral-doa.php"; ?>
        <!-- Inicio del contenido principal de la página -->
        <main class="contenido-doa contenido-panel-principal">
            <div class="panel-principal-grid">
                <!-- Inicio del bloque del panel principal -->
                <section class="bloque-panel-principal">
                    <div class="cabecera-bloque-panel">
                        <h1>
                            Mis asignaturas
                        </h1>
                        <a class="cabecera-bloque-panel__enlace" href="asignaturas.php">
                            VER TODAS LAS ASIGNATURAS
                        </a>
                    </div>
                    <?php if (count($asignaturas_panel) === 0) { ?>
                        <div class="resumen-asignaturas">
                            <article class="tarjeta-asignatura-resumen tarjeta-asignatura-resumen--activa">
                                <span class="tarjeta-asignatura-resumen__nombre">
                                    Sin asignaturas
                                </span>
                                <span aria-hidden="true" class="tarjeta-asignatura-resumen__punto"></span>
                                <span aria-hidden="true" class="tarjeta-asignatura-resumen__barra">
                                    <span class="relleno-progreso--42"></span>
                                </span>
                            </article>
                        </div>

                        <div class="lista-progresos-asignaturas" id="listaProgresosAsignaturas">
                            <article class="tarjeta-progreso-asignatura tarjeta-progreso-asignatura--activa">
                                <div class="tarjeta-progreso-asignatura__cabecera">
                                    <h2>No hay asignaturas asignadas</h2>
                                    <a class="boton-entrar-asignatura" href="asignaturas.php">
                                        Ver asignaturas
                                    </a>
                                </div>

                                <p>
                                    Secretaría debe asignarte a una asignatura para que aparezca aquí.
                                </p>
                            </article>
                        </div>
                    <?php } else { ?>
                        <div class="resumen-asignaturas">
                            <?php foreach ($asignaturas_panel as $indice => $asignatura) { ?>
                                <?php
                                $progreso_demo = calcular_progreso_panel_demo($indice);
                                $clase_activa = $indice === 0 ? " tarjeta-asignatura-resumen--activa" : "";
                                $clase_ocultar_movil = $indice >= 2 ? " tarjeta-asignatura-resumen--ocultar-movil" : "";
                                ?>

                                <button
                                    class="tarjeta-asignatura-resumen<?php echo $clase_activa . $clase_ocultar_movil; ?>"
                                    data-asignatura="<?php echo (int) $asignatura["id_asignatura"]; ?>"
                                    type="button">

                                    <span class="tarjeta-asignatura-resumen__nombre">
                                        <?php echo limpiar_texto_doa($asignatura["nombre"]); ?>
                                    </span>

                                    <span aria-hidden="true" class="tarjeta-asignatura-resumen__punto"></span>

                                    <span aria-hidden="true" class="tarjeta-asignatura-resumen__barra">
                                        <span class="<?php echo limpiar_texto_doa($progreso_demo["clase_barra"]); ?>"></span>
                                    </span>
                                </button>
                            <?php } ?>
                        </div>

                        <div class="lista-progresos-asignaturas" id="listaProgresosAsignaturas">
                            <?php foreach ($asignaturas_panel as $indice => $asignatura) { ?>
                                <?php
                                $progreso_demo = calcular_progreso_panel_demo($indice);
                                $clase_tarjeta = $indice === 0
                                    ? "tarjeta-progreso-asignatura tarjeta-progreso-asignatura--activa"
                                    : "tarjeta-progreso-asignatura tarjeta-progreso-asignatura--secundaria";

                                if ($indice >= 2) {
                                    $clase_tarjeta .= " tarjeta-progreso-asignatura--ocultar-movil";
                                }

                                $color_iconos = $indice === 0 ? "blue" : "grey";
                                ?>

                                <article
                                    class="<?php echo limpiar_texto_doa($clase_tarjeta); ?>"
                                    data-asignatura="<?php echo (int) $asignatura["id_asignatura"]; ?>">

                                    <div class="tarjeta-progreso-asignatura__cabecera">
                                        <h2>
                                            Progreso <?php echo limpiar_texto_doa($asignatura["nombre"]); ?>
                                        </h2>

                                        <a class="boton-entrar-asignatura" href="detalle_asignatura.php?id_asignatura=<?php echo (int) $asignatura["id_asignatura"]; ?>">
                                            Entrar
                                        </a>
                                    </div>

                                    <div
                                        aria-label="Progreso de <?php echo limpiar_texto_doa($asignatura["nombre"]); ?>"
                                        class="progreso-asignatura <?php echo limpiar_texto_doa($progreso_demo["clase_avance"]); ?>">

                                        <span class="progreso-asignatura__destello" aria-hidden="true"></span>

                                        <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada progreso-asignatura__unidad--ocultar-movil">
                                            <span class="progreso-asignatura__badge"></span>
                                            <span aria-hidden="true" class="progreso-asignatura__estado">
                                                <img alt="" src="img/iconos/<?php echo $color_iconos; ?>-check.svg">
                                            </span>
                                            <a class="progreso-asignatura__nombre" href="detalle_asignatura.php?id_asignatura=<?php echo (int) $asignatura["id_asignatura"]; ?>">
                                                Unidad 01
                                            </a>
                                        </div>

                                        <div class="progreso-asignatura__unidad progreso-asignatura__unidad--completada">
                                            <span class="progreso-asignatura__badge"></span>
                                            <span aria-hidden="true" class="progreso-asignatura__estado">
                                                <img alt="" src="img/iconos/<?php echo $color_iconos; ?>-check.svg">
                                            </span>
                                            <a class="progreso-asignatura__nombre" href="detalle_asignatura.php?id_asignatura=<?php echo (int) $asignatura["id_asignatura"]; ?>">
                                                Unidad 02
                                            </a>
                                        </div>

                                        <div class="progreso-asignatura__unidad progreso-asignatura__unidad--actual">
                                            <span class="progreso-asignatura__badge">
                                                Actual
                                            </span>
                                            <span aria-hidden="true" class="progreso-asignatura__estado">
                                                <img alt="" src="img/iconos/<?php echo $color_iconos; ?>-play.svg">
                                            </span>
                                            <a class="progreso-asignatura__nombre" href="detalle_asignatura.php?id_asignatura=<?php echo (int) $asignatura["id_asignatura"]; ?>">
                                                Unidad 03
                                            </a>
                                        </div>

                                        <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada">
                                            <span class="progreso-asignatura__badge"></span>
                                            <span aria-hidden="true" class="progreso-asignatura__estado">
                                                <img alt="" src="img/iconos/grey-x.svg">
                                            </span>
                                            <a class="progreso-asignatura__nombre" href="detalle_asignatura.php?id_asignatura=<?php echo (int) $asignatura["id_asignatura"]; ?>">
                                                Unidad 04
                                            </a>
                                        </div>

                                        <div class="progreso-asignatura__unidad progreso-asignatura__unidad--bloqueada progreso-asignatura__unidad--ocultar-movil">
                                            <span class="progreso-asignatura__badge"></span>
                                            <span aria-hidden="true" class="progreso-asignatura__estado">
                                                <img alt="" src="img/iconos/grey-x.svg">
                                            </span>
                                            <a class="progreso-asignatura__nombre" href="detalle_asignatura.php?id_asignatura=<?php echo (int) $asignatura["id_asignatura"]; ?>">
                                                Unidad 05
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </section>
                <!-- Final del bloque del panel principal -->
                <div class="panel-derecho">
                    <div class="tarjeta-lateral-panel">
                        <p class="tarjeta-lateral-panel__titulo">
                            Próxima evaluación
                        </p>

                        <div class="tarjeta-lateral-panel__contenido">
                            <?php if (!$proxima_evaluacion) { ?>
                                <h2>No hay exámenes próximos</h2>

                                <p class="texto-vencimiento">
                                    Cuando un profesor publique un examen, aparecerá aquí.
                                </p>

                                <a class="boton-secundario-panel" href="examenes.php">
                                    Ver exámenes
                                </a>
                            <?php } else { ?>
                                <h2>
                                    <?php echo limpiar_texto_doa($proxima_evaluacion["titulo"]); ?>
                                </h2>

                                <ul class="lista-detalles-panel">
                                    <li>
                                        <img alt="" src="img/iconos/grey-calendar.svg">
                                        <span>
                                            <?php echo date("d/m/Y", strtotime($proxima_evaluacion["fecha_inicio"])); ?>
                                        </span>
                                    </li>

                                    <li>
                                        <img alt="" src="img/iconos/grey-clock.svg">
                                        <span>
                                            <?php echo date("H:i", strtotime($proxima_evaluacion["fecha_inicio"])); ?>
                                        </span>
                                    </li>

                                    <li>
                                        <img alt="" src="img/iconos/grey-notebook.svg">
                                        <span>
                                            <?php echo limpiar_texto_doa($proxima_evaluacion["asignatura_nombre"]); ?>
                                        </span>
                                    </li>
                                </ul>

                                <a class="boton-secundario-panel" href="detalle_examen.php?id_actividad=<?php echo (int) $proxima_evaluacion["id_actividad"]; ?>">
                                    Ver detalles
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if (count($tareas_pendientes) === 0) { ?>
                        <div class="tarjeta-lateral-panel">
                            <p class="tarjeta-lateral-panel__titulo">
                                Tareas activas
                            </p>

                            <div class="tarjeta-lateral-panel__contenido">
                                <h2>No hay tareas pendientes</h2>

                                <p class="texto-vencimiento">
                                    Las tareas publicadas aparecerán aquí.
                                </p>

                                <a class="boton-secundario-panel" href="listado_tareas.php">
                                    Ver tareas
                                </a>
                            </div>
                        </div>
                    <?php } ?>

                    <?php foreach ($tareas_pendientes as $tarea) { ?>
                        <div class="tarjeta-lateral-panel">
                            <p class="tarjeta-lateral-panel__titulo">
                                Tarea activa
                            </p>

                            <div class="tarjeta-lateral-panel__contenido">
                                <h2>
                                    <?php echo limpiar_texto_doa($tarea["titulo"]); ?>
                                </h2>

                                <p class="texto-vencimiento">
                                    <?php echo limpiar_texto_doa($tarea["asignatura_nombre"]); ?>
                                    <?php if ($tarea["fecha_limite"] !== null) { ?>
                                        <br>
                                        Vence:
                                        <strong>
                                            <?php echo date("d/m/Y", strtotime($tarea["fecha_limite"])); ?>
                                        </strong>
                                    <?php } ?>
                                </p>

                                <a class="boton-secundario-panel" href="detalle_tarea.php?id_actividad=<?php echo (int) $tarea["id_actividad"]; ?>">
                                    Ir a la tarea
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </main>
        <!-- Final del contenido principal de la página -->
    </div>
    <!-- Final del contenido principal -->

    <script src="js/panel_principal.js">
    </script>
</body>

</html>