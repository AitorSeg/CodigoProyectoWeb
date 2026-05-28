<?php
// Inicio configuración de página

$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar asignatura, tarea...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

$id_profesor = (int) $_SESSION["doa_id_usuario"];

// Fin configuración de página


// Inicio funciones auxiliares

function obtener_texto_proximo_examen_asignaturas_profesor($fecha)
{
    if ($fecha === null) {
        return "Sin examen";
    }

    return date("d/m", strtotime($fecha));
}

function obtener_texto_ultima_actividad_asignaturas_profesor($tipo, $titulo)
{
    if ($titulo === null || $titulo === "") {
        return "Sin actividad reciente en esta asignatura.";
    }

    $tipo_texto = match ($tipo) {
        "examen" => "Examen",
        "practica" => "Práctica",
        "tarea" => "Tarea",
        default => "Actividad",
    };

    return $tipo_texto . " publicada: “" . $titulo . "”.";
}

// Fin funciones auxiliares


// Inicio resumen general

$consulta_resumen = $pdo->prepare("
    SELECT
        COUNT(DISTINCT a.id_asignatura) AS total_asignaturas,
        COUNT(DISTINCT ua_alumno.id_usuario) AS total_alumnos,
        COUNT(DISTINCT CASE
            WHEN c.id_calificacion IS NULL THEN e.id_entrega
        END) AS total_entregas_pendientes,
        COUNT(DISTINCT CASE
            WHEN ae_examen.tipo_actividad = 'examen'
            AND ae_examen.visible = 1
            AND ae_examen.estado = 'publicada'
            AND (
                ae_examen.fecha_limite IS NULL
                OR ae_examen.fecha_limite >= NOW()
            )
            THEN ae_examen.id_actividad
        END) AS total_proximos_examenes
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
    LEFT JOIN actividades_evaluables ae_entrega
        ON ae_entrega.id_asignatura = a.id_asignatura
        AND ae_entrega.id_profesor = :id_profesor_entregas
        AND ae_entrega.tipo_actividad IN ('tarea', 'practica')
    LEFT JOIN entregas e
        ON e.id_actividad = ae_entrega.id_actividad
        AND e.estado IN ('entregada', 'tardia')
    LEFT JOIN calificaciones c
        ON c.id_actividad = e.id_actividad
        AND c.id_alumno = e.id_alumno
    LEFT JOIN actividades_evaluables ae_examen
        ON ae_examen.id_asignatura = a.id_asignatura
        AND ae_examen.id_profesor = :id_profesor_examenes
    WHERE a.estado = 'activa'
");

$consulta_resumen->execute([
    "id_profesor" => $id_profesor,
    "id_profesor_entregas" => $id_profesor,
    "id_profesor_examenes" => $id_profesor
]);

$resumen = $consulta_resumen->fetch();

$total_asignaturas = (int) $resumen["total_asignaturas"];
$total_alumnos = (int) $resumen["total_alumnos"];
$total_entregas_pendientes = (int) $resumen["total_entregas_pendientes"];
$total_proximos_examenes = (int) $resumen["total_proximos_examenes"];

// Fin resumen general


// Inicio consulta de asignaturas

$consulta_asignaturas = $pdo->prepare("
    SELECT
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.descripcion,
        a.curso,
        a.grupo,
        COUNT(DISTINCT ua_alumno.id_usuario) AS total_alumnos,
        COUNT(DISTINCT CASE
            WHEN ae_tarea.tipo_actividad IN ('tarea', 'practica')
            AND ae_tarea.visible = 1
            AND ae_tarea.estado = 'publicada'
            AND (
                ae_tarea.fecha_limite IS NULL
                OR ae_tarea.fecha_limite >= NOW()
            )
            THEN ae_tarea.id_actividad
        END) AS total_tareas_activas,
        COUNT(DISTINCT CASE
            WHEN c.id_calificacion IS NULL THEN e.id_entrega
        END) AS entregas_pendientes,
        COUNT(DISTINCT r.id_recurso) AS total_recursos,
        MIN(CASE
            WHEN ae_examen.tipo_actividad = 'examen'
            AND ae_examen.visible = 1
            AND ae_examen.estado = 'publicada'
            AND (
                ae_examen.fecha_limite IS NULL
                OR ae_examen.fecha_limite >= NOW()
            )
            THEN ae_examen.fecha_inicio
        END) AS fecha_proximo_examen,
        ultima_actividad.tipo_actividad AS ultima_actividad_tipo,
        ultima_actividad.titulo AS ultima_actividad_titulo
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
    LEFT JOIN actividades_evaluables ae_tarea
        ON ae_tarea.id_asignatura = a.id_asignatura
        AND ae_tarea.id_profesor = :id_profesor_tareas
        AND ae_tarea.tipo_actividad IN ('tarea', 'practica')
    LEFT JOIN entregas e
        ON e.id_actividad = ae_tarea.id_actividad
        AND e.estado IN ('entregada', 'tardia')
    LEFT JOIN calificaciones c
        ON c.id_actividad = e.id_actividad
        AND c.id_alumno = e.id_alumno
    LEFT JOIN recursos r
        ON r.id_asignatura = a.id_asignatura
        AND r.id_profesor = :id_profesor_recursos
        AND r.visible = 1
    LEFT JOIN actividades_evaluables ae_examen
        ON ae_examen.id_asignatura = a.id_asignatura
        AND ae_examen.id_profesor = :id_profesor_examenes
        AND ae_examen.tipo_actividad = 'examen'
    LEFT JOIN actividades_evaluables ultima_actividad
        ON ultima_actividad.id_actividad = (
            SELECT ae_ultima.id_actividad
            FROM actividades_evaluables ae_ultima
            WHERE ae_ultima.id_asignatura = a.id_asignatura
            AND ae_ultima.id_profesor = :id_profesor_ultima
            AND ae_ultima.tipo_actividad IN ('tarea', 'practica', 'examen')
            ORDER BY ae_ultima.fecha_creacion DESC
            LIMIT 1
        )
    WHERE a.estado = 'activa'
    GROUP BY
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.descripcion,
        a.curso,
        a.grupo,
        ultima_actividad.tipo_actividad,
        ultima_actividad.titulo
    ORDER BY entregas_pendientes DESC, a.nombre ASC
");

$consulta_asignaturas->execute([
    "id_profesor" => $id_profesor,
    "id_profesor_tareas" => $id_profesor,
    "id_profesor_recursos" => $id_profesor,
    "id_profesor_examenes" => $id_profesor,
    "id_profesor_ultima" => $id_profesor
]);

$asignaturas_profesor = $consulta_asignaturas->fetchAll();

// Fin consulta de asignaturas
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Mis asignaturas | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/asignaturas_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-asignaturas-profesor">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido principal -->

        <main class="contenido-doa contenido-asignaturas-profesor">
            <!-- Inicio cabecera de página -->

            <section class="cabecera-asignaturas-profesor">
                <div>
                    <p class="cabecera-asignaturas-profesor__eyebrow">Docencia</p>

                    <h1>Mis asignaturas</h1>

                    <p>
                        Consulta el estado general de tus grupos y accede rápidamente
                        a cada asignatura.
                    </p>
                </div>
            </section>

            <!-- Fin cabecera de página -->


            <!-- Inicio resumen general -->

            <section class="resumen-asignaturas-profesor" aria-label="Resumen general del profesor">
                <article class="dato-asignaturas-profesor dato-asignaturas-profesor--principal">
                    <span class="dato-asignaturas-profesor__label">Asignaturas activas</span>
                    <strong class="dato-asignaturas-profesor__valor"><?php echo $total_asignaturas; ?></strong>
                </article>

                <article class="dato-asignaturas-profesor">
                    <span class="dato-asignaturas-profesor__label">Alumnos totales</span>
                    <strong class="dato-asignaturas-profesor__valor"><?php echo $total_alumnos; ?></strong>
                </article>

                <article class="dato-asignaturas-profesor">
                    <span class="dato-asignaturas-profesor__label">Entregas pendientes</span>
                    <strong class="dato-asignaturas-profesor__valor"><?php echo $total_entregas_pendientes; ?></strong>
                </article>

                <article class="dato-asignaturas-profesor">
                    <span class="dato-asignaturas-profesor__label">Próximos exámenes</span>
                    <strong class="dato-asignaturas-profesor__valor"><?php echo $total_proximos_examenes; ?></strong>
                </article>
            </section>

            <!-- Fin resumen general -->


            <!-- Inicio listado de asignaturas -->

            <section class="grid-asignaturas-docente" aria-label="Listado de asignaturas del profesor">
                <?php if (count($asignaturas_profesor) === 0) { ?>
                    <article class="tarjeta-asignatura-docente">
                        <div class="tarjeta-asignatura-docente__cabecera">
                            <div>
                                <h2>Sin asignaturas asignadas</h2>
                                <p class="tarjeta-asignatura-docente__unidad">Pendiente de asignación</p>
                            </div>
                        </div>

                        <div class="tarjeta-asignatura-docente__actividad">
                            <span>Información</span>

                            <p>
                                Secretaría debe asignarte a una asignatura para que puedas gestionarla desde este panel.
                            </p>
                        </div>
                    </article>
                <?php } ?>

                <?php foreach ($asignaturas_profesor as $asignatura) { ?>
                    <?php
                    $id_asignatura = (int) $asignatura["id_asignatura"];

                    $url_detalle = "detalle_asignatura_profesor.php?id_asignatura=" . $id_asignatura;
                    $url_recursos = "recursos_profesor.php?id_asignatura=" . $id_asignatura;
                    $url_tareas = "listado_tareas_profesor.php?id_asignatura=" . $id_asignatura;

                    $texto_proximo_examen = obtener_texto_proximo_examen_asignaturas_profesor($asignatura["fecha_proximo_examen"]);

                    $texto_actividad = obtener_texto_ultima_actividad_asignaturas_profesor(
                        $asignatura["ultima_actividad_tipo"],
                        $asignatura["ultima_actividad_titulo"]
                    );
                    ?>

                    <article class="tarjeta-asignatura-docente">
                        <div class="tarjeta-asignatura-docente__cabecera">
                            <div>
                                <h2><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></h2>

                                <p class="tarjeta-asignatura-docente__unidad">
                                    <?php echo limpiar_texto_doa($asignatura["codigo"]); ?>
                                    · <?php echo limpiar_texto_doa($asignatura["curso"]); ?>
                                    · Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                                </p>
                            </div>
                        </div>

                        <ul class="tarjeta-asignatura-docente__meta">
                            <li><?php echo (int) $asignatura["total_alumnos"]; ?> alumnos</li>
                            <li><?php echo limpiar_texto_doa($asignatura["codigo"]); ?></li>
                            <li>Asignatura activa</li>
                        </ul>

                        <div class="tarjeta-asignatura-docente__stats">
                            <div class="mini-dato-docente">
                                <span>Tareas activas</span>
                                <strong><?php echo (int) $asignatura["total_tareas_activas"]; ?></strong>
                            </div>

                            <div class="mini-dato-docente">
                                <span>Entregas pendientes</span>
                                <strong><?php echo (int) $asignatura["entregas_pendientes"]; ?></strong>
                            </div>

                            <div class="mini-dato-docente">
                                <span>Recursos</span>
                                <strong><?php echo (int) $asignatura["total_recursos"]; ?></strong>
                            </div>

                            <div class="mini-dato-docente">
                                <span>Próximo examen</span>
                                <strong><?php echo limpiar_texto_doa($texto_proximo_examen); ?></strong>
                            </div>
                        </div>

                        <div class="tarjeta-asignatura-docente__actividad">
                            <span>Última actividad</span>

                            <p><?php echo limpiar_texto_doa($texto_actividad); ?></p>
                        </div>

                        <div class="tarjeta-asignatura-docente__acciones">
                            <a class="boton-docente boton-docente--principal" href="<?php echo limpiar_texto_doa($url_detalle); ?>">
                                Entrar
                            </a>

                            <a class="boton-docente" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                                Recursos
                            </a>

                            <a class="boton-docente" href="<?php echo limpiar_texto_doa($url_tareas); ?>">
                                Tareas
                            </a>
                        </div>
                    </article>
                <?php } ?>
            </section>

            <!-- Fin listado de asignaturas -->
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>

</html>