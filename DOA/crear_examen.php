<?php
// Inicio configuración de página

$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar examen...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

// Fin configuración de página


// Inicio parámetros de pantalla

if (!isset($_GET["id_asignatura"]) && !isset($_POST["id_asignatura"])) {
    header("Location: asignaturas_profesor.php");
    exit;
}

$id_profesor = (int) $_SESSION["doa_id_usuario"];
$id_asignatura = isset($_POST["id_asignatura"])
    ? (int) $_POST["id_asignatura"]
    : (int) $_GET["id_asignatura"];

$errores = [];
$mensaje_ok = "";

$datos_examen = [
    "titulo" => "",
    "unidad" => "Unidad 03",
    "descripcion" => "",
    "temas" => "",
    "fecha_inicio" => date("Y-m-d"),
    "fecha_limite" => "",
    "duracion_minutos" => "45",
    "intentos_maximos" => "1",
    "estado" => "publicada"
];

$preguntas_formulario = [
    [
        "enunciado" => "",
        "opciones" => ["", "", ""],
        "correcta" => 0,
        "explicacion" => ""
    ]
];

if (isset($_GET["guardado"]) && $_GET["guardado"] === "ok") {
    $mensaje_ok = "Examen guardado correctamente.";
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


// Inicio guardado de examen

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $datos_examen["titulo"] = trim($_POST["titulo"]);
    $datos_examen["unidad"] = trim($_POST["unidad"]);
    $datos_examen["descripcion"] = trim($_POST["descripcion"]);
    $datos_examen["temas"] = trim($_POST["temas"]);
    $datos_examen["fecha_inicio"] = $_POST["fecha_inicio"];
    $datos_examen["fecha_limite"] = $_POST["fecha_limite"];
    $datos_examen["duracion_minutos"] = trim($_POST["duracion_minutos"]);
    $datos_examen["intentos_maximos"] = trim($_POST["intentos_maximos"]);
    $datos_examen["estado"] = $_POST["estado"];

    $preguntas_formulario = [];

    foreach ($_POST["preguntas"] ?? [] as $pregunta_post) {
        $opciones = $pregunta_post["opciones"] ?? ["", "", ""];

        $preguntas_formulario[] = [
            "enunciado" => trim($pregunta_post["enunciado"] ?? ""),
            "opciones" => [
                trim($opciones[0] ?? ""),
                trim($opciones[1] ?? ""),
                trim($opciones[2] ?? "")
            ],
            "correcta" => isset($pregunta_post["correcta"]) ? (int) $pregunta_post["correcta"] : -1,
            "explicacion" => trim($pregunta_post["explicacion"] ?? "")
        ];
    }

    if ($datos_examen["titulo"] === "") {
        $errores[] = "El nombre del examen es obligatorio.";
    }

    if ($datos_examen["unidad"] === "") {
        $errores[] = "La unidad es obligatoria.";
    }

    if ($datos_examen["descripcion"] === "") {
        $errores[] = "La descripción o instrucciones son obligatorias.";
    }

    if ($datos_examen["temas"] === "") {
        $errores[] = "El contenido evaluado es obligatorio.";
    }

    if ($datos_examen["fecha_inicio"] === "") {
        $errores[] = "La fecha de apertura es obligatoria.";
    }

    if ($datos_examen["fecha_limite"] === "") {
        $errores[] = "La fecha de cierre es obligatoria.";
    }

    if ($datos_examen["fecha_inicio"] !== "" && $datos_examen["fecha_limite"] !== "" && $datos_examen["fecha_limite"] < $datos_examen["fecha_inicio"]) {
        $errores[] = "La fecha de cierre no puede ser anterior a la fecha de apertura.";
    }

    if (!ctype_digit($datos_examen["duracion_minutos"]) || (int) $datos_examen["duracion_minutos"] < 1) {
        $errores[] = "La duración debe ser un número mayor que 0.";
    }

    if (!ctype_digit($datos_examen["intentos_maximos"]) || (int) $datos_examen["intentos_maximos"] < 1) {
        $errores[] = "Los intentos permitidos deben ser un número mayor que 0.";
    }

    if (!in_array($datos_examen["estado"], ["publicada", "borrador"], true)) {
        $errores[] = "El estado seleccionado no es válido.";
    }

    if (count($preguntas_formulario) === 0) {
        $errores[] = "El examen debe tener al menos una pregunta.";
    }

    foreach ($preguntas_formulario as $indice => $pregunta) {
        $numero_pregunta = $indice + 1;

        if ($pregunta["enunciado"] === "") {
            $errores[] = "La pregunta " . $numero_pregunta . " no tiene enunciado.";
        }

        foreach ($pregunta["opciones"] as $indice_opcion => $opcion) {
            if ($opcion === "") {
                $errores[] = "La opción " . ($indice_opcion + 1) . " de la pregunta " . $numero_pregunta . " está vacía.";
            }
        }

        if (!in_array($pregunta["correcta"], [0, 1, 2], true)) {
            $errores[] = "La pregunta " . $numero_pregunta . " no tiene respuesta correcta marcada.";
        }

        if ($pregunta["explicacion"] === "") {
            $errores[] = "La pregunta " . $numero_pregunta . " no tiene explicación.";
        }
    }

    if (count($errores) === 0) {
        $visible = $datos_examen["estado"] === "publicada" ? 1 : 0;
        $puntuacion_pregunta = round(10 / count($preguntas_formulario), 2);

        $pdo->beginTransaction();

        try {
            $insertar_examen = $pdo->prepare("
                INSERT INTO actividades_evaluables
                    (
                        id_asignatura,
                        id_profesor,
                        tipo_actividad,
                        unidad,
                        titulo,
                        descripcion,
                        temas,
                        fecha_inicio,
                        fecha_limite,
                        duracion_minutos,
                        intentos_maximos,
                        puntuacion_maxima,
                        visible,
                        estado
                    )
                VALUES
                    (
                        :id_asignatura,
                        :id_profesor,
                        'examen',
                        :unidad,
                        :titulo,
                        :descripcion,
                        :temas,
                        :fecha_inicio,
                        :fecha_limite,
                        :duracion_minutos,
                        :intentos_maximos,
                        10.00,
                        :visible,
                        :estado
                    )
            ");

            $insertar_examen->execute([
                "id_asignatura" => $id_asignatura,
                "id_profesor" => $id_profesor,
                "unidad" => $datos_examen["unidad"],
                "titulo" => $datos_examen["titulo"],
                "descripcion" => $datos_examen["descripcion"],
                "temas" => $datos_examen["temas"],
                "fecha_inicio" => $datos_examen["fecha_inicio"] . " 00:00:00",
                "fecha_limite" => $datos_examen["fecha_limite"] . " 23:59:59",
                "duracion_minutos" => (int) $datos_examen["duracion_minutos"],
                "intentos_maximos" => (int) $datos_examen["intentos_maximos"],
                "visible" => $visible,
                "estado" => $datos_examen["estado"]
            ]);

            $id_actividad = (int) $pdo->lastInsertId();

            $insertar_pregunta = $pdo->prepare("
                INSERT INTO preguntas_examen
                    (id_actividad, enunciado, tipo_pregunta, orden, puntuacion, explicacion)
                VALUES
                    (:id_actividad, :enunciado, 'test', :orden, :puntuacion, :explicacion)
            ");

            $insertar_opcion = $pdo->prepare("
                INSERT INTO opciones_pregunta
                    (id_pregunta, texto, es_correcta, orden)
                VALUES
                    (:id_pregunta, :texto, :es_correcta, :orden)
            ");

            foreach ($preguntas_formulario as $indice => $pregunta) {
                $insertar_pregunta->execute([
                    "id_actividad" => $id_actividad,
                    "enunciado" => $pregunta["enunciado"],
                    "orden" => $indice + 1,
                    "puntuacion" => $puntuacion_pregunta,
                    "explicacion" => $pregunta["explicacion"]
                ]);

                $id_pregunta = (int) $pdo->lastInsertId();

                foreach ($pregunta["opciones"] as $indice_opcion => $opcion) {
                    $insertar_opcion->execute([
                        "id_pregunta" => $id_pregunta,
                        "texto" => $opcion,
                        "es_correcta" => $pregunta["correcta"] === $indice_opcion ? 1 : 0,
                        "orden" => $indice_opcion + 1
                    ]);
                }
            }

            $pdo->commit();

            header("Location: examenes_profesor.php?id_asignatura=" . $id_asignatura . "&creado=ok");
            exit;
        } catch (Throwable $error) {
            $pdo->rollBack();
            throw $error;
        }
    }
}

// Fin guardado de examen


// Inicio enlaces de navegación

$url_detalle = "detalle_asignatura_profesor.php?id_asignatura=" . $id_asignatura;
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

    <title>Crear examen | DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/detalle_asignatura.css" rel="stylesheet">
    <link href="css/crear_examen.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fin metadatos y estilos -->
</head>

<body class="pagina-doa pagina-crear-examen">
    <!-- Inicio cabecera -->

    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <!-- Fin cabecera -->

    <div class="layout-doa">
        <!-- Inicio barra lateral -->

        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <!-- Fin barra lateral -->


        <!-- Inicio contenido principal -->

        <main class="contenido-doa">
            <section class="cabecera-detalle-asignatura">
                <div class="cabecera-detalle-asignatura__texto">
                    <a class="enlace-volver-asignaturas" href="<?php echo limpiar_texto_doa($url_examenes); ?>">
                        <span class="enlace-volver-asignaturas__icono" aria-hidden="true">
                            <img src="img/iconos/grey-chevron-right.svg" alt="">
                        </span>

                        <span>Volver a exámenes</span>
                    </a>

                    <h1>Crear examen</h1>

                    <ul class="metadatos-asignatura">
                        <li>
                            <img src="img/iconos/grey-notebook.svg" alt="">
                            <span><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></span>
                        </li>

                        <li>
                            <img src="img/iconos/grey-graduation-cap.svg" alt="">
                            <span>
                                <?php echo limpiar_texto_doa($asignatura["curso"]); ?>
                                · Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                            </span>
                        </li>

                        <li>
                            <img src="img/iconos/grey-calendar.svg" alt="">
                            <span><?php echo (int) $asignatura["total_alumnos"]; ?> alumnos</span>
                        </li>
                    </ul>
                </div>

                <div class="cabecera-detalle-asignatura__pestanas">
                    <nav class="pestanas-asignatura" aria-label="Secciones de la asignatura">
                        <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_recursos); ?>">
                            Recursos
                        </a>

                        <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_tareas); ?>">
                            Tareas
                        </a>

                        <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="<?php echo limpiar_texto_doa($url_examenes); ?>">
                            Exámenes
                        </a>

                        <a class="pestanas-asignatura__item" href="<?php echo limpiar_texto_doa($url_calificaciones); ?>">
                            Calificaciones
                        </a>
                    </nav>
                </div>
            </section>

            <!-- Inicio formulario de examen -->

            <form class="crear-examen-grid" id="formularioCrearExamen" method="post">
                <input type="hidden" name="id_asignatura" value="<?php echo $id_asignatura; ?>">

                <section class="columna-principal-formulario">
                    <article class="tarjeta-formulario-examen">
                        <h2>Configuración del examen</h2>

                        <?php if (count($errores) > 0) { ?>
                            <div class="mensaje-examen-guardado mensaje-examen-guardado--error">
                                <?php foreach ($errores as $error) { ?>
                                    <p><?php echo limpiar_texto_doa($error); ?></p>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <?php if ($mensaje_ok !== "") { ?>
                            <p class="mensaje-examen-guardado">
                                <?php echo limpiar_texto_doa($mensaje_ok); ?>
                            </p>
                        <?php } ?>

                        <div class="grupo-campo-formulario">
                            <label for="inputNombre">Nombre del examen</label>

                            <input
                                id="inputNombre"
                                name="titulo"
                                type="text"
                                placeholder="Ej. Parcial 02"
                                value="<?php echo limpiar_texto_doa($datos_examen["titulo"]); ?>"
                                required>
                        </div>

                        <div class="formulario-doble">
                            <div class="grupo-campo-formulario">
                                <label for="inputUnidad">Unidad</label>

                                <input
                                    id="inputUnidad"
                                    name="unidad"
                                    type="text"
                                    placeholder="Ej. Unidad 03"
                                    value="<?php echo limpiar_texto_doa($datos_examen["unidad"]); ?>"
                                    required>
                            </div>

                            <div class="grupo-campo-formulario">
                                <label for="selectEstadoExamen">Estado</label>

                                <select id="selectEstadoExamen" name="estado" required>
                                    <option value="publicada" <?php echo $datos_examen["estado"] === "publicada" ? "selected" : ""; ?>>
                                        Publicado
                                    </option>

                                    <option value="borrador" <?php echo $datos_examen["estado"] === "borrador" ? "selected" : ""; ?>>
                                        Borrador
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="grupo-campo-formulario">
                            <label for="inputTemas">Contenido evaluado</label>

                            <textarea
                                id="inputTemas"
                                name="temas"
                                rows="3"
                                placeholder="Ej. Recursividad, arrays, estructuras dinámicas..."
                                required><?php echo limpiar_texto_doa($datos_examen["temas"]); ?></textarea>
                        </div>

                        <div class="grupo-campo-formulario grupo-campo-sin-margen">
                            <label for="inputDescripcion">Descripción o instrucciones</label>

                            <textarea
                                id="inputDescripcion"
                                name="descripcion"
                                rows="3"
                                placeholder="Indica las instrucciones que verá el alumnado..."
                                required><?php echo limpiar_texto_doa($datos_examen["descripcion"]); ?></textarea>
                        </div>
                    </article>

                    <article class="tarjeta-formulario-examen">
                        <h2>Ajustes de evaluación</h2>

                        <div class="formulario-doble">
                            <div class="grupo-campo-formulario">
                                <label for="inputFechaApertura">Fecha de apertura</label>

                                <input
                                    id="inputFechaApertura"
                                    name="fecha_inicio"
                                    type="date"
                                    value="<?php echo limpiar_texto_doa($datos_examen["fecha_inicio"]); ?>"
                                    required>
                            </div>

                            <div class="grupo-campo-formulario">
                                <label for="inputFechaCierre">Fecha de cierre</label>

                                <input
                                    id="inputFechaCierre"
                                    name="fecha_limite"
                                    type="date"
                                    value="<?php echo limpiar_texto_doa($datos_examen["fecha_limite"]); ?>"
                                    required>
                            </div>
                        </div>

                        <div class="formulario-doble">
                            <div class="grupo-campo-formulario">
                                <label for="inputDuracion">Duración en minutos</label>

                                <input
                                    id="inputDuracion"
                                    name="duracion_minutos"
                                    type="number"
                                    min="1"
                                    value="<?php echo limpiar_texto_doa($datos_examen["duracion_minutos"]); ?>"
                                    required>
                            </div>

                            <div class="grupo-campo-formulario">
                                <label for="inputIntentos">Intentos permitidos</label>

                                <input
                                    id="inputIntentos"
                                    name="intentos_maximos"
                                    type="number"
                                    min="1"
                                    value="<?php echo limpiar_texto_doa($datos_examen["intentos_maximos"]); ?>"
                                    required>
                            </div>
                        </div>
                    </article>

                    <article class="tarjeta-formulario-examen">
                        <div class="cabecera-preguntas-crear">
                            <div>
                                <h2>Preguntas del examen</h2>
                                <p>Añade preguntas tipo test y marca la respuesta correcta.</p>
                            </div>
                        </div>

                        <div class="contenedor-preguntas-crear" id="contenedorPreguntas">
                            <?php foreach ($preguntas_formulario as $indice => $pregunta) { ?>
                                <article class="bloque-pregunta-crear">
                                    <div class="cabecera-pregunta-crear">
                                        <h3>Pregunta <span class="numero-pregunta-crear"><?php echo $indice + 1; ?></span></h3>
                                        <button class="btn-eliminar-pregunta" type="button">Eliminar</button>
                                    </div>

                                    <div class="grupo-campo-formulario">
                                        <label>Enunciado de la pregunta</label>

                                        <textarea
                                            class="input-enunciado"
                                            name="preguntas[<?php echo $indice; ?>][enunciado]"
                                            rows="2"
                                            placeholder="Escribe el enunciado..."
                                            required><?php echo limpiar_texto_doa($pregunta["enunciado"]); ?></textarea>
                                    </div>

                                    <div class="opciones-contenedor">
                                        <label class="label-secundario">Opciones</label>

                                        <?php foreach ($pregunta["opciones"] as $indice_opcion => $opcion) { ?>
                                            <div class="fila-opcion-crear">
                                                <input
                                                    type="radio"
                                                    name="preguntas[<?php echo $indice; ?>][correcta]"
                                                    value="<?php echo $indice_opcion; ?>"
                                                    <?php echo (int) $pregunta["correcta"] === $indice_opcion ? "checked" : ""; ?>
                                                    required>

                                                <input
                                                    type="text"
                                                    name="preguntas[<?php echo $indice; ?>][opciones][]"
                                                    placeholder="Opción <?php echo $indice_opcion + 1; ?>"
                                                    value="<?php echo limpiar_texto_doa($opcion); ?>"
                                                    required>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="grupo-campo-formulario grupo-campo-sin-margen">
                                        <label>Explicación de la respuesta</label>

                                        <input
                                            class="input-explicacion"
                                            name="preguntas[<?php echo $indice; ?>][explicacion]"
                                            type="text"
                                            placeholder="Explica por qué esta es la respuesta correcta..."
                                            value="<?php echo limpiar_texto_doa($pregunta["explicacion"]); ?>"
                                            required>
                                    </div>
                                </article>
                            <?php } ?>
                        </div>

                        <button class="boton-anadir-pregunta" id="btnAnadirPregunta" type="button">
                            Añadir pregunta
                        </button>
                    </article>
                </section>

                <aside class="columna-lateral-formulario">
                    <article class="tarjeta-publicacion-examen">
                        <h2>Publicación</h2>

                        <p>
                            Guarda el examen en la base de datos. Si está publicado, aparecerá en la vista del alumnado.
                        </p>

                        <ul class="resumen-publicacion-examen">
                            <li>
                                <span>Asignatura</span>
                                <strong><?php echo limpiar_texto_doa($asignatura["nombre"]); ?></strong>
                            </li>

                            <li>
                                <span>Preguntas</span>
                                <strong id="resumenPreguntasExamen"><?php echo count($preguntas_formulario); ?></strong>
                            </li>
                        </ul>

                        <button class="boton-publicar-examen" type="submit">
                            Guardar examen
                        </button>

                        <a class="boton-descartar-examen" href="<?php echo limpiar_texto_doa($url_examenes); ?>">
                            Descartar
                        </a>
                    </article>
                </aside>
            </form>

            <!-- Fin formulario de examen -->
        </main>

        <!-- Fin contenido principal -->
    </div>

    <script src="js/crear_examen.js"></script>
</body>

</html>