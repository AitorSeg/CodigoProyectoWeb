<?php
// Inicio configuración de página

$rol_pagina = "alumno";
$pagina_activa = "notificaciones";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar notificación...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

$id_alumno = (int) $_SESSION["doa_id_usuario"];

// Fin configuración de página


// Inicio funciones auxiliares

function formatear_fecha_notificacion($fecha)
{
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y H:i", strtotime($fecha));
}

function obtener_texto_tipo_notificacion($tipo)
{
    return match ($tipo) {
        "tarea" => "Tarea",
        "recurso" => "Recurso",
        "calificacion" => "Calificación",
        "anuncio" => "Anuncio",
        "aviso" => "Aviso",
        default => "Notificación",
    };
}

function obtener_resumen_notificacion($mensaje)
{
    $mensaje_limpio = trim((string) $mensaje);

    if (mb_strlen($mensaje_limpio) <= 120) {
        return $mensaje_limpio;
    }

    return mb_substr($mensaje_limpio, 0, 120) . "...";
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

$filtro_notificaciones = $_GET["filtro"] ?? "todas";
$id_notificacion_seleccionada = isset($_GET["id_notificacion"])
    ? (int) $_GET["id_notificacion"]
    : 0;

$filtros_validos = ["todas", "no-leidas", "tarea", "aviso"];

if (!in_array($filtro_notificaciones, $filtros_validos, true)) {
    $filtro_notificaciones = "todas";
}

// Fin parámetros de pantalla


// Inicio acciones de lectura

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";
    $filtro_redireccion = $_POST["filtro"] ?? "todas";

    if (!in_array($filtro_redireccion, $filtros_validos, true)) {
        $filtro_redireccion = "todas";
    }

    if ($accion === "marcar_todas") {
        $actualizar_notificaciones = $pdo->prepare("
            UPDATE notificaciones
            SET
                leida = 1,
                fecha_lectura = NOW()
            WHERE id_usuario_destino = :id_usuario_destino
            AND leida = 0
        ");

        $actualizar_notificaciones->execute([
            "id_usuario_destino" => $id_alumno
        ]);

        header("Location: notificaciones.php?filtro=" . urlencode($filtro_redireccion));
        exit;
    }

    if ($accion === "marcar_una") {
        $id_notificacion_post = (int) ($_POST["id_notificacion"] ?? 0);

        $actualizar_notificacion = $pdo->prepare("
            UPDATE notificaciones
            SET
                leida = 1,
                fecha_lectura = NOW()
            WHERE id_notificacion = :id_notificacion
            AND id_usuario_destino = :id_usuario_destino
        ");

        $actualizar_notificacion->execute([
            "id_notificacion" => $id_notificacion_post,
            "id_usuario_destino" => $id_alumno
        ]);

        header(
            "Location: notificaciones.php?filtro="
            . urlencode($filtro_redireccion)
            . "&id_notificacion="
            . $id_notificacion_post
        );
        exit;
    }
}

// Fin acciones de lectura


// Inicio resumen de notificaciones

$consulta_resumen = $pdo->prepare("
    SELECT
        SUM(CASE WHEN leida = 0 THEN 1 ELSE 0 END) AS total_no_leidas,
        SUM(CASE WHEN tipo_notificacion = 'tarea' THEN 1 ELSE 0 END) AS total_tareas,
        SUM(CASE WHEN tipo_notificacion IN ('aviso', 'anuncio') THEN 1 ELSE 0 END) AS total_avisos
    FROM notificaciones
    WHERE id_usuario_destino = :id_usuario_destino
");

$consulta_resumen->execute([
    "id_usuario_destino" => $id_alumno
]);

$resumen_notificaciones = $consulta_resumen->fetch();

$total_no_leidas = (int) ($resumen_notificaciones["total_no_leidas"] ?? 0);
$total_tareas = (int) ($resumen_notificaciones["total_tareas"] ?? 0);
$total_avisos = (int) ($resumen_notificaciones["total_avisos"] ?? 0);

// Fin resumen de notificaciones


// Inicio consulta de listado

$condiciones_notificaciones = [
    "n.id_usuario_destino = :id_usuario_destino"
];

$parametros_notificaciones = [
    "id_usuario_destino" => $id_alumno
];

if ($filtro_notificaciones === "no-leidas") {
    $condiciones_notificaciones[] = "n.leida = 0";
}

if ($filtro_notificaciones === "tarea") {
    $condiciones_notificaciones[] = "n.tipo_notificacion = 'tarea'";
}

if ($filtro_notificaciones === "aviso") {
    $condiciones_notificaciones[] = "n.tipo_notificacion IN ('aviso', 'anuncio')";
}

$where_notificaciones = implode(" AND ", $condiciones_notificaciones);

$consulta_notificaciones = $pdo->prepare("
    SELECT
        n.id_notificacion,
        n.tipo_notificacion,
        n.titulo,
        n.mensaje,
        n.url_destino,
        n.leida,
        n.fecha_creacion,
        n.fecha_lectura,
        creador.nombre AS creador_nombre,
        creador.apellidos AS creador_apellidos
    FROM notificaciones n
    LEFT JOIN usuarios creador
        ON creador.id_usuario = n.id_usuario_creador
    WHERE $where_notificaciones
    ORDER BY n.leida ASC, n.fecha_creacion DESC
    LIMIT 40
");

$consulta_notificaciones->execute($parametros_notificaciones);
$notificaciones = $consulta_notificaciones->fetchAll();

// Fin consulta de listado


// Inicio consulta de detalle

$notificacion_detalle = null;

if ($id_notificacion_seleccionada > 0) {
    $consulta_detalle = $pdo->prepare("
        SELECT
            n.id_notificacion,
            n.tipo_notificacion,
            n.titulo,
            n.mensaje,
            n.url_destino,
            n.leida,
            n.fecha_creacion,
            n.fecha_lectura,
            creador.nombre AS creador_nombre,
            creador.apellidos AS creador_apellidos
        FROM notificaciones n
        LEFT JOIN usuarios creador
            ON creador.id_usuario = n.id_usuario_creador
        WHERE n.id_notificacion = :id_notificacion
        AND n.id_usuario_destino = :id_usuario_destino
        LIMIT 1
    ");

    $consulta_detalle->execute([
        "id_notificacion" => $id_notificacion_seleccionada,
        "id_usuario_destino" => $id_alumno
    ]);

    $notificacion_detalle = $consulta_detalle->fetch();
}

if (!$notificacion_detalle && count($notificaciones) > 0) {
    $notificacion_detalle = $notificaciones[0];
    $id_notificacion_seleccionada = (int) $notificacion_detalle["id_notificacion"];
}

// Fin consulta de detalle
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones | DOA</title>

    <link rel="stylesheet" href="css/doa.css">
    <link rel="stylesheet" href="css/doa_layout.css">
    <link rel="stylesheet" href="css/doa_componentes.css">
    <link rel="stylesheet" href="css/notificaciones.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-notificaciones">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido principal -->

        <main class="contenido-doa contenido-notificaciones">
            <!-- Inicio cabecera de notificaciones -->

            <section class="cabecera-notificaciones">
                <div>
                    <h1>Notificaciones</h1>

                    <p>
                        Consulta avisos del centro, recordatorios de asignaturas y comunicaciones importantes.
                    </p>
                </div>

                <form method="post">
                    <input type="hidden" name="accion" value="marcar_todas">
                    <input type="hidden" name="filtro" value="<?php echo limpiar_texto_doa($filtro_notificaciones); ?>">

                    <button type="submit" class="boton-marcar-todas">
                        Marcar todas como leídas
                    </button>
                </form>
            </section>

            <!-- Fin cabecera de notificaciones -->


            <!-- Inicio resumen -->

            <section class="resumen-notificaciones" aria-label="Resumen de notificaciones">
                <article class="tarjeta-resumen-notificacion">
                    <span>No leídas</span>
                    <strong><?php echo $total_no_leidas; ?></strong>
                </article>

                <article class="tarjeta-resumen-notificacion">
                    <span>Tareas</span>
                    <strong><?php echo $total_tareas; ?></strong>
                </article>

                <article class="tarjeta-resumen-notificacion">
                    <span>Avisos</span>
                    <strong><?php echo $total_avisos; ?></strong>
                </article>
            </section>

            <!-- Fin resumen -->


            <!-- Inicio listado y detalle -->

            <section class="notificaciones-grid">
                <div class="panel-listado-notificaciones">
                    <form class="filtros-notificaciones" method="get" aria-label="Filtros de notificaciones">
                        <button
                            type="submit"
                            class="filtro-notificacion <?php echo $filtro_notificaciones === "todas" ? "filtro-notificacion--activo" : ""; ?>"
                            name="filtro"
                            value="todas">
                            Todas
                        </button>

                        <button
                            type="submit"
                            class="filtro-notificacion <?php echo $filtro_notificaciones === "no-leidas" ? "filtro-notificacion--activo" : ""; ?>"
                            name="filtro"
                            value="no-leidas">
                            No leídas
                        </button>

                        <button
                            type="submit"
                            class="filtro-notificacion <?php echo $filtro_notificaciones === "tarea" ? "filtro-notificacion--activo" : ""; ?>"
                            name="filtro"
                            value="tarea">
                            Tareas
                        </button>

                        <button
                            type="submit"
                            class="filtro-notificacion <?php echo $filtro_notificaciones === "aviso" ? "filtro-notificacion--activo" : ""; ?>"
                            name="filtro"
                            value="aviso">
                            Avisos
                        </button>
                    </form>

                    <div class="lista-notificaciones">
                        <?php if (count($notificaciones) === 0) { ?>
                            <p class="mensaje-sin-notificaciones">
                                No hay notificaciones que coincidan con el filtro seleccionado.
                            </p>
                        <?php } ?>

                        <?php foreach ($notificaciones as $notificacion) { ?>
                            <?php
                            $id_notificacion = (int) $notificacion["id_notificacion"];
                            $notificacion_activa = $id_notificacion === $id_notificacion_seleccionada;
                            $notificacion_no_leida = (int) $notificacion["leida"] === 0;

                            $clase_item = "notificacion-item";

                            if ($notificacion_activa) {
                                $clase_item .= " notificacion-item--activa";
                            }

                            if ($notificacion_no_leida) {
                                $clase_item .= " notificacion-item--no-leida";
                            }

                            $url_notificacion = "notificaciones.php?filtro="
                                . urlencode($filtro_notificaciones)
                                . "&id_notificacion="
                                . $id_notificacion;

                            $texto_tipo = obtener_texto_tipo_notificacion($notificacion["tipo_notificacion"]);
                            ?>

                            <div class="bloque-notificacion">
                                <a class="<?php echo limpiar_texto_doa($clase_item); ?>" href="<?php echo limpiar_texto_doa($url_notificacion); ?>">
                                    <div>
                                        <p class="notificacion-item__titulo">
                                            <?php echo limpiar_texto_doa($notificacion["titulo"]); ?>
                                        </p>

                                        <span class="notificacion-item__tipo">
                                            <?php echo limpiar_texto_doa($texto_tipo); ?>
                                        </span>
                                    </div>

                                    <span class="notificacion-item__fecha">
                                        <?php echo limpiar_texto_doa(formatear_fecha_notificacion($notificacion["fecha_creacion"])); ?>
                                    </span>

                                    <p class="notificacion-item__resumen">
                                        <?php echo limpiar_texto_doa(obtener_resumen_notificacion($notificacion["mensaje"])); ?>
                                    </p>
                                </a>

                                <?php if ($notificacion_activa) { ?>
                                    <div class="detalle-notificacion-movil">
                                        <div class="detalle-notificacion-movil__cabecera">
                                            <p><?php echo limpiar_texto_doa($texto_tipo); ?></p>

                                            <p>
                                                <?php echo limpiar_texto_doa(formatear_fecha_notificacion($notificacion["fecha_creacion"])); ?>
                                            </p>
                                        </div>

                                        <p class="detalle-notificacion-movil__texto">
                                            <?php echo limpiar_texto_doa($notificacion["mensaje"]); ?>
                                        </p>

                                        <div class="detalle-notificacion-movil__acciones">
                                            <?php if ((int) $notificacion["leida"] === 0) { ?>
                                                <form method="post">
                                                    <input type="hidden" name="accion" value="marcar_una">
                                                    <input type="hidden" name="id_notificacion" value="<?php echo $id_notificacion; ?>">
                                                    <input type="hidden" name="filtro" value="<?php echo limpiar_texto_doa($filtro_notificaciones); ?>">

                                                    <button class="boton-lectura-notificacion" type="submit">
                                                        Marcar como leída
                                                    </button>
                                                </form>
                                            <?php } ?>

                                            <?php if ($notificacion["url_destino"] !== null && $notificacion["url_destino"] !== "") { ?>
                                                <a
                                                    class="boton-accion-notificacion boton-accion-notificacion-movil"
                                                    href="<?php echo limpiar_texto_doa($notificacion["url_destino"]); ?>">
                                                    Ver detalle
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <aside class="detalle-notificacion" aria-label="Detalle de la notificación seleccionada">
                    <?php if (!$notificacion_detalle) { ?>
                        <div class="detalle-notificacion__cabecera">
                            <span class="etiqueta-notificacion">
                                Notificación
                            </span>

                            <h2>Sin notificaciones</h2>

                            <p>--</p>
                        </div>

                        <div class="detalle-notificacion__contenido">
                            <p>
                                Cuando recibas una notificación, aparecerá en este panel.
                            </p>
                        </div>
                    <?php } else { ?>
                        <?php
                        $tipo_detalle = obtener_texto_tipo_notificacion($notificacion_detalle["tipo_notificacion"]);
                        $creador_detalle = trim(
                            (string) $notificacion_detalle["creador_nombre"]
                            . " "
                            . (string) $notificacion_detalle["creador_apellidos"]
                        );

                        if ($creador_detalle === "") {
                            $creador_detalle = "Sistema DOA";
                        }
                        ?>

                        <div class="detalle-notificacion__cabecera">
                            <span class="etiqueta-notificacion">
                                <?php echo limpiar_texto_doa($tipo_detalle); ?>
                            </span>

                            <h2>
                                <?php echo limpiar_texto_doa($notificacion_detalle["titulo"]); ?>
                            </h2>

                            <p>
                                <?php echo limpiar_texto_doa($creador_detalle); ?>
                                ·
                                <?php echo limpiar_texto_doa(formatear_fecha_notificacion($notificacion_detalle["fecha_creacion"])); ?>
                            </p>
                        </div>

                        <div class="detalle-notificacion__contenido">
                            <p>
                                <?php echo limpiar_texto_doa($notificacion_detalle["mensaje"]); ?>
                            </p>
                        </div>

                        <div class="detalle-notificacion__acciones">
                            <?php if ((int) $notificacion_detalle["leida"] === 0) { ?>
                                <form method="post">
                                    <input type="hidden" name="accion" value="marcar_una">
                                    <input type="hidden" name="id_notificacion" value="<?php echo (int) $notificacion_detalle["id_notificacion"]; ?>">
                                    <input type="hidden" name="filtro" value="<?php echo limpiar_texto_doa($filtro_notificaciones); ?>">

                                    <button type="submit" class="boton-lectura-notificacion">
                                        Marcar como leída
                                    </button>
                                </form>
                            <?php } else { ?>
                                <span class="boton-lectura-notificacion boton-lectura-notificacion--desactivado">
                                    Leída
                                </span>
                            <?php } ?>

                            <?php if ($notificacion_detalle["url_destino"] !== null && $notificacion_detalle["url_destino"] !== "") { ?>
                                <a
                                    href="<?php echo limpiar_texto_doa($notificacion_detalle["url_destino"]); ?>"
                                    class="boton-accion-notificacion">
                                    Ver detalle
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </aside>
            </section>

            <!-- Fin listado y detalle -->
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>
</html>