<?php
$rol_pagina = "secretaria";
$pagina_activa = "panel";
$enlace_panel = "panel_secretaria.php";
$placeholder_buscador = "Buscar asignatura, profesor, alumno...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

$consulta_resumen = $pdo->query("
    SELECT
        (SELECT COUNT(*) FROM asignaturas) AS total_asignaturas,

        (
            SELECT COUNT(DISTINCT id_usuario)
            FROM usuarios_asignaturas
            WHERE rol_asignatura = 'profesor'
            AND estado = 'activa'
        ) AS total_profesores_asignados,

        (
            SELECT COUNT(DISTINCT id_usuario)
            FROM usuarios_asignaturas
            WHERE rol_asignatura = 'alumno'
            AND estado = 'activa'
        ) AS total_alumnos_matriculados,

        (
            SELECT COUNT(*)
            FROM asignaturas a
            WHERE NOT EXISTS (
                SELECT 1
                FROM usuarios_asignaturas ua_profesor
                WHERE ua_profesor.id_asignatura = a.id_asignatura
                AND ua_profesor.rol_asignatura = 'profesor'
                AND ua_profesor.estado = 'activa'
            )
            OR NOT EXISTS (
                SELECT 1
                FROM usuarios_asignaturas ua_alumno
                WHERE ua_alumno.id_asignatura = a.id_asignatura
                AND ua_alumno.rol_asignatura = 'alumno'
                AND ua_alumno.estado = 'activa'
            )
        ) AS total_pendientes
");

$resumen = $consulta_resumen->fetch();

$consulta_ultimas_asignaturas = $pdo->query("
    SELECT
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.grupo,
        GROUP_CONCAT(
            DISTINCT CONCAT(u_profesor.nombre, ' ', u_profesor.apellidos)
            SEPARATOR ', '
        ) AS profesores,
        COUNT(DISTINCT ua_alumno.id_usuario) AS total_alumnos
    FROM asignaturas a
    LEFT JOIN usuarios_asignaturas ua_profesor
        ON ua_profesor.id_asignatura = a.id_asignatura
        AND ua_profesor.rol_asignatura = 'profesor'
        AND ua_profesor.estado = 'activa'
    LEFT JOIN usuarios u_profesor
        ON u_profesor.id_usuario = ua_profesor.id_usuario
    LEFT JOIN usuarios_asignaturas ua_alumno
        ON ua_alumno.id_asignatura = a.id_asignatura
        AND ua_alumno.rol_asignatura = 'alumno'
        AND ua_alumno.estado = 'activa'
    GROUP BY
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.grupo,
        a.fecha_creacion
    ORDER BY a.fecha_creacion DESC
    LIMIT 3
");

$ultimas_asignaturas = $consulta_ultimas_asignaturas->fetchAll();

$consulta_asignaturas_pendientes = $pdo->query("
    SELECT
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.grupo,
        COUNT(DISTINCT CASE WHEN ua.rol_asignatura = 'profesor' THEN ua.id_usuario END) AS total_profesores,
        COUNT(DISTINCT CASE WHEN ua.rol_asignatura = 'alumno' THEN ua.id_usuario END) AS total_alumnos
    FROM asignaturas a
    LEFT JOIN usuarios_asignaturas ua
        ON ua.id_asignatura = a.id_asignatura
        AND ua.estado = 'activa'
    GROUP BY
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.grupo,
        a.fecha_creacion
    HAVING total_profesores = 0 OR total_alumnos = 0
    ORDER BY a.fecha_creacion DESC
    LIMIT 3
");

$asignaturas_pendientes = $consulta_asignaturas_pendientes->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Panel de Secretaría | DOA</title>

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
            <section class="cabecera-secretaria">
                <div class="cabecera-secretaria__texto">
                    <p class="cabecera-secretaria__eyebrow">Panel de Secretaría</p>

                    <h1>
                        Buenos días, <?php echo limpiar_texto_doa($_SESSION["doa_nombre"]); ?>
                    </h1>

                    <p>
                        Gestiona las asignaturas del centro y asigna profesores y alumnos
                        a los grupos correspondientes.
                    </p>
                </div>
            </section>

            <section class="resumen-metricas resumen-metricas--compacto" aria-label="Resumen de Secretaría">
                <article class="tarjeta-metrica tarjeta-metrica--principal">
                    <span>Asignaturas creadas</span>
                    <strong>
                        <?php echo (int) $resumen["total_asignaturas"]; ?>
                    </strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Profesores asignados</span>
                    <strong>
                        <?php echo (int) $resumen["total_profesores_asignados"]; ?>
                    </strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Alumnos matriculados</span>
                    <strong>
                        <?php echo (int) $resumen["total_alumnos_matriculados"]; ?>
                    </strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Asignaciones pendientes</span>
                    <strong>
                        <?php echo (int) $resumen["total_pendientes"]; ?>
                    </strong>
                </article>
            </section>

            <div class="grid-secretaria">
                <section class="zona-principal-secretaria">
                    <article class="bloque-secretaria">
                        <div class="bloque-secretaria__cabecera">
                            <div>
                                <h2>Gestión rápida</h2>
                                <p>Accede a las acciones principales de Secretaría.</p>
                            </div>
                        </div>

                        <div class="acciones-secretaria">
                            <a class="tarjeta-accion-secretaria" href="crear_asignatura.php">
                                <span class="tarjeta-accion-secretaria__icono" aria-hidden="true">
                                    <img src="img/iconos/blue-notebook.svg" alt="">
                                </span>

                                <span class="tarjeta-accion-secretaria__texto">
                                    <strong>Crear asignatura</strong>
                                    <small>Alta de una nueva asignatura o grupo.</small>
                                </span>
                            </a>

                            <a class="tarjeta-accion-secretaria" href="asignaciones_secretaria.php">
                                <span class="tarjeta-accion-secretaria__icono" aria-hidden="true">
                                    <img src="img/iconos/blue-user.svg" alt="">
                                </span>

                                <span class="tarjeta-accion-secretaria__texto">
                                    <strong>Asignar usuarios</strong>
                                    <small>Vincular profesores y alumnos a una asignatura.</small>
                                </span>
                            </a>
                        </div>
                    </article>

                    <article class="bloque-secretaria">
                        <div class="bloque-secretaria__cabecera">
                            <div>
                                <h2>Últimas asignaturas creadas</h2>
                                <p>Revisión rápida de las asignaturas añadidas recientemente.</p>
                            </div>

                            <a class="bloque-secretaria__enlace" href="asignaturas_secretaria.php">
                                Ver asignaturas
                            </a>
                        </div>

                        <div class="tabla-simple-secretaria">
                            <div class="tabla-simple-secretaria__cabecera">
                                <span>Asignatura</span>
                                <span>Código</span>
                                <span>Profesor</span>
                                <span>Alumnos</span>
                            </div>

                            <?php if (count($ultimas_asignaturas) === 0) { ?>
                                <a class="tabla-simple-secretaria__fila" href="crear_asignatura.php">
                                    <span>
                                        <strong>No hay asignaturas creadas</strong>
                                        <small>Crea la primera asignatura</small>
                                    </span>
                                    <span>-</span>
                                    <span>-</span>
                                    <span>0</span>
                                </a>
                            <?php } ?>

                            <?php foreach ($ultimas_asignaturas as $asignatura) { ?>
                                <a class="tabla-simple-secretaria__fila" href="asignaturas_secretaria.php">
                                    <span>
                                        <strong><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></strong>
                                        <small>
                                            Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                                        </small>
                                    </span>

                                    <span><?php echo limpiar_texto_doa($asignatura["codigo"]); ?></span>

                                    <span>
                                        <?php
                                        echo $asignatura["profesores"] !== null
                                            ? limpiar_texto_doa($asignatura["profesores"])
                                            : "Pendiente";
                                        ?>
                                    </span>

                                    <span><?php echo (int) $asignatura["total_alumnos"]; ?></span>
                                </a>
                            <?php } ?>
                        </div>
                    </article>
                </section>

                <aside class="lateral-secretaria">
                    <article class="tarjeta-lateral-secretaria">
                        <h3>Asignaciones pendientes</h3>

                        <div class="lista-lateral-secretaria">
                            <?php if (count($asignaturas_pendientes) === 0) { ?>
                                <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                    <strong>Sin pendientes</strong>
                                    <span>Todas las asignaturas tienen profesor y alumnos asignados.</span>
                                </div>
                            <?php } ?>

                            <?php foreach ($asignaturas_pendientes as $asignatura) { ?>
                                <?php
                                if ((int) $asignatura["total_profesores"] === 0 && (int) $asignatura["total_alumnos"] === 0) {
                                    $motivo_pendiente = "Profesor y alumnos pendientes";
                                } elseif ((int) $asignatura["total_profesores"] === 0) {
                                    $motivo_pendiente = "Profesor pendiente de asignar";
                                } else {
                                    $motivo_pendiente = "Alumnos pendientes de asignar";
                                }
                                ?>

                                <a class="item-lateral-secretaria" href="asignaciones_secretaria.php?id_asignatura=<?php echo (int) $asignatura["id_asignatura"]; ?>">
                                    <strong><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></strong>
                                    <span><?php echo limpiar_texto_doa($motivo_pendiente); ?></span>
                                </a>
                            <?php } ?>
                        </div>
                    </article>

                    <article class="tarjeta-lateral-secretaria">
                        <h3>Estado del sistema</h3>

                        <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                            <strong>Asignaturas creadas</strong>
                            <span>
                                <?php echo (int) $resumen["total_asignaturas"]; ?> registradas en base de datos.
                            </span>
                        </div>

                        <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                            <strong>Profesores asignados</strong>
                            <span>
                                <?php echo (int) $resumen["total_profesores_asignados"]; ?> profesores vinculados a asignaturas.
                            </span>
                        </div>

                        <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                            <strong>Alumnos matriculados</strong>
                            <span>
                                <?php echo (int) $resumen["total_alumnos_matriculados"]; ?> alumnos asignados a grupos.
                            </span>
                        </div>
                    </article>
                </aside>
            </div>
        </main>
    </div>

</body>

</html>