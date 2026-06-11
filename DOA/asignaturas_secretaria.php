<?php
$rol_pagina = "secretaria";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_secretaria.php";
$placeholder_buscador = "Buscar asignatura, profesor, alumno...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

$consulta_asignaturas = $pdo->query("
    SELECT
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.curso,
        a.grupo,
        a.estado,
        COUNT(DISTINCT ua_alumno.id_usuario) AS total_alumnos,
        GROUP_CONCAT(
            DISTINCT CONCAT(u_profesor.nombre, ' ', u_profesor.apellidos)
            SEPARATOR ', '
        ) AS profesores
    FROM asignaturas a
    LEFT JOIN usuarios_asignaturas ua_alumno
        ON ua_alumno.id_asignatura = a.id_asignatura
        AND ua_alumno.rol_asignatura = 'alumno'
        AND ua_alumno.estado = 'activa'
    LEFT JOIN usuarios_asignaturas ua_profesor
        ON ua_profesor.id_asignatura = a.id_asignatura
        AND ua_profesor.rol_asignatura = 'profesor'
        AND ua_profesor.estado = 'activa'
    LEFT JOIN usuarios u_profesor
        ON u_profesor.id_usuario = ua_profesor.id_usuario
    GROUP BY
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.curso,
        a.grupo,
        a.estado
    ORDER BY a.fecha_creacion DESC
");

$asignaturas = $consulta_asignaturas->fetchAll();

$total_asignaturas_activas = 0;
$total_con_profesor = 0;
$total_sin_profesor = 0;
$total_alumnos_asignados = 0;

foreach ($asignaturas as $asignatura) {
    if ($asignatura["estado"] === "activa") {
        $total_asignaturas_activas++;
    }

    if ($asignatura["profesores"] !== null) {
        $total_con_profesor++;
    } else {
        $total_sin_profesor++;
    }

    $total_alumnos_asignados += (int) $asignatura["total_alumnos"];
}

$mensaje_ok = "";

if (isset($_GET["creada"]) && $_GET["creada"] === "ok") {
    $mensaje_ok = "Asignatura creada correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Asignaturas | Secretaría DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/secretaria.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-secretaria">
    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa contenido-secretaria">
            <section class="cabecera-secretaria cabecera-secretaria--con-accion">
                <div class="cabecera-secretaria__texto">
                    <p class="cabecera-secretaria__eyebrow">Gestión académica</p>

                    <h1>Asignaturas</h1>

                    <p>
                        Consulta las asignaturas creadas, revisa sus datos principales
                        y accede a sus asignaciones.
                    </p>
                </div>

                <a class="boton-secretaria boton-secretaria--principal" href="crear_asignatura.php">
                    Crear asignatura
                </a>
            </section>

            <?php if ($mensaje_ok !== "") { ?>
                <p class="mensaje-formulario-secretaria">
                    <?php echo limpiar_texto_doa($mensaje_ok); ?>
                </p>
            <?php } ?>

            <section aria-label="Resumen de asignaturas" class="resumen-metricas resumen-metricas--compacto">
                <article class="tarjeta-metrica tarjeta-metrica--principal">
                    <span>Asignaturas activas</span>
                    <strong><?php echo $total_asignaturas_activas; ?></strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Con profesor</span>
                    <strong><?php echo $total_con_profesor; ?></strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Sin profesor</span>
                    <strong><?php echo $total_sin_profesor; ?></strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Alumnos asignados</span>
                    <strong><?php echo $total_alumnos_asignados; ?></strong>
                </article>
            </section>

            <section class="bloque-secretaria">
                <div class="bloque-secretaria__cabecera">
                    <div>
                        <h2>Listado de asignaturas</h2>

                        <p>
                            Vista administrativa de las asignaturas disponibles en la demo.
                        </p>
                    </div>
                </div>

                <div class="tabla-asignaturas-secretaria">
                    <div class="tabla-asignaturas-secretaria__cabecera">
                        <span>Asignatura</span>
                        <span>Código</span>
                        <span>Curso</span>
                        <span>Profesor</span>
                        <span>Alumnos</span>
                        <span>Estado</span>
                        <span>Acciones</span>
                    </div>

                    <?php if (count($asignaturas) === 0) { ?>
                        <article class="tabla-asignaturas-secretaria__fila">
                            <div class="asignatura-secretaria-nombre">
                                <strong>No hay asignaturas creadas</strong>
                                <small>Crea la primera asignatura desde el botón superior.</small>
                            </div>

                            <span>-</span>
                            <span>-</span>
                            <span>-</span>
                            <span>0</span>
                            <span>
                                <span class="estado-secretaria estado-secretaria--pendiente">
                                    Pendiente
                                </span>
                            </span>
                            <span>-</span>
                        </article>
                    <?php } ?>

                    <?php foreach ($asignaturas as $asignatura) { ?>
                        <?php
                        $profesores = $asignatura["profesores"] !== null ? $asignatura["profesores"] : "Pendiente";

                        $asignatura_completa = $asignatura["profesores"] !== null && (int) $asignatura["total_alumnos"] > 0;

                        $clase_estado = $asignatura_completa
                            ? "estado-secretaria--completa"
                            : "estado-secretaria--pendiente";

                        $texto_estado = $asignatura_completa
                            ? "Completa"
                            : "Pendiente";
                        ?>

                        <article class="tabla-asignaturas-secretaria__fila">
                            <div class="asignatura-secretaria-nombre">
                                <strong><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></strong>
                                <small>
                                    Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?> ·
                                    <?php echo limpiar_texto_doa($asignatura["estado"]); ?>
                                </small>
                            </div>

                            <span><?php echo limpiar_texto_doa($asignatura["codigo"]); ?></span>
                            <span><?php echo limpiar_texto_doa($asignatura["curso"]); ?></span>
                            <span><?php echo limpiar_texto_doa($profesores); ?></span>
                            <span><?php echo (int) $asignatura["total_alumnos"]; ?></span>

                            <span>
                                <span class="estado-secretaria <?php echo limpiar_texto_doa($clase_estado); ?>">
                                    <?php echo limpiar_texto_doa($texto_estado); ?>
                                </span>
                            </span>

                            <div class="acciones-fila-secretaria">
                                <a class="enlace-accion-secretaria" href="asignaciones_secretaria.php?id_asignatura=<?php echo (int) $asignatura["id_asignatura"]; ?>">
                                    Asignaciones
                                </a>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            </section>
        </main>
    </div>

</body>

</html>