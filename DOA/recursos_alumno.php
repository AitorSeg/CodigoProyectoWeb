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

function construir_url_recursos($id_asignatura, $carpeta, $filtro_tipo, $filtro_etiqueta)
{
    return "recursos_alumno.php?" . http_build_query([
        "id_asignatura" => $id_asignatura,
        "carpeta" => $carpeta,
        "tipo" => $filtro_tipo,
        "etiqueta" => $filtro_etiqueta
    ]);
}

function formatear_tamano_recurso($tamano_bytes)
{
    if ($tamano_bytes === null) {
        return "-";
    }

    if ((int) $tamano_bytes >= 1048576) {
        return round((int) $tamano_bytes / 1048576, 1) . " MB";
    }

    return round((int) $tamano_bytes / 1024, 1) . " KB";
}

function obtener_tipo_recurso_visible($recurso)
{
    if ($recurso["tipo_archivo"] !== null && $recurso["tipo_archivo"] !== "") {
        return strtoupper($recurso["tipo_archivo"]);
    }

    return ucfirst($recurso["tipo_recurso"]);
}

function obtener_clase_etiqueta_recurso($etiqueta)
{
    return match ($etiqueta) {
        "Unidad actual" => "etiqueta-actual",
        "Práctica", "Importante" => "etiqueta-importante",
        "Nuevo" => "etiqueta-success",
        default => "etiqueta-neutral",
    };
}

// Fin funciones auxiliares


// Inicio parámetros de pantalla

if (!isset($_GET["id_asignatura"])) {
    header("Location: asignaturas.php");
    exit;
}

$id_alumno = (int) $_SESSION["doa_id_usuario"];
$id_asignatura = (int) $_GET["id_asignatura"];

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

// Fin parámetros de pantalla


// Inicio consultas a base de datos

$consulta_asignatura = $pdo->prepare("
    SELECT
        a.id_asignatura,
        a.nombre,
        a.codigo,
        a.curso,
        a.grupo,
        GROUP_CONCAT(
            DISTINCT CONCAT(u_profesor.nombre, ' ', u_profesor.apellidos)
            SEPARATOR ', '
        ) AS profesores
    FROM asignaturas a
    INNER JOIN usuarios_asignaturas ua_alumno
        ON ua_alumno.id_asignatura = a.id_asignatura
        AND ua_alumno.id_usuario = :id_alumno
        AND ua_alumno.rol_asignatura = 'alumno'
        AND ua_alumno.estado = 'activa'
    LEFT JOIN usuarios_asignaturas ua_profesor
        ON ua_profesor.id_asignatura = a.id_asignatura
        AND ua_profesor.rol_asignatura = 'profesor'
        AND ua_profesor.estado = 'activa'
    LEFT JOIN usuarios u_profesor
        ON u_profesor.id_usuario = ua_profesor.id_usuario
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
    "id_alumno" => $id_alumno,
    "id_asignatura" => $id_asignatura
]);

$asignatura = $consulta_asignatura->fetch();

if (!$asignatura) {
    header("Location: asignaturas.php");
    exit;
}

$consulta_recursos_destacados = $pdo->prepare("
    SELECT
        id_recurso,
        titulo,
        tipo_recurso,
        tipo_archivo,
        etiqueta,
        tamano_bytes,
        url_archivo,
        url_externa,
        fecha_publicacion
    FROM recursos
    WHERE id_asignatura = :id_asignatura
    AND visible = 1
    AND carpeta = 'UNIDAD 03'
    ORDER BY fecha_publicacion DESC
    LIMIT 2
");

$consulta_recursos_destacados->execute([
    "id_asignatura" => $id_asignatura
]);

$recursos_destacados = $consulta_recursos_destacados->fetchAll();

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
        url_externa,
        fecha_publicacion
    FROM recursos
    WHERE " . implode(" AND ", $condiciones_recursos) . "
    ORDER BY fecha_publicacion DESC, titulo ASC
");

$consulta_recursos->execute($parametros_recursos);

$recursos = $consulta_recursos->fetchAll();

$profesores = $asignatura["profesores"] !== null ? $asignatura["profesores"] : "Pendiente";

$url_detalle = "detalle_asignatura.php?id_asignatura=" . $id_asignatura;
$url_tareas = "listado_tareas.php?id_asignatura=" . $id_asignatura;
$url_examenes = "examenes.php?id_asignatura=" . $id_asignatura;
$url_calificaciones = "calificaciones.php?id_asignatura=" . $id_asignatura;

// Fin consultas a base de datos
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Inicio metadatos y estilos -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos · <?php echo limpiar_texto_doa($asignatura["nombre"]); ?> | DOA</title>

    <link rel="stylesheet" href="css/doa.css">
    <link rel="stylesheet" href="css/doa_layout.css">
    <link rel="stylesheet" href="css/doa_componentes.css">
    <link rel="stylesheet" href="css/detalle_asignatura.css">
    <link rel="stylesheet" href="css/recursos_alumno.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-recursos-asignatura">
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
                        <a href="<?php echo limpiar_texto_doa($url_detalle); ?>" class="enlace-volver-asignaturas">
                            <span class="enlace-volver-asignaturas__icono" aria-hidden="true">
                                <img src="img/iconos/grey-chevron-right.svg" alt="">
                            </span>

                            <span>Volver a detalles de la asignatura</span>
                        </a>

                        <h1><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></h1>

                        <ul class="metadatos-asignatura">
                            <li>
                                <img src="img/iconos/grey-user.svg" alt="">
                                <span><?php echo limpiar_texto_doa($profesores); ?></span>
                            </li>

                            <li>
                                <img src="img/iconos/grey-notebook.svg" alt="">
                                <span>
                                    <?php echo limpiar_texto_doa($asignatura["codigo"]); ?>
                                    · <?php echo limpiar_texto_doa($asignatura["curso"]); ?>
                                    · Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div class="cabecera-detalle-asignatura__pestanas">
                        <nav class="pestanas-asignatura" aria-label="Secciones de la asignatura">
                            <a href="recursos_alumno.php?id_asignatura=<?php echo $id_asignatura; ?>" class="pestanas-asignatura__item pestanas-asignatura__item--activo">
                                Recursos
                            </a>

                            <a href="<?php echo limpiar_texto_doa($url_tareas); ?>" class="pestanas-asignatura__item">
                                Tareas
                            </a>

                            <a href="<?php echo limpiar_texto_doa($url_examenes); ?>" class="pestanas-asignatura__item">
                                Exámenes
                            </a>

                            <a href="<?php echo limpiar_texto_doa($url_calificaciones); ?>" class="pestanas-asignatura__item">
                                Calificaciones
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Fin cabecera de asignatura -->


                <!-- Inicio recursos destacados -->

                <section class="bloque-recursos">
                    <div class="recursos-tema-cabecera">
                        <h2>Recursos del tema actual</h2>

                        <a href="<?php echo limpiar_texto_doa(construir_url_recursos($id_asignatura, "UNIDAD 03", "TODOS", "TODAS")); ?>" class="enlace-ver-todos">
                            Ver todos<span class="ocultar-movil"> los recursos del tema</span>
                        </a>
                    </div>

                    <div class="grid-recursos-tema">
                        <?php if (count($recursos_destacados) === 0) { ?>
                            <article class="tarjeta-recurso-mini">
                                <div class="tarjeta-recurso-mini__icono" aria-hidden="true">
                                    <img src="img/iconos/grey-file.svg" alt="">
                                </div>

                                <div class="tarjeta-recurso-mini__info">
                                    <h4>Sin recursos</h4>
                                    <span>Pendiente de publicar.</span>
                                </div>
                            </article>
                        <?php } ?>

                        <?php foreach ($recursos_destacados as $recurso_destacado) { ?>
                            <?php
                            $tipo_visible = obtener_tipo_recurso_visible($recurso_destacado);
                            $url_recurso = $recurso_destacado["url_externa"] ?: $recurso_destacado["url_archivo"];
                            $icono_recurso = $recurso_destacado["tipo_recurso"] === "video" ? "grey-play.svg" : "grey-file.svg";
                            ?>

                            <a href="<?php echo limpiar_texto_doa($url_recurso ?: "#"); ?>" class="tarjeta-recurso-mini">
                                <div class="tarjeta-recurso-mini__icono" aria-hidden="true">
                                    <img src="img/iconos/<?php echo limpiar_texto_doa($icono_recurso); ?>" alt="">
                                </div>

                                <div class="tarjeta-recurso-mini__info">
                                    <h4><?php echo limpiar_texto_doa($recurso_destacado["titulo"]); ?></h4>
                                    <span>
                                        <?php echo limpiar_texto_doa($tipo_visible); ?>
                                        · <?php echo limpiar_texto_doa($recurso_destacado["etiqueta"] ?? "Material"); ?>
                                    </span>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </section>

                <!-- Fin recursos destacados -->


                <!-- Inicio biblioteca -->

                <section class="bloque-recursos">
                    <h2>Biblioteca</h2>

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
                                                href="<?php echo limpiar_texto_doa(construir_url_recursos($id_asignatura, $carpeta_temario, $filtro_tipo, $filtro_etiqueta)); ?>"
                                                class="carpeta-click <?php echo $carpeta_actual === $carpeta_temario ? "activo" : ""; ?>">
                                                <?php echo limpiar_texto_doa($carpeta_temario); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>

                            <div class="biblioteca-carpeta">
                                <a
                                    class="biblioteca-carpeta__titulo carpeta-click <?php echo $carpeta_actual === "PRÁCTICAS" ? "activo" : ""; ?>"
                                    href="<?php echo limpiar_texto_doa(construir_url_recursos($id_asignatura, "PRÁCTICAS", $filtro_tipo, $filtro_etiqueta)); ?>">
                                    <img src="img/iconos/grey-file.svg" alt="" aria-hidden="true">
                                    PRÁCTICAS
                                </a>
                            </div>

                            <div class="biblioteca-carpeta">
                                <a
                                    class="biblioteca-carpeta__titulo carpeta-click <?php echo $carpeta_actual === "EXÁMENES" ? "activo" : ""; ?>"
                                    href="<?php echo limpiar_texto_doa(construir_url_recursos($id_asignatura, "EXÁMENES", $filtro_tipo, $filtro_etiqueta)); ?>">
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
                                        src="img/iconos/grey-chevron-right.svg"
                                        alt=""
                                        class="flecha-breadcrumb"
                                        id="flechaBreadcrumb"
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
                                        $tipo_visible = obtener_tipo_recurso_visible($recurso);
                                        $tamano_visible = formatear_tamano_recurso($recurso["tamano_bytes"]);
                                        $etiqueta = $recurso["etiqueta"] ?? "Material";
                                        $clase_etiqueta = obtener_clase_etiqueta_recurso($etiqueta);
                                        $url_recurso = $recurso["url_externa"] ?: $recurso["url_archivo"];
                                        ?>

                                        <div class="biblioteca-tabla-fila archivo-fila">
                                            <?php if ($url_recurso !== null && $url_recurso !== "") { ?>
                                                <a href="<?php echo limpiar_texto_doa($url_recurso); ?>" class="nombre-archivo">
                                                    <?php echo limpiar_texto_doa($recurso["titulo"]); ?>
                                                </a>
                                            <?php } else { ?>
                                                <span class="nombre-archivo">
                                                    <?php echo limpiar_texto_doa($recurso["titulo"]); ?>
                                                </span>
                                            <?php } ?>

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

    <script src="js/recursos_alumno.js"></script>
</body>

</html>