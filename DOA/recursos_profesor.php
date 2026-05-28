<?php
// Inicio configuración de página

$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar recursos...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página


// Inicio funciones auxiliares

function construir_url_recursos_profesor($id_asignatura, $carpeta, $filtro_tipo, $filtro_etiqueta)
{
    return "recursos_profesor.php?" . http_build_query([
        "id_asignatura" => $id_asignatura,
        "carpeta" => $carpeta,
        "tipo" => $filtro_tipo,
        "etiqueta" => $filtro_etiqueta
    ]);
}

function formatear_tamano_recurso_profesor($tamano_bytes)
{
    if ($tamano_bytes === null) {
        return "-";
    }

    if ((int) $tamano_bytes >= 1048576) {
        return round((int) $tamano_bytes / 1048576, 1) . " MB";
    }

    return round((int) $tamano_bytes / 1024, 1) . " KB";
}

function obtener_tipo_recurso_visible_profesor($recurso)
{
    if ($recurso["tipo_archivo"] !== null && $recurso["tipo_archivo"] !== "") {
        return strtoupper($recurso["tipo_archivo"]);
    }

    return ucfirst($recurso["tipo_recurso"]);
}

function obtener_clase_etiqueta_recurso_profesor($etiqueta)
{
    return match ($etiqueta) {
        "Unidad actual" => "etiqueta-actual",
        "Práctica", "Importante" => "etiqueta-importante",
        "Nuevo" => "etiqueta-success",
        default => "etiqueta-neutral",
    };
}

function obtener_tipo_recurso_bd($extension)
{
    return match ($extension) {
        "JPG", "JPEG", "PNG", "WEBP" => "imagen",
        "MP4", "WEBM" => "video",
        "PDF", "PPTX", "DOCX" => "documento",
        default => "otro",
    };
}

function limpiar_nombre_archivo($nombre_archivo)
{
    $nombre_limpio = strtolower($nombre_archivo);
    $nombre_limpio = preg_replace("/[^a-z0-9._-]/", "-", $nombre_limpio);

    return $nombre_limpio;
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

if (!isset($_GET["id_asignatura"]) && !isset($_POST["id_asignatura"])) {
    header("Location: asignaturas_profesor.php");
    exit;
}

$id_profesor = (int) $_SESSION["doa_id_usuario"];
$id_asignatura = isset($_POST["id_asignatura"])
    ? (int) $_POST["id_asignatura"]
    : (int) $_GET["id_asignatura"];

$carpetas_disponibles = [
    "UNIDAD 01",
    "UNIDAD 02",
    "UNIDAD 03",
    "PRÁCTICAS",
    "EXÁMENES"
];

$tipos_disponibles = [
    "TODOS",
    "PDF",
    "ZIP",
    "PPTX",
    "VIDEO",
    "ENLACE",
    "IMAGEN"
];

$etiquetas_disponibles = [
    "TODAS",
    "Unidad actual",
    "Práctica",
    "Importante",
    "Nuevo"
];

$carpeta_actual = $_GET["carpeta"] ?? "UNIDAD 03";
$filtro_tipo = $_GET["tipo"] ?? "TODOS";
$filtro_etiqueta = $_GET["etiqueta"] ?? "TODAS";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $carpeta_actual = $_POST["carpeta_recurso"];
}

if (!in_array($carpeta_actual, $carpetas_disponibles, true)) {
    $carpeta_actual = "UNIDAD 03";
}

if (!in_array($filtro_tipo, $tipos_disponibles, true)) {
    $filtro_tipo = "TODOS";
}

if (!in_array($filtro_etiqueta, $etiquetas_disponibles, true)) {
    $filtro_etiqueta = "TODAS";
}

$carpeta_es_unidad = str_starts_with($carpeta_actual, "UNIDAD");

$errores = [];
$mensaje_ok = "";

$datos_recurso = [
    "titulo" => "",
    "descripcion" => "",
    "carpeta" => $carpeta_actual,
    "etiqueta" => "Unidad actual"
];

if (isset($_GET["guardado"]) && $_GET["guardado"] === "ok") {
    $mensaje_ok = "Recurso añadido correctamente.";
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

// Fin consulta de asignatura


// Inicio guardado de recurso

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $datos_recurso["titulo"] = trim($_POST["titulo_recurso"]);
    $datos_recurso["descripcion"] = trim($_POST["descripcion_recurso"]);
    $datos_recurso["carpeta"] = $_POST["carpeta_recurso"];
    $datos_recurso["etiqueta"] = $_POST["etiqueta_recurso"];

    if ($datos_recurso["titulo"] === "") {
        $errores[] = "El título del recurso es obligatorio.";
    }

    if (!in_array($datos_recurso["carpeta"], $carpetas_disponibles, true)) {
        $errores[] = "La carpeta seleccionada no es válida.";
    }

    if (!in_array($datos_recurso["etiqueta"], array_slice($etiquetas_disponibles, 1), true)) {
        $errores[] = "La etiqueta seleccionada no es válida.";
    }

    if ($_FILES["archivo_recurso"]["error"] !== UPLOAD_ERR_OK) {
        $errores[] = "Selecciona un archivo válido.";
    }

    if (count($errores) === 0) {
        $nombre_original = $_FILES["archivo_recurso"]["name"];
        $tamano_archivo = (int) $_FILES["archivo_recurso"]["size"];
        $extension = strtoupper(pathinfo($nombre_original, PATHINFO_EXTENSION));

        $extensiones_permitidas = [
            "PDF",
            "PPTX",
            "DOCX",
            "ZIP",
            "JPG",
            "JPEG",
            "PNG",
            "WEBP",
            "MP4",
            "WEBM"
        ];

        if (!in_array($extension, $extensiones_permitidas, true)) {
            $errores[] = "El tipo de archivo no está permitido.";
        }
    }

    if (count($errores) === 0) {
        $directorio_subidas = __DIR__ . "/uploads/recursos";

        if (!is_dir($directorio_subidas)) {
            $errores[] = "Falta la carpeta DOA/uploads/recursos.";
        }
    }

    if (count($errores) === 0) {
        $nombre_archivo = date("YmdHis") . "_" . $id_asignatura . "_" . limpiar_nombre_archivo($nombre_original);
        $ruta_destino = $directorio_subidas . "/" . $nombre_archivo;
        $url_archivo = "uploads/recursos/" . $nombre_archivo;

        if (!move_uploaded_file($_FILES["archivo_recurso"]["tmp_name"], $ruta_destino)) {
            $errores[] = "No se ha podido guardar el archivo en la carpeta de recursos.";
        }
    }

    if (count($errores) === 0) {
        $insertar_recurso = $pdo->prepare("
            INSERT INTO recursos
                (
                    id_asignatura,
                    id_profesor,
                    titulo,
                    descripcion,
                    carpeta,
                    tipo_recurso,
                    tipo_archivo,
                    etiqueta,
                    tamano_bytes,
                    url_archivo,
                    visible
                )
            VALUES
                (
                    :id_asignatura,
                    :id_profesor,
                    :titulo,
                    :descripcion,
                    :carpeta,
                    :tipo_recurso,
                    :tipo_archivo,
                    :etiqueta,
                    :tamano_bytes,
                    :url_archivo,
                    1
                )
        ");

        $insertar_recurso->execute([
            "id_asignatura" => $id_asignatura,
            "id_profesor" => $id_profesor,
            "titulo" => $datos_recurso["titulo"],
            "descripcion" => $datos_recurso["descripcion"] !== "" ? $datos_recurso["descripcion"] : null,
            "carpeta" => $datos_recurso["carpeta"],
            "tipo_recurso" => obtener_tipo_recurso_bd($extension),
            "tipo_archivo" => $extension,
            "etiqueta" => $datos_recurso["etiqueta"],
            "tamano_bytes" => $tamano_archivo,
            "url_archivo" => $url_archivo
        ]);

        header("Location: recursos_profesor.php?id_asignatura=" . $id_asignatura . "&carpeta=" . urlencode($datos_recurso["carpeta"]) . "&guardado=ok");
        exit;
    }
}

// Fin guardado de recurso


// Inicio consulta de recursos

$condiciones_recursos = [
    "id_asignatura = :id_asignatura",
    "visible = 1",
    "carpeta = :carpeta"
];

$parametros_recursos = [
    "id_asignatura" => $id_asignatura,
    "carpeta" => $carpeta_actual
];

if ($filtro_tipo !== "TODOS") {
    $condiciones_recursos[] = "UPPER(COALESCE(tipo_archivo, tipo_recurso)) = :tipo";
    $parametros_recursos["tipo"] = $filtro_tipo;
}

if ($filtro_etiqueta !== "TODAS") {
    $condiciones_recursos[] = "etiqueta = :etiqueta";
    $parametros_recursos["etiqueta"] = $filtro_etiqueta;
}

$consulta_recursos = $pdo->prepare("
    SELECT
        id_recurso,
        titulo,
        descripcion,
        carpeta,
        tipo_recurso,
        tipo_archivo,
        etiqueta,
        tamano_bytes,
        url_archivo,
        fecha_publicacion
    FROM recursos
    WHERE " . implode(" AND ", $condiciones_recursos) . "
    ORDER BY fecha_publicacion DESC, titulo ASC
");

$consulta_recursos->execute($parametros_recursos);

$recursos = $consulta_recursos->fetchAll();

$url_detalle = "detalle_asignatura_profesor.php?id_asignatura=" . $id_asignatura;
$url_tareas = "listado_tareas_profesor.php?id_asignatura=" . $id_asignatura;
$url_examenes = "examenes_profesor.php?id_asignatura=" . $id_asignatura;
$url_calificaciones = "calificaciones_profesor.php?id_asignatura=" . $id_asignatura;

// Fin consulta de recursos
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Recursos · <?php echo limpiar_texto_doa($asignatura["nombre"]); ?> | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/recursos_alumno.css" rel="stylesheet">
    <link href="css/recursos_profesor.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-recursos-asignatura pagina-recursos-profesor">
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
                            <span class="enlace-volver-asignaturas__icono" aria-hidden="true">
                                <img src="img/iconos/grey-chevron-right.svg" alt="">
                            </span>

                            <span>Volver a detalles de la asignatura</span>
                        </a>

                        <h1><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></h1>

                        <ul class="metadatos-asignatura">
                            <li>
                                <img src="img/iconos/grey-graduation-cap.svg" alt="">
                                <span>
                                    <?php echo limpiar_texto_doa($asignatura["curso"]); ?>
                                    · Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                                </span>
                            </li>

                            <li>
                                <img src="img/iconos/grey-user.svg" alt="">
                                <span><?php echo (int) $asignatura["total_alumnos"]; ?> alumnos</span>
                            </li>

                            <li>
                                <img src="img/iconos/grey-notebook.svg" alt="">
                                <span><?php echo limpiar_texto_doa($asignatura["codigo"]); ?></span>
                            </li>
                        </ul>
                    </div>

                    <div class="cabecera-detalle-asignatura__pestanas">
                        <nav class="pestanas-asignatura" aria-label="Secciones de la asignatura">
                            <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="<?php echo limpiar_texto_doa(construir_url_recursos_profesor($id_asignatura, $carpeta_actual, $filtro_tipo, $filtro_etiqueta)); ?>">
                                Recursos
                            </a>

                            <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_tareas); ?>">
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


                <!-- Inicio formulario de subida -->

                <section class="bloque-recursos">
                    <div class="card tarjeta-subida-recurso">
                        <div class="tarjeta-subida-recurso__cabecera">
                            <div>
                                <h2 class="tarjeta-subida-recurso__titulo">Subir recurso</h2>

                                <p class="tarjeta-subida-recurso__texto">
                                    Añade un nuevo recurso a la biblioteca de la asignatura.
                                </p>
                            </div>
                        </div>

                        <?php if ($mensaje_ok !== "") { ?>
                            <p class="mensaje-recurso-subido">
                                <?php echo limpiar_texto_doa($mensaje_ok); ?>
                            </p>
                        <?php } ?>

                        <?php if (count($errores) > 0) { ?>
                            <div class="mensaje-recurso-subido mensaje-recurso-subido--error">
                                <?php foreach ($errores as $error) { ?>
                                    <p><?php echo limpiar_texto_doa($error); ?></p>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <form class="formulario-recursos-grid" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_asignatura" value="<?php echo $id_asignatura; ?>">

                            <div>
                                <label class="form-label" for="tituloRecurso">Título del recurso *</label>

                                <input
                                    class="input"
                                    id="tituloRecurso"
                                    name="titulo_recurso"
                                    type="text"
                                    placeholder="Ej. Presentación Unidad 03"
                                    value="<?php echo limpiar_texto_doa($datos_recurso["titulo"]); ?>"
                                    required>
                            </div>

                            <div>
                                <label class="form-label" for="archivoRecurso">Archivo *</label>

                                <input
                                    class="input input-archivo-recurso"
                                    id="archivoRecurso"
                                    name="archivo_recurso"
                                    type="file"
                                    required>
                            </div>

                            <div>
                                <label class="form-label" for="carpetaRecurso">Carpeta *</label>

                                <select class="input" id="carpetaRecurso" name="carpeta_recurso" required>
                                    <?php foreach ($carpetas_disponibles as $carpeta_disponible) { ?>
                                        <option
                                            value="<?php echo limpiar_texto_doa($carpeta_disponible); ?>"
                                            <?php echo $datos_recurso["carpeta"] === $carpeta_disponible ? "selected" : ""; ?>>
                                            <?php echo limpiar_texto_doa($carpeta_disponible); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div>
                                <label class="form-label" for="etiquetaRecurso">Etiqueta *</label>

                                <select class="input" id="etiquetaRecurso" name="etiqueta_recurso" required>
                                    <?php foreach (array_slice($etiquetas_disponibles, 1) as $etiqueta_disponible) { ?>
                                        <option
                                            value="<?php echo limpiar_texto_doa($etiqueta_disponible); ?>"
                                            <?php echo $datos_recurso["etiqueta"] === $etiqueta_disponible ? "selected" : ""; ?>>
                                            <?php echo limpiar_texto_doa($etiqueta_disponible); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="campo-recurso-descripcion">
                                <label class="form-label" for="descripcionRecurso">Descripción</label>

                                <textarea
                                    class="input textarea-recurso"
                                    id="descripcionRecurso"
                                    name="descripcion_recurso"
                                    placeholder="Descripción breve del recurso..."><?php echo limpiar_texto_doa($datos_recurso["descripcion"]); ?></textarea>
                            </div>

                            <div class="formulario-recursos-grid__acciones">
                                <button class="btn btn-primary boton-guardar-recurso" type="submit">
                                    Guardar recurso
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Fin formulario de subida -->


                <!-- Inicio biblioteca -->

                <section class="bloque-recursos">
                    <h2>Biblioteca de la asignatura</h2>

                    <div class="biblioteca-layout">
                        <aside class="biblioteca-sidebar" id="menuCarpetasGeneral">
                            <div class="biblioteca-carpeta">
                                <div class="biblioteca-carpeta__titulo">
                                    <img src="img/iconos/grey-notebook.svg" alt="" aria-hidden="true">
                                    TEMARIO
                                </div>

                                <ul class="biblioteca-menu-lista">
                                    <?php foreach (["UNIDAD 01", "UNIDAD 02", "UNIDAD 03"] as $carpeta_temario) { ?>
                                        <li>
                                            <a
                                                class="carpeta-click <?php echo $carpeta_actual === $carpeta_temario ? "activo" : ""; ?>"
                                                href="<?php echo limpiar_texto_doa(construir_url_recursos_profesor($id_asignatura, $carpeta_temario, $filtro_tipo, $filtro_etiqueta)); ?>">
                                                <?php echo limpiar_texto_doa($carpeta_temario); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>

                            <div class="biblioteca-carpeta">
                                <a
                                    class="biblioteca-carpeta__titulo carpeta-click <?php echo $carpeta_actual === "PRÁCTICAS" ? "activo" : ""; ?>"
                                    href="<?php echo limpiar_texto_doa(construir_url_recursos_profesor($id_asignatura, "PRÁCTICAS", $filtro_tipo, $filtro_etiqueta)); ?>">
                                    <img src="img/iconos/grey-file.svg" alt="" aria-hidden="true">
                                    PRÁCTICAS
                                </a>
                            </div>

                            <div class="biblioteca-carpeta">
                                <a
                                    class="biblioteca-carpeta__titulo carpeta-click <?php echo $carpeta_actual === "EXÁMENES" ? "activo" : ""; ?>"
                                    href="<?php echo limpiar_texto_doa(construir_url_recursos_profesor($id_asignatura, "EXÁMENES", $filtro_tipo, $filtro_etiqueta)); ?>">
                                    <img src="img/iconos/grey-check.svg" alt="" aria-hidden="true">
                                    EXÁMENES
                                </a>
                            </div>
                        </aside>

                        <div class="biblioteca-contenido">
                            <div class="biblioteca-toolbar">
                                <button class="biblioteca-breadcrumb" id="btnNavegacionMovil" type="button">
                                    <span id="textoCarpetaPadre" <?php echo $carpeta_es_unidad ? "" : "hidden"; ?>>
                                        TEMARIO
                                    </span>

                                    <img
                                        class="flecha-breadcrumb"
                                        id="flechaBreadcrumb"
                                        src="img/iconos/grey-chevron-right.svg"
                                        alt=""
                                        aria-hidden="true"
                                        <?php echo $carpeta_es_unidad ? "" : "hidden"; ?>>

                                    <strong id="textoBreadcrumb">
                                        <?php echo limpiar_texto_doa($carpeta_actual); ?>
                                    </strong>

                                    <img
                                        class="icono-desplegable-movil"
                                        src="img/iconos/grey-down-arrow.svg"
                                        alt=""
                                        aria-hidden="true">
                                </button>

                                <div class="contenedor-filtros-wrapper">
                                    <button class="btn-filtrar-movil" id="btnFiltrarMovil" type="button">
                                        FILTRAR
                                        <img src="img/iconos/grey-down-arrow.svg" alt="" aria-hidden="true">
                                    </button>

                                    <form class="biblioteca-filtros grupo-filtros" id="contenedorFiltros" method="get">
                                        <input type="hidden" name="id_asignatura" value="<?php echo $id_asignatura; ?>">
                                        <input type="hidden" name="carpeta" value="<?php echo limpiar_texto_doa($carpeta_actual); ?>">

                                        <label class="filtro-select">
                                            <select id="filtroTipo" name="tipo">
                                                <?php foreach ($tipos_disponibles as $tipo_disponible) { ?>
                                                    <option
                                                        value="<?php echo limpiar_texto_doa($tipo_disponible); ?>"
                                                        <?php echo $filtro_tipo === $tipo_disponible ? "selected" : ""; ?>>
                                                        Tipo: <?php echo limpiar_texto_doa(strtolower($tipo_disponible)); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </label>

                                        <label class="filtro-select">
                                            <select id="filtroEtiqueta" name="etiqueta">
                                                <?php foreach ($etiquetas_disponibles as $etiqueta_disponible) { ?>
                                                    <option
                                                        value="<?php echo limpiar_texto_doa($etiqueta_disponible); ?>"
                                                        <?php echo $filtro_etiqueta === $etiqueta_disponible ? "selected" : ""; ?>>
                                                        Etiquetas: <?php echo limpiar_texto_doa(strtolower($etiqueta_disponible)); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </label>

                                        <button class="btn btn-primary boton-filtro-aplicar" type="submit">
                                            Aplicar
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="biblioteca-tabla">
                                <div class="biblioteca-tabla-header">
                                    <span>NOMBRE</span>
                                    <span>TIPO</span>
                                    <span>TAMAÑO</span>
                                    <span>ETIQUETAS</span>
                                    <span>FECHA</span>
                                </div>

                                <div id="cuerpoTablaArchivos">
                                    <?php if (count($recursos) === 0) { ?>
                                        <p class="mensaje-tabla-vacia">
                                            No hay recursos que coincidan con la carpeta y los filtros seleccionados.
                                        </p>
                                    <?php } ?>

                                    <?php foreach ($recursos as $recurso) { ?>
                                        <?php
                                        $tipo_visible = obtener_tipo_recurso_visible_profesor($recurso);
                                        $tamano_visible = formatear_tamano_recurso_profesor($recurso["tamano_bytes"]);
                                        $etiqueta = $recurso["etiqueta"] ?? "Material";
                                        $clase_etiqueta = obtener_clase_etiqueta_recurso_profesor($etiqueta);
                                        ?>

                                        <div class="biblioteca-tabla-fila archivo-fila">
                                            <a href="<?php echo limpiar_texto_doa($recurso["url_archivo"]); ?>" class="nombre-archivo">
                                                <?php echo limpiar_texto_doa($recurso["titulo"]); ?>
                                            </a>

                                            <span class="col-tipo">
                                                <?php echo limpiar_texto_doa($tipo_visible); ?>
                                            </span>

                                            <span class="col-movil-oculta">
                                                <?php echo limpiar_texto_doa($tamano_visible); ?>
                                            </span>

                                            <span class="col-movil-oculta">
                                                <span class="badge-etiqueta <?php echo limpiar_texto_doa($clase_etiqueta); ?>">
                                                    <?php echo limpiar_texto_doa($etiqueta); ?>
                                                </span>
                                            </span>

                                            <span class="col-movil-oculta">
                                                <?php echo date("d/m/Y", strtotime($recurso["fecha_publicacion"])); ?>
                                            </span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Fin biblioteca -->
            </section>
        </main>

        <!-- Fin contenido principal -->
    </div>

    <script src="js/recursos_profesor.js"></script>
</body>

</html>