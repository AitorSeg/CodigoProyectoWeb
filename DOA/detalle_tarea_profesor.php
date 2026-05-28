<?php
// Inicio configuración de página

$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar tarea...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página


// Inicio funciones auxiliares

function formatear_fecha_profesor($fecha) {
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y", strtotime($fecha));
}

function obtener_estado_entrega_profesor($entrega_estado, $id_calificacion) {
    if ($id_calificacion !== null) {
        return "calificada";
    }

    if ($entrega_estado === null) {
        return "pendiente";
    }

    return $entrega_estado;
}

function obtener_texto_estado_entrega_profesor($estado) {
    return match ($estado) {
        "pendiente" => "Pendiente",
        "entregada" => "Entregada",
        "tardia" => "Tardía",
        "revisada" => "Revisada",
        "calificada" => "Calificada",
        default => ucfirst($estado),
    };
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

if (!isset($_GET["id_actividad"])) {
    header("Location: asignaturas_profesor.php");
    exit;
}

$id_profesor = (int) $_SESSION["doa_id_usuario"];
$id_actividad = (int) $_GET["id_actividad"];

// Fin parámetros de pantalla


// Inicio consulta de tarea

$consulta_tarea = $pdo->prepare("
    SELECT
        ae.id_actividad,
        ae.id_asignatura,
        ae.tipo_actividad,
        ae.unidad,
        ae.titulo,
        ae.descripcion,
        ae.fecha_inicio,
        ae.fecha_limite,
        ae.estado,
        ae.visible,
        a.nombre AS asignatura_nombre,
        a.codigo,
        a.curso,
        a.grupo
    FROM actividades_evaluables ae
    INNER JOIN asignaturas a
        ON a.id_asignatura = ae.id_asignatura
    INNER JOIN usuarios_asignaturas ua_profesor
        ON ua_profesor.id_asignatura = ae.id_asignatura
        AND ua_profesor.id_usuario = :id_profesor
        AND ua_profesor.rol_asignatura = 'profesor'
        AND ua_profesor.estado = 'activa'
    WHERE ae.id_actividad = :id_actividad
    AND ae.tipo_actividad IN ('tarea', 'practica')
    LIMIT 1
");

$consulta_tarea->execute([
    "id_profesor" => $id_profesor,
    "id_actividad" => $id_actividad
]);

$tarea = $consulta_tarea->fetch();

if (!$tarea) {
    header("Location: asignaturas_profesor.php");
    exit;
}

$id_asignatura = (int) $tarea["id_asignatura"];

// Fin consulta de tarea


// Inicio consulta de entregas

$consulta_entregas = $pdo->prepare("
    SELECT
        u.id_usuario,
        u.nombre,
        u.apellidos,
        u.email,
        e.id_entrega,
        e.fecha_entrega,
        e.estado AS estado_entrega,
        c.id_calificacion,
        c.nota
    FROM usuarios_asignaturas ua
    INNER JOIN usuarios u
        ON u.id_usuario = ua.id_usuario
    LEFT JOIN entregas e
        ON e.id_alumno = u.id_usuario
        AND e.id_actividad = :id_actividad_entrega
    LEFT JOIN calificaciones c
        ON c.id_alumno = u.id_usuario
        AND c.id_actividad = :id_actividad_calificacion
    WHERE ua.id_asignatura = :id_asignatura
    AND ua.rol_asignatura = 'alumno'
    AND ua.estado = 'activa'
    ORDER BY u.nombre ASC, u.apellidos ASC
");

$consulta_entregas->execute([
    "id_actividad_entrega" => $id_actividad,
    "id_actividad_calificacion" => $id_actividad,
    "id_asignatura" => $id_asignatura
]);

$entregas_alumnos = $consulta_entregas->fetchAll();

$total_alumnos = count($entregas_alumnos);
$total_entregadas = 0;
$total_pendientes_revision = 0;
$total_calificadas = 0;

foreach ($entregas_alumnos as $entrega_alumno) {
    if ($entrega_alumno["id_entrega"] !== null) {
        $total_entregadas++;
    }

    if ($entrega_alumno["id_entrega"] !== null && $entrega_alumno["id_calificacion"] === null) {
        $total_pendientes_revision++;
    }

    if ($entrega_alumno["id_calificacion"] !== null) {
        $total_calificadas++;
    }
}

// Fin consulta de entregas


// Inicio enlaces de navegación

$url_listado_tareas = "listado_tareas_profesor.php?id_asignatura=" . $id_asignatura;
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

    <title><?php echo limpiar_texto_doa($tarea["titulo"]); ?> | Profesor DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/listado_tareas.css" rel="stylesheet">
    <link href="css/listado_tareas_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-detalle-tarea-profesor">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido principal -->

        <main class="contenido-doa">
            <section class="detalle-asignatura-principal">
                <!-- Inicio cabecera de asignatura -->

                <div class="cabecera-detalle-asignatura">
                    <div class="cabecera-detalle-asignatura__texto">
                        <a class="enlace-volver-asignaturas" href="<?php echo limpiar_texto_doa($url_listado_tareas); ?>">
                            <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
                                <img alt="" src="img/iconos/grey-chevron-right.svg">
                            </span>

                            <span>Volver a tareas</span>
                        </a>

                        <h1><?php echo limpiar_texto_doa($tarea["asignatura_nombre"]); ?></h1>

                        <ul class="metadatos-asignatura">
                            <li>
                                <img alt="" src="img/iconos/grey-graduation-cap.svg">
                                <span>
                                    <?php echo limpiar_texto_doa($tarea["curso"]); ?>
                                    · Grupo <?php echo limpiar_texto_doa($tarea["grupo"]); ?>
                                </span>
                            </li>

                            <li>
                                <img alt="" src="img/iconos/grey-notebook.svg">
                                <span><?php echo limpiar_texto_doa($tarea["codigo"]); ?></span>
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


                <!-- Inicio detalle de tarea -->

                <section class="cabecera-tareas-profesor">
                    <div>
                        <h2><?php echo limpiar_texto_doa($tarea["titulo"]); ?></h2>

                        <p>
                            <?php echo limpiar_texto_doa($tarea["descripcion"]); ?>
                        </p>
                    </div>

                    <a class="boton-crear-tarea-profesor" href="crear_tarea.php?id_asignatura=<?php echo $id_asignatura; ?>">
                        Crear otra tarea
                    </a>
                </section>

                <section class="resumen-tareas-profesor" aria-label="Resumen de entregas">
                    <article class="tarjeta-resumen-tarea tarjeta-resumen-tarea--principal">
                        <span>Alumnos</span>
                        <strong><?php echo $total_alumnos; ?></strong>
                    </article>

                    <article class="tarjeta-resumen-tarea">
                        <span>Entregadas</span>
                        <strong><?php echo $total_entregadas; ?></strong>
                    </article>

                    <article class="tarjeta-resumen-tarea">
                        <span>Pendientes de revisar</span>
                        <strong><?php echo $total_pendientes_revision; ?></strong>
                    </article>

                    <article class="tarjeta-resumen-tarea">
                        <span>Calificadas</span>
                        <strong><?php echo $total_calificadas; ?></strong>
                    </article>
                </section>

                <!-- Fin detalle de tarea -->


                <!-- Inicio tabla de entregas -->

                <section class="bloque-listado-tareas">
                    <div class="cabecera-listado-tareas">
                        <h2>Entregas del alumnado</h2>
                    </div>

                    <div class="tabla-tareas tabla-tareas-profesor">
                        <div class="tabla-tareas__cabecera">
                            <p>Alumno</p>
                            <p>Fecha de entrega</p>
                            <p>Estado</p>
                            <p>Calificación</p>
                            <p>Acción</p>
                        </div>

                        <div>
                            <?php if (count($entregas_alumnos) === 0) { ?>
                                <p class="mensaje-tabla-vacia">
                                    No hay alumnos asignados a esta asignatura.
                                </p>
                            <?php } ?>

                            <?php foreach ($entregas_alumnos as $entrega_alumno) { ?>
                                <?php
                                $nombre_alumno = trim($entrega_alumno["nombre"] . " " . $entrega_alumno["apellidos"]);
                                $estado_entrega = obtener_estado_entrega_profesor($entrega_alumno["estado_entrega"], $entrega_alumno["id_calificacion"]);
                                $texto_estado = obtener_texto_estado_entrega_profesor($estado_entrega);
                                $fecha_entrega = formatear_fecha_profesor($entrega_alumno["fecha_entrega"]);
                                $nota = $entrega_alumno["nota"] !== null ? number_format((float) $entrega_alumno["nota"], 1) . "/10" : "-";
                                ?>

                                <article class="fila-tarea fila-tarea-profesor">
                                    <div class="fila-tarea__nombre">
                                        <?php echo limpiar_texto_doa($nombre_alumno); ?>
                                    </div>

                                    <p>
                                        <strong><?php echo limpiar_texto_doa($fecha_entrega); ?></strong>
                                    </p>

                                    <p>
                                        <span class="etiqueta-estado etiqueta-estado--<?php echo limpiar_texto_doa($estado_entrega); ?>">
                                            <?php echo limpiar_texto_doa($texto_estado); ?>
                                        </span>
                                    </p>

                                    <p class="fila-tarea__nota">
                                        <?php echo limpiar_texto_doa($nota); ?>
                                    </p>

                                    <?php if ($entrega_alumno["id_entrega"] !== null) { ?>
                                        <a class="boton-editar-tarea" href="detalle_tarea_entregada.php?id_entrega=<?php echo (int) $entrega_alumno["id_entrega"]; ?>">
                                            Revisar
                                        </a>
                                    <?php } else { ?>
                                        <span class="boton-editar-tarea boton-editar-tarea--desactivado">
                                            Sin entrega
                                        </span>
                                    <?php } ?>
                                </article>
                            <?php } ?>
                        </div>
                    </div>
                </section>

                <!-- Fin tabla de entregas -->
            </section>
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>
</html>