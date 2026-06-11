<?php
$rol_pagina = "secretaria";
$pagina_activa = "asignaciones";
$enlace_panel = "panel_secretaria.php";
$placeholder_buscador = "Buscar asignatura, profesor, alumno...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

$errores = [];
$mensaje_ok = "";

if (isset($_GET["guardado"]) && $_GET["guardado"] === "ok") {
    $mensaje_ok = "Asignaciones guardadas correctamente.";
}

$consulta_asignaturas = $pdo->query("
    SELECT id_asignatura, nombre, codigo, curso, grupo, estado
    FROM asignaturas
    ORDER BY nombre ASC, grupo ASC
");

$asignaturas = $consulta_asignaturas->fetchAll();

$consulta_profesores = $pdo->query("
    SELECT id_usuario, nombre, apellidos, email, tipo_usuario
    FROM usuarios
    WHERE rol = 'profesor'
    AND estado = 'activo'
    ORDER BY nombre ASC, apellidos ASC
");

$profesores = $consulta_profesores->fetchAll();

$consulta_alumnos = $pdo->query("
    SELECT id_usuario, nombre, apellidos, email, dni
    FROM usuarios
    WHERE rol = 'alumno'
    AND tipo_usuario = 'demo'
    AND estado = 'activo'
    ORDER BY nombre ASC, apellidos ASC
");

$alumnos = $consulta_alumnos->fetchAll();

$id_asignatura_seleccionada = 0;

if (isset($_GET["id_asignatura"])) {
    $id_asignatura_seleccionada = (int) $_GET["id_asignatura"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_asignatura_seleccionada = (int) $_POST["id_asignatura"];
}

if ($id_asignatura_seleccionada === 0 && count($asignaturas) > 0) {
    $id_asignatura_seleccionada = (int) $asignaturas[0]["id_asignatura"];
}

$asignatura_seleccionada = null;

foreach ($asignaturas as $asignatura) {
    if ((int) $asignatura["id_asignatura"] === $id_asignatura_seleccionada) {
        $asignatura_seleccionada = $asignatura;
    }
}

if ($asignatura_seleccionada === null && count($asignaturas) > 0) {
    $asignatura_seleccionada = $asignaturas[0];
    $id_asignatura_seleccionada = (int) $asignatura_seleccionada["id_asignatura"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_profesor = $_POST["id_profesor"] !== "" ? (int) $_POST["id_profesor"] : null;
    $ids_alumnos = isset($_POST["ids_alumnos"]) ? array_map("intval", $_POST["ids_alumnos"]) : [];

    $ids_profesores_validos = array_map("intval", array_column($profesores, "id_usuario"));
    $ids_alumnos_validos = array_map("intval", array_column($alumnos, "id_usuario"));

    if ($asignatura_seleccionada === null) {
        $errores[] = "No hay una asignatura válida seleccionada.";
    }

    if ($id_profesor !== null && !in_array($id_profesor, $ids_profesores_validos, true)) {
        $errores[] = "El profesor seleccionado no es válido.";
    }

    foreach ($ids_alumnos as $id_alumno) {
        if (!in_array($id_alumno, $ids_alumnos_validos, true)) {
            $errores[] = "Hay un alumno seleccionado que no es válido.";
        }
    }

    if (count($errores) === 0) {
        $estado_asignatura = $id_profesor !== null && count($ids_alumnos) > 0 ? "activa" : "pendiente";

        $pdo->beginTransaction();

        try {
            $eliminar_asignaciones = $pdo->prepare("
                DELETE FROM usuarios_asignaturas
                WHERE id_asignatura = :id_asignatura
            ");

            $eliminar_asignaciones->execute([
                "id_asignatura" => $id_asignatura_seleccionada
            ]);

            $insertar_asignacion = $pdo->prepare("
                INSERT INTO usuarios_asignaturas
                    (id_usuario, id_asignatura, rol_asignatura, estado)
                VALUES
                    (:id_usuario, :id_asignatura, :rol_asignatura, 'activa')
            ");

            if ($id_profesor !== null) {
                $insertar_asignacion->execute([
                    "id_usuario" => $id_profesor,
                    "id_asignatura" => $id_asignatura_seleccionada,
                    "rol_asignatura" => "profesor"
                ]);
            }

            foreach ($ids_alumnos as $id_alumno) {
                $insertar_asignacion->execute([
                    "id_usuario" => $id_alumno,
                    "id_asignatura" => $id_asignatura_seleccionada,
                    "rol_asignatura" => "alumno"
                ]);
            }

            $actualizar_asignatura = $pdo->prepare("
                UPDATE asignaturas
                SET estado = :estado
                WHERE id_asignatura = :id_asignatura
            ");

            $actualizar_asignatura->execute([
                "estado" => $estado_asignatura,
                "id_asignatura" => $id_asignatura_seleccionada
            ]);

            $pdo->commit();

            header("Location: asignaciones_secretaria.php?id_asignatura=" . $id_asignatura_seleccionada . "&guardado=ok");
            exit;
        } catch (Throwable $error) {
            $pdo->rollBack();
            throw $error;
        }
    }
}

$id_profesor_asignado = "";
$ids_alumnos_asignados = [];

if ($asignatura_seleccionada !== null) {
    $consulta_asignaciones_actuales = $pdo->prepare("
        SELECT id_usuario, rol_asignatura
        FROM usuarios_asignaturas
        WHERE id_asignatura = :id_asignatura
        AND estado = 'activa'
    ");

    $consulta_asignaciones_actuales->execute([
        "id_asignatura" => $id_asignatura_seleccionada
    ]);

    $asignaciones_actuales = $consulta_asignaciones_actuales->fetchAll();

    foreach ($asignaciones_actuales as $asignacion_actual) {
        if ($asignacion_actual["rol_asignatura"] === "profesor") {
            $id_profesor_asignado = (string) $asignacion_actual["id_usuario"];
        }

        if ($asignacion_actual["rol_asignatura"] === "alumno") {
            $ids_alumnos_asignados[] = (int) $asignacion_actual["id_usuario"];
        }
    }
}

$consulta_pendientes = $pdo->query("
    SELECT COUNT(*) AS total
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
");

$total_pendientes = (int) $consulta_pendientes->fetch()["total"];

$nombre_profesor_asignado = "Pendiente";

foreach ($profesores as $profesor) {
    if ((string) $profesor["id_usuario"] === $id_profesor_asignado) {
        $nombre_profesor_asignado = trim($profesor["nombre"] . " " . $profesor["apellidos"]);
    }
}

$total_alumnos_asignados = count($ids_alumnos_asignados);

$asignacion_completa = $id_profesor_asignado !== "" && $total_alumnos_asignados > 0;
$clase_estado_resumen = $asignacion_completa ? "estado-secretaria--completa" : "estado-secretaria--pendiente";
$texto_estado_resumen = $asignacion_completa ? "Completa" : "Pendiente";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Asignaciones | Secretaría DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/secretaria.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-secretaria pagina-asignaciones-secretaria">
    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa contenido-secretaria">
            <section class="cabecera-secretaria">
                <div class="cabecera-secretaria__texto">
                    <p class="cabecera-secretaria__eyebrow">Gestión académica</p>

                    <h1>Asignaciones</h1>

                    <p>
                        Asigna profesores y alumnos a las asignaturas creadas en el sistema.
                    </p>
                </div>
            </section>

            <section aria-label="Resumen de asignaciones" class="resumen-metricas resumen-metricas--compacto">
                <article class="tarjeta-metrica tarjeta-metrica--principal">
                    <span>Asignaturas</span>
                    <strong><?php echo count($asignaturas); ?></strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Profesores disponibles</span>
                    <strong><?php echo count($profesores); ?></strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Alumnos disponibles</span>
                    <strong><?php echo count($alumnos); ?></strong>
                </article>

                <article class="tarjeta-metrica">
                    <span>Pendientes</span>
                    <strong><?php echo $total_pendientes; ?></strong>
                </article>
            </section>

            <?php if ($mensaje_ok !== "") { ?>
                <p class="mensaje-formulario-secretaria">
                    <?php echo limpiar_texto_doa($mensaje_ok); ?>
                </p>
            <?php } ?>

            <?php if (count($errores) > 0) { ?>
                <div class="mensaje-formulario-secretaria mensaje-formulario-secretaria--error">
                    <?php foreach ($errores as $error) { ?>
                        <p><?php echo limpiar_texto_doa($error); ?></p>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="grid-asignaciones-secretaria">
                <section class="bloque-secretaria">
                    <div class="bloque-secretaria__cabecera">
                        <div>
                            <h2>Asignar usuarios</h2>

                            <p>
                                Selecciona una asignatura y configura el profesor responsable y los alumnos del grupo.
                            </p>
                        </div>
                    </div>

                    <?php if (count($asignaturas) === 0) { ?>
                        <p class="mensaje-formulario-secretaria">
                            Todavía no hay asignaturas creadas. Primero crea una asignatura desde Secretaría.
                        </p>

                        <div class="acciones-formulario-secretaria">
                            <a class="boton-secretaria boton-secretaria--principal" href="crear_asignatura.php">
                                Crear asignatura
                            </a>
                        </div>
                    <?php } else { ?>
                        <form class="formulario-asignaciones-secretaria" method="get">
                            <div class="campo-formulario-secretaria campo-formulario-secretaria--completo">
                                <label class="form-label" for="selectAsignaturaSecretaria">
                                    Asignatura *
                                </label>

                                <select class="input" id="selectAsignaturaSecretaria" name="id_asignatura">
                                    <?php foreach ($asignaturas as $asignatura) { ?>
                                        <option
                                            value="<?php echo (int) $asignatura["id_asignatura"]; ?>"
                                            <?php echo (int) $asignatura["id_asignatura"] === $id_asignatura_seleccionada ? "selected" : ""; ?>>
                                            <?php echo limpiar_texto_doa($asignatura["nombre"]); ?>
                                            · Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                                            · <?php echo limpiar_texto_doa($asignatura["codigo"]); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="acciones-formulario-secretaria">
                                <button class="boton-secretaria" type="submit">
                                    Cargar asignatura
                                </button>
                            </div>
                        </form>

                        <form class="formulario-asignaciones-secretaria" method="post">
                            <input type="hidden" name="id_asignatura" value="<?php echo $id_asignatura_seleccionada; ?>">

                            <div class="panel-asignacion-secretaria">
                                <div class="panel-asignacion-secretaria__cabecera">
                                    <div>
                                        <h3>Profesor asignado</h3>

                                        <p>
                                            Selecciona el docente responsable de la asignatura.
                                        </p>
                                    </div>
                                </div>

                                <label class="form-label" for="selectProfesorSecretaria">
                                    Profesor
                                </label>

                                <select class="input" id="selectProfesorSecretaria" name="id_profesor">
                                    <option value="">Sin profesor asignado</option>

                                    <?php foreach ($profesores as $profesor) { ?>
                                        <?php
                                        $nombre_profesor = trim($profesor["nombre"] . " " . $profesor["apellidos"]);
                                        ?>

                                        <option
                                            value="<?php echo (int) $profesor["id_usuario"]; ?>"
                                            <?php echo (string) $profesor["id_usuario"] === $id_profesor_asignado ? "selected" : ""; ?>>
                                            <?php echo limpiar_texto_doa($nombre_profesor); ?>
                                            · <?php echo limpiar_texto_doa($profesor["tipo_usuario"]); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="panel-asignacion-secretaria">
                                <div class="panel-asignacion-secretaria__cabecera">
                                    <div>
                                        <h3>Alumnos asignados</h3>

                                        <p>
                                            Marca los alumnos que pertenecen a este grupo.
                                        </p>
                                    </div>

                                    <span class="contador-alumnos-secretaria">
                                        <?php echo $total_alumnos_asignados; ?> seleccionados
                                    </span>
                                </div>

                                <div class="lista-alumnos-secretaria">
                                    <?php foreach ($alumnos as $alumno) { ?>
                                        <?php
                                        $nombre_alumno = trim($alumno["nombre"] . " " . $alumno["apellidos"]);
                                        ?>

                                        <label class="item-alumno-secretaria">
                                            <input
                                                type="checkbox"
                                                name="ids_alumnos[]"
                                                value="<?php echo (int) $alumno["id_usuario"]; ?>"
                                                <?php echo in_array((int) $alumno["id_usuario"], $ids_alumnos_asignados, true) ? "checked" : ""; ?>>

                                            <span>
                                                <strong><?php echo limpiar_texto_doa($nombre_alumno); ?></strong>
                                                <small>
                                                    <?php echo limpiar_texto_doa($alumno["email"]); ?>
                                                    · <?php echo limpiar_texto_doa($alumno["dni"]); ?>
                                                </small>
                                            </span>
                                        </label>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="acciones-formulario-secretaria">
                                <a class="boton-secretaria" href="asignaturas_secretaria.php">
                                    Volver a asignaturas
                                </a>

                                <button class="boton-secretaria boton-secretaria--principal" type="submit">
                                    Guardar asignaciones
                                </button>
                            </div>
                        </form>
                    <?php } ?>
                </section>

                <aside class="lateral-secretaria">
                    <article class="tarjeta-lateral-secretaria">
                        <h3>Resumen actual</h3>

                        <dl class="resumen-asignacion-secretaria">
                            <div>
                                <dt>Asignatura</dt>
                                <dd>
                                    <?php echo $asignatura_seleccionada !== null ? limpiar_texto_doa($asignatura_seleccionada["nombre"]) : "-"; ?>
                                </dd>
                            </div>

                            <div>
                                <dt>Código</dt>
                                <dd>
                                    <?php echo $asignatura_seleccionada !== null ? limpiar_texto_doa($asignatura_seleccionada["codigo"]) : "-"; ?>
                                </dd>
                            </div>

                            <div>
                                <dt>Profesor</dt>
                                <dd><?php echo limpiar_texto_doa($nombre_profesor_asignado); ?></dd>
                            </div>

                            <div>
                                <dt>Alumnos</dt>
                                <dd><?php echo $total_alumnos_asignados; ?> asignados</dd>
                            </div>

                            <div>
                                <dt>Estado</dt>
                                <dd>
                                    <span class="estado-secretaria <?php echo limpiar_texto_doa($clase_estado_resumen); ?>">
                                        <?php echo limpiar_texto_doa($texto_estado_resumen); ?>
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </article>

                    <article class="tarjeta-lateral-secretaria">
                        <h3>Indicaciones</h3>

                        <div class="lista-lateral-secretaria">
                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>Profesor</strong>
                                <span>Cada asignatura puede tener un profesor responsable.</span>
                            </div>

                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>Alumnos</strong>
                                <span>Los alumnos marcados quedarán asociados al grupo seleccionado.</span>
                            </div>

                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>Base de datos</strong>
                                <span>Las asignaciones se guardan en la tabla usuarios_asignaturas.</span>
                            </div>
                        </div>
                    </article>
                </aside>
            </div>
        </main>
    </div>
</body>

</html>