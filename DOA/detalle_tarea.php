<?php
// Inicio configuración de página

$rol_pagina = "alumno";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_principal.php";
$placeholder_buscador = "Buscar asignatura...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página


// Inicio funciones auxiliares

function formatear_fecha_tarea($fecha)
{
    if ($fecha === null) {
        return "-";
    }

    return date("d/m/Y", strtotime($fecha));
}

function formatear_nota_tarea($nota)
{
    if ($nota === null) {
        return "-";
    }

    return number_format((float) $nota, 1) . "/10";
}

function formatear_tamano_archivo_entrega($tamano_bytes)
{
    if ($tamano_bytes === null) {
        return "-";
    }

    if ((int) $tamano_bytes >= 1048576) {
        return round((int) $tamano_bytes / 1048576, 1) . " MB";
    }

    return round((int) $tamano_bytes / 1024, 1) . " KB";
}

function limpiar_nombre_archivo_entrega($nombre_archivo)
{
    $nombre_limpio = strtolower($nombre_archivo);
    $nombre_limpio = preg_replace("/[^a-z0-9._-]/", "-", $nombre_limpio);

    return $nombre_limpio;
}

function obtener_estado_tarea_detalle($entrega, $calificacion)
{
    if ($calificacion) {
        return "calificada";
    }

    if (!$entrega) {
        return "pendiente";
    }

    return $entrega["estado"];
}

function obtener_texto_estado_tarea_detalle($estado)
{
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

if (!isset($_GET["id_actividad"]) && !isset($_POST["id_actividad"])) {
    header("Location: listado_tareas.php");
    exit;
}

$id_alumno = (int) $_SESSION["doa_id_usuario"];
$id_actividad = isset($_POST["id_actividad"])
    ? (int) $_POST["id_actividad"]
    : (int) $_GET["id_actividad"];

$errores = [];
$mensaje_ok = "";

$texto_entrega = "";

if (isset($_GET["entregada"]) && $_GET["entregada"] === "ok") {
    $mensaje_ok = "Tarea entregada correctamente.";
}

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
        a.nombre AS asignatura_nombre,
        a.codigo,
        a.curso,
        a.grupo,
        GROUP_CONCAT(
            DISTINCT CONCAT(u_profesor.nombre, ' ', u_profesor.apellidos)
            SEPARATOR ', '
        ) AS profesores
    FROM actividades_evaluables ae
    INNER JOIN asignaturas a
        ON a.id_asignatura = ae.id_asignatura
    INNER JOIN usuarios_asignaturas ua_alumno
        ON ua_alumno.id_asignatura = ae.id_asignatura
        AND ua_alumno.id_usuario = :id_alumno
        AND ua_alumno.rol_asignatura = 'alumno'
        AND ua_alumno.estado = 'activa'
    LEFT JOIN usuarios_asignaturas ua_profesor
        ON ua_profesor.id_asignatura = ae.id_asignatura
        AND ua_profesor.rol_asignatura = 'profesor'
        AND ua_profesor.estado = 'activa'
    LEFT JOIN usuarios u_profesor
        ON u_profesor.id_usuario = ua_profesor.id_usuario
    WHERE ae.id_actividad = :id_actividad
    AND ae.tipo_actividad IN ('tarea', 'practica')
    AND ae.visible = 1
    AND ae.estado = 'publicada'
    AND a.estado = 'activa'
    GROUP BY
        ae.id_actividad,
        ae.id_asignatura,
        ae.tipo_actividad,
        ae.unidad,
        ae.titulo,
        ae.descripcion,
        ae.fecha_inicio,
        ae.fecha_limite,
        a.nombre,
        a.codigo,
        a.curso,
        a.grupo
    LIMIT 1
");

$consulta_tarea->execute([
    "id_alumno" => $id_alumno,
    "id_actividad" => $id_actividad
]);

$tarea = $consulta_tarea->fetch();

if (!$tarea) {
    header("Location: asignaturas.php");
    exit;
}

$id_asignatura = (int) $tarea["id_asignatura"];
$profesores = $tarea["profesores"] !== null ? $tarea["profesores"] : "Pendiente";

// Fin consulta de tarea


// Inicio consulta de entrega y calificación

$consulta_entrega = $pdo->prepare("
    SELECT
        id_entrega,
        texto_entrega,
        fecha_entrega,
        estado
    FROM entregas
    WHERE id_actividad = :id_actividad
    AND id_alumno = :id_alumno
    LIMIT 1
");

$consulta_entrega->execute([
    "id_actividad" => $id_actividad,
    "id_alumno" => $id_alumno
]);

$entrega = $consulta_entrega->fetch();

$consulta_calificacion = $pdo->prepare("
    SELECT
        id_calificacion,
        nota,
        comentario_profesor,
        fecha_calificacion
    FROM calificaciones
    WHERE id_actividad = :id_actividad
    AND id_alumno = :id_alumno
    LIMIT 1
");

$consulta_calificacion->execute([
    "id_actividad" => $id_actividad,
    "id_alumno" => $id_alumno
]);

$calificacion = $consulta_calificacion->fetch();

$archivos_entrega = [];

if ($entrega) {
    $consulta_archivos_entrega = $pdo->prepare("
        SELECT
            nombre_archivo,
            url_archivo,
            tipo_archivo,
            tamano_bytes
        FROM archivos_entrega
        WHERE id_entrega = :id_entrega
        ORDER BY fecha_subida ASC
    ");

    $consulta_archivos_entrega->execute([
        "id_entrega" => $entrega["id_entrega"]
    ]);

    $archivos_entrega = $consulta_archivos_entrega->fetchAll();

    $texto_entrega = $entrega["texto_entrega"] ?? "";
}

// Fin consulta de entrega y calificación


// Inicio guardado de entrega

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $texto_entrega = trim($_POST["texto_entrega"] ?? "");

    if ($entrega) {
        $errores[] = "Esta tarea ya tiene una entrega registrada.";
    }

    if (!isset($_FILES["archivos_entrega"]) || $_FILES["archivos_entrega"]["name"][0] === "") {
        $errores[] = "Selecciona al menos un archivo para entregar la tarea.";
    }

    if (count($errores) === 0) {
        $extensiones_permitidas = [
            "PDF",
            "DOCX",
            "ZIP",
            "JPG",
            "JPEG",
            "PNG",
            "WEBP"
        ];

        foreach ($_FILES["archivos_entrega"]["name"] as $indice => $nombre_original) {
            if ($_FILES["archivos_entrega"]["error"][$indice] !== UPLOAD_ERR_OK) {
                $errores[] = "Uno de los archivos no se ha subido correctamente.";
            }

            $extension = strtoupper(pathinfo($nombre_original, PATHINFO_EXTENSION));

            if (!in_array($extension, $extensiones_permitidas, true)) {
                $errores[] = "El archivo " . $nombre_original . " tiene un tipo no permitido.";
            }
        }
    }

    if (count($errores) === 0) {
        $directorio_entregas = __DIR__ . "/uploads/entregas";

        if (!is_dir($directorio_entregas)) {
            $errores[] = "Falta la carpeta DOA/uploads/entregas.";
        }
    }

    if (count($errores) === 0) {
        $estado_entrega = "entregada";

        if ($tarea["fecha_limite"] !== null && date("Y-m-d H:i:s") > $tarea["fecha_limite"]) {
            $estado_entrega = "tardia";
        }

        $pdo->beginTransaction();

        try {
            $insertar_entrega = $pdo->prepare("
                INSERT INTO entregas
                    (id_actividad, id_alumno, texto_entrega, estado)
                VALUES
                    (:id_actividad, :id_alumno, :texto_entrega, :estado)
            ");

            $insertar_entrega->execute([
                "id_actividad" => $id_actividad,
                "id_alumno" => $id_alumno,
                "texto_entrega" => $texto_entrega !== "" ? $texto_entrega : null,
                "estado" => $estado_entrega
            ]);

            $id_entrega = (int) $pdo->lastInsertId();

            $insertar_archivo = $pdo->prepare("
                INSERT INTO archivos_entrega
                    (id_entrega, nombre_archivo, url_archivo, tipo_archivo, tamano_bytes)
                VALUES
                    (:id_entrega, :nombre_archivo, :url_archivo, :tipo_archivo, :tamano_bytes)
            ");

            foreach ($_FILES["archivos_entrega"]["name"] as $indice => $nombre_original) {
                $extension = strtoupper(pathinfo($nombre_original, PATHINFO_EXTENSION));
                $nombre_archivo = date("YmdHis") . "_" . $id_entrega . "_" . $indice . "_" . limpiar_nombre_archivo_entrega($nombre_original);
                $ruta_destino = $directorio_entregas . "/" . $nombre_archivo;
                $url_archivo = "uploads/entregas/" . $nombre_archivo;

                if (!move_uploaded_file($_FILES["archivos_entrega"]["tmp_name"][$indice], $ruta_destino)) {
                    throw new RuntimeException("No se ha podido guardar uno de los archivos de entrega.");
                }

                $insertar_archivo->execute([
                    "id_entrega" => $id_entrega,
                    "nombre_archivo" => $nombre_original,
                    "url_archivo" => $url_archivo,
                    "tipo_archivo" => $extension,
                    "tamano_bytes" => (int) $_FILES["archivos_entrega"]["size"][$indice]
                ]);
            }

            $pdo->commit();

            header("Location: detalle_tarea.php?id_actividad=" . $id_actividad . "&entregada=ok");
            exit;
        } catch (Throwable $error) {
            $pdo->rollBack();
            throw $error;
        }
    }
}

// Fin guardado de entrega


// Inicio consulta de recursos adjuntos

$consulta_recursos = $pdo->prepare("
    SELECT
        titulo,
        url_archivo,
        url_externa,
        tipo_archivo
    FROM recursos
    WHERE id_asignatura = :id_asignatura
    AND visible = 1
    AND (
        UPPER(carpeta) = UPPER(:unidad)
        OR carpeta = 'PRÁCTICAS'
    )
    ORDER BY fecha_publicacion DESC
    LIMIT 4
");

$consulta_recursos->execute([
    "id_asignatura" => $id_asignatura,
    "unidad" => $tarea["unidad"]
]);

$recursos_adjuntos = $consulta_recursos->fetchAll();

// Fin consulta de recursos adjuntos


// Inicio datos derivados

$estado_tarea = obtener_estado_tarea_detalle($entrega, $calificacion);
$texto_estado = obtener_texto_estado_tarea_detalle($estado_tarea);

$fecha_emision = formatear_fecha_tarea($tarea["fecha_inicio"]);
$fecha_entrega = formatear_fecha_tarea($tarea["fecha_limite"]);
$nota_tarea = $calificacion ? formatear_nota_tarea($calificacion["nota"]) : "-";

$url_listado_tareas = "listado_tareas.php?id_asignatura=" . $id_asignatura;
$url_recursos = "recursos_alumno.php?id_asignatura=" . $id_asignatura;
$url_tareas = "listado_tareas.php?id_asignatura=" . $id_asignatura;
$url_examenes = "examenes.php?id_asignatura=" . $id_asignatura;
$url_calificaciones = "calificaciones.php?id_asignatura=" . $id_asignatura;

// Fin datos derivados
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title><?php echo limpiar_texto_doa($tarea["titulo"]); ?> | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/detalle_tarea.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-detalle-tarea">
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
                                <img alt="" src="img/iconos/grey-user.svg">
                                <span><?php echo limpiar_texto_doa($profesores); ?></span>
                            </li>

                            <li>
                                <img alt="" src="img/iconos/grey-notebook.svg">
                                <span>
                                    <?php echo limpiar_texto_doa($tarea["unidad"]); ?>
                                </span>
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

                <section class="detalle-tarea">
                    <div class="cabecera-tarea-detalle">
                        <span class="etiqueta-estado etiqueta-estado--<?php echo limpiar_texto_doa($estado_tarea); ?>">
                            <?php echo limpiar_texto_doa($texto_estado); ?>
                        </span>

                        <h2><?php echo limpiar_texto_doa($tarea["titulo"]); ?></h2>

                        <p class="descripcion-tarea">
                            <?php echo limpiar_texto_doa($tarea["descripcion"]); ?>
                        </p>
                    </div>

                    <div class="fechas-tarea">
                        <div class="fecha-tarea">
                            <span>Fecha de emisión</span>
                            <p class="fecha-tarea__valor"><?php echo limpiar_texto_doa($fecha_emision); ?></p>
                        </div>

                        <div class="fecha-tarea">
                            <span>Fecha de entrega</span>
                            <p class="fecha-tarea__valor"><?php echo limpiar_texto_doa($fecha_entrega); ?></p>
                        </div>

                        <div class="fecha-tarea">
                            <span>Calificación</span>
                            <p class="fecha-tarea__valor"><?php echo limpiar_texto_doa($nota_tarea); ?></p>
                        </div>
                    </div>

                    <?php if ($mensaje_ok !== "") { ?>
                        <p class="mensaje-tarea">
                            <?php echo limpiar_texto_doa($mensaje_ok); ?>
                        </p>
                    <?php } ?>

                    <?php if (count($errores) > 0) { ?>
                        <div class="mensaje-tarea mensaje-tarea--error">
                            <?php foreach ($errores as $error) { ?>
                                <p><?php echo limpiar_texto_doa($error); ?></p>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <div class="zona-entrega">
                        <section class="archivos-subidos">
                            <div>
                                <h3 class="titulo-bloque-tarea">Mi entrega</h3>

                                <ul class="lista-archivos">
                                    <?php if (!$entrega) { ?>
                                        <li class="item-sin-archivos">
                                            Todavía no has entregado ningún archivo.
                                        </li>
                                    <?php } ?>

                                    <?php foreach ($archivos_entrega as $archivo) { ?>
                                        <li>
                                            <a href="<?php echo limpiar_texto_doa($archivo["url_archivo"]); ?>">
                                                <img alt="" src="img/iconos/grey-file.svg">
                                                <span>
                                                    <?php echo limpiar_texto_doa($archivo["nombre_archivo"]); ?>
                                                    · <?php echo limpiar_texto_doa(formatear_tamano_archivo_entrega($archivo["tamano_bytes"])); ?>
                                                </span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>

                            <?php if (!$entrega) { ?>
                                <form class="subida-archivo" method="post" enctype="multipart/form-data" id="formEntregaTarea">
                                    <input type="hidden" name="id_actividad" value="<?php echo $id_actividad; ?>">

                                    <input
                                        class="input"
                                        id="archivoEntrega"
                                        name="archivos_entrega[]"
                                        type="file"
                                        multiple
                                        required>

                                    <div class="grupo-campo-entrega">
                                        <label class="form-label" for="textoEntrega">
                                            Comentario opcional
                                        </label>

                                        <textarea
                                            class="input textarea-entrega"
                                            id="textoEntrega"
                                            name="texto_entrega"
                                            placeholder="Añade un comentario breve para el profesor..."><?php echo limpiar_texto_doa($texto_entrega); ?></textarea>
                                    </div>
                                </form>
                            <?php } ?>

                            <?php if ($entrega && $texto_entrega !== "") { ?>
                                <div class="comentario-entrega-registrada">
                                    <h4>Comentario enviado</h4>

                                    <p>
                                        <?php echo limpiar_texto_doa($texto_entrega); ?>
                                    </p>
                                </div>
                            <?php } ?>
                        </section>

                        <section class="recursos-adjuntos">
                            <h3>Recursos adjuntos</h3>

                            <ul>
                                <?php if (count($recursos_adjuntos) === 0) { ?>
                                    <li class="item-sin-archivos">
                                        No hay recursos adjuntos para esta tarea.
                                    </li>
                                <?php } ?>

                                <?php foreach ($recursos_adjuntos as $recurso) { ?>
                                    <?php
                                    $url_recurso = $recurso["url_externa"] ?: $recurso["url_archivo"];
                                    ?>

                                    <li>
                                        <a href="<?php echo limpiar_texto_doa($url_recurso ?: "#"); ?>">
                                            <span>
                                                <img alt="" src="img/iconos/grey-file.svg">
                                                <?php echo limpiar_texto_doa($recurso["titulo"]); ?>
                                            </span>

                                            <img alt="" src="img/iconos/grey-download.svg">
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </section>

                        <section class="tarjeta-acciones-tarea">
                            <h3>Acciones de entrega</h3>

                            <?php if (!$entrega) { ?>
                                <div class="acciones-tarea acciones-tarea--vertical">
                                    <button class="boton-principal" type="submit" form="formEntregaTarea">
                                        Entregar
                                    </button>

                                    <a class="boton-secundario boton-enlace-tarea" href="<?php echo limpiar_texto_doa($url_listado_tareas); ?>">
                                        Cancelar
                                    </a>
                                </div>
                            <?php } else { ?>
                                <div class="acciones-tarea acciones-tarea--vertical">
                                    <a class="boton-secundario boton-enlace-tarea" href="<?php echo limpiar_texto_doa($url_listado_tareas); ?>">
                                        Volver a tareas
                                    </a>
                                </div>
                            <?php } ?>
                        </section>
                    </div>
                </section>

                <!-- Fin detalle de tarea -->
            </section>
        </main>

        <!-- Fin contenido principal -->
    </div>
</body>

</html>