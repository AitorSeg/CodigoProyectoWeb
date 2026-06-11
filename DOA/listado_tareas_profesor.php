<?php
// Inicio configuración de página

$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar tarea...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página


// Inicio parámetros de pantalla

if (!isset($_GET["id_asignatura"])) {
    header("Location: asignaturas_profesor.php");
    exit;
}

$id_profesor = (int) $_SESSION["doa_id_usuario"];
$id_asignatura = (int) $_GET["id_asignatura"];

$filtro_tipo = $_GET["tipo"] ?? "todas";
$filtro_estado = $_GET["estado"] ?? "todos";
$orden_tareas = $_GET["orden"] ?? "fecha_entrega";

if (!in_array($filtro_tipo, ["todas", "tarea", "practica"], true)) {
    $filtro_tipo = "todas";
}

if (!in_array($filtro_estado, ["todos", "publicada", "cerrada", "borrador"], true)) {
    $filtro_estado = "todos";
}

if (!in_array($orden_tareas, ["fecha_entrega", "nombre", "pendientes"], true)) {
    $orden_tareas = "fecha_entrega";
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

$total_alumnos = (int) $asignatura["total_alumnos"];

// Fin consulta de asignatura


// Inicio consulta de resumen

$consulta_resumen = $pdo->prepare("
    SELECT
        COUNT(DISTINCT CASE
            WHEN ae.estado = 'publicada' THEN ae.id_actividad
        END) AS total_tareas_activas,

        COUNT(DISTINCT e.id_entrega) AS total_entregas_recibidas,

        COUNT(DISTINCT CASE
            WHEN e.estado IN ('entregada', 'tardia', 'revisada')
            AND c.id_calificacion IS NULL
            THEN e.id_entrega
        END) AS total_pendientes_revision,

        COUNT(DISTINCT CASE
            WHEN ae.estado = 'cerrada' THEN ae.id_actividad
        END) AS total_tareas_cerradas
    FROM actividades_evaluables ae
    LEFT JOIN entregas e
        ON e.id_actividad = ae.id_actividad
    LEFT JOIN calificaciones c
        ON c.id_actividad = e.id_actividad
        AND c.id_alumno = e.id_alumno
    WHERE ae.id_asignatura = :id_asignatura
    AND ae.id_profesor = :id_profesor
    AND ae.tipo_actividad IN ('tarea', 'practica')
");

$consulta_resumen->execute([
    "id_asignatura" => $id_asignatura,
    "id_profesor" => $id_profesor
]);

$resumen = $consulta_resumen->fetch();

$total_tareas_activas = (int) $resumen["total_tareas_activas"];
$total_entregas_recibidas = (int) $resumen["total_entregas_recibidas"];
$total_pendientes_revision = (int) $resumen["total_pendientes_revision"];
$total_tareas_cerradas = (int) $resumen["total_tareas_cerradas"];

// Fin consulta de resumen


// Inicio consulta de tareas

$condiciones_tareas = [
    "ae.id_asignatura = :id_asignatura",
    "ae.id_profesor = :id_profesor",
    "ae.tipo_actividad IN ('tarea', 'practica')"
];

$parametros_tareas = [
    "id_asignatura" => $id_asignatura,
    "id_profesor" => $id_profesor
];

if ($filtro_tipo !== "todas") {
    $condiciones_tareas[] = "ae.tipo_actividad = :tipo_actividad";
    $parametros_tareas["tipo_actividad"] = $filtro_tipo;
}

if ($filtro_estado !== "todos") {
    $condiciones_tareas[] = "ae.estado = :estado";
    $parametros_tareas["estado"] = $filtro_estado;
}

$orden_sql = match ($orden_tareas) {
    "nombre" => "ae.titulo ASC",
    "pendientes" => "pendientes_revision DESC, CASE WHEN ae.fecha_limite IS NULL THEN 1 ELSE 0 END, ae.fecha_limite ASC",
    default => "CASE WHEN ae.fecha_limite IS NULL THEN 1 ELSE 0 END, ae.fecha_limite ASC",
};

$consulta_tareas = $pdo->prepare(
    "
    SELECT
        ae.id_actividad,
        ae.tipo_actividad,
        ae.unidad,
        ae.titulo,
        ae.fecha_limite,
        ae.visible,
        ae.estado,

        COUNT(DISTINCT e.id_entrega) AS total_entregas,

        COUNT(DISTINCT CASE
            WHEN e.estado IN ('entregada', 'tardia', 'revisada')
            AND c.id_calificacion IS NULL
            THEN e.id_entrega
        END) AS pendientes_revision
    FROM actividades_evaluables ae
    LEFT JOIN entregas e
        ON e.id_actividad = ae.id_actividad
    LEFT JOIN calificaciones c
        ON c.id_actividad = e.id_actividad
        AND c.id_alumno = e.id_alumno
    WHERE " . implode(" AND ", $condiciones_tareas) . "
    GROUP BY
        ae.id_actividad,
        ae.tipo_actividad,
        ae.unidad,
        ae.titulo,
        ae.fecha_limite,
        ae.visible,
        ae.estado
    ORDER BY " . $orden_sql
);

$consulta_tareas->execute($parametros_tareas);

$tareas = $consulta_tareas->fetchAll();

// Fin consulta de tareas


// Inicio enlaces de navegación

$url_detalle = "detalle_asignatura_profesor.php?id_asignatura=" . $id_asignatura;
$url_recursos = "recursos_profesor.php?id_asignatura=" . $id_asignatura;
$url_tareas = "listado_tareas_profesor.php?id_asignatura=" . $id_asignatura;
$url_crear_tarea = "crear_tarea.php?id_asignatura=" . $id_asignatura;
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

    <title>Tareas · <?php echo limpiar_texto_doa($asignatura["nombre"]); ?> | DOA</title>

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

<body class="pagina-doa pagina-listado-tareas pagina-listado-tareas-profesor">
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
                                <span><?php echo $total_alumnos; ?> alumnos</span>
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


                <!-- Inicio acciones y resumen -->

                <section class="cabecera-tareas-profesor">
                    <div>
                        <h2>Tareas de la asignatura</h2>

                        <p>
                            Gestiona las tareas publicadas, revisa entregas y accede al detalle de cada actividad.
                        </p>
                    </div>

                    <a class="boton-crear-tarea-profesor" href="<?php echo limpiar_texto_doa($url_crear_tarea); ?>">
                        Crear tarea
                    </a>
                </section>

                <section class="resumen-docente" aria-label="Resumen de tareas">
                    <article class="tarjeta-resumen-docente tarjeta-resumen-docente--principal">
                        <span>Tareas activas</span>
                        <strong><?php echo $total_tareas_activas; ?></strong>
                    </article>

                    <article class="tarjeta-resumen-docente">
                        <span>Entregas recibidas</span>
                        <strong><?php echo $total_entregas_recibidas; ?></strong>
                    </article>

                    <article class="tarjeta-resumen-docente">
                        <span>Pendientes de revisar</span>
                        <strong><?php echo $total_pendientes_revision; ?></strong>
                    </article>

                    <article class="tarjeta-resumen-docente">
                        <span>Tareas cerradas</span>
                        <strong><?php echo $total_tareas_cerradas; ?></strong>
                    </article>
                </section>

                <!-- Fin acciones y resumen -->


                <!-- Inicio listado de tareas -->

                <section class="bloque-listado-tareas">
                    <div class="cabecera-listado-tareas">
                        <h2>Listado de tareas</h2>

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

                                    <option value="publicada" <?php echo $filtro_estado === "publicada" ? "selected" : ""; ?>>
                                        Estado: publicadas
                                    </option>

                                    <option value="cerrada" <?php echo $filtro_estado === "cerrada" ? "selected" : ""; ?>>
                                        Estado: cerradas
                                    </option>

                                    <option value="borrador" <?php echo $filtro_estado === "borrador" ? "selected" : ""; ?>>
                                        Estado: borradores
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

                                    <option value="pendientes" <?php echo $orden_tareas === "pendientes" ? "selected" : ""; ?>>
                                        Ordenar por pendientes
                                    </option>
                                </select>
                            </label>

                            <button class="btn btn-primary boton-filtro-aplicar" type="submit">
                                Aplicar
                            </button>
                        </form>
                    </div>

                    <div class="tabla-tareas tabla-tareas-profesor">
                        <div class="tabla-tareas__cabecera">
                            <p>Tarea</p>
                            <p>Fecha de entrega</p>
                            <p>Entregas</p>
                            <p>Pendientes</p>
                            <p>Estado</p>
                            <p>Acción</p>
                        </div>

                        <div>
                            <?php if (count($tareas) === 0) { ?>
                                <p class="mensaje-tabla-vacia">
                                    No hay tareas que coincidan con los filtros seleccionados.
                                </p>
                            <?php } ?>

                            <?php foreach ($tareas as $tarea) { ?>
                                <?php
                                $fecha_entrega = $tarea["fecha_limite"] !== null
                                    ? date("d/m/Y", strtotime($tarea["fecha_limite"]))
                                    : "-";

                                $total_entregas_tarea = (int) $tarea["total_entregas"];
                                $pendientes_revision_tarea = (int) $tarea["pendientes_revision"];
                                $texto_estado = ucfirst($tarea["estado"]);

                                $url_detalle_tarea = "detalle_tarea_profesor.php?id_actividad=" . (int) $tarea["id_actividad"];
                                ?>

                                <article class="fila-tarea fila-tarea-profesor">
                                    <a class="fila-tarea__nombre" href="<?php echo limpiar_texto_doa($url_detalle_tarea); ?>">
                                        <?php echo limpiar_texto_doa($tarea["titulo"]); ?>
                                    </a>

                                    <p>
                                        <strong><?php echo limpiar_texto_doa($fecha_entrega); ?></strong>
                                    </p>

                                    <p>
                                        <strong>
                                            <?php echo $total_entregas_tarea; ?>/<?php echo $total_alumnos; ?>
                                        </strong>
                                    </p>

                                    <p>
                                        <strong><?php echo $pendientes_revision_tarea; ?></strong>
                                    </p>

                                    <p>
                                        <span class="etiqueta-estado etiqueta-estado--<?php echo limpiar_texto_doa($tarea["estado"]); ?>">
                                            <?php echo limpiar_texto_doa($texto_estado); ?>
                                        </span>
                                    </p>

                                    <a class="boton-editar-tarea" href="<?php echo limpiar_texto_doa($url_detalle_tarea); ?>">
                                        Ver
                                    </a>
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