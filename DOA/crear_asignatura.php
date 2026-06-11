<?php
$rol_pagina = "secretaria";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_secretaria.php";
$placeholder_buscador = "Buscar asignatura, profesor, alumno...";

require_once __DIR__ . "/includes/proteger_doa.php";
require_once __DIR__ . "/../config/conexion.php";

$errores = [];

$datos_asignatura = [
    "nombre" => "",
    "codigo" => "",
    "curso" => "",
    "grupo" => "",
    "descripcion" => "",
    "id_profesor" => ""
];

$consulta_profesores = $pdo->query("
    SELECT id_usuario, nombre, apellidos, email, tipo_usuario
    FROM usuarios
    WHERE rol = 'profesor'
    AND estado = 'activo'
    ORDER BY nombre ASC, apellidos ASC
");

$profesores = $consulta_profesores->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $datos_asignatura["nombre"] = trim($_POST["nombre"]);
    $datos_asignatura["codigo"] = strtoupper(trim($_POST["codigo"]));
    $datos_asignatura["curso"] = trim($_POST["curso"]);
    $datos_asignatura["grupo"] = trim($_POST["grupo"]);
    $datos_asignatura["descripcion"] = trim($_POST["descripcion"]);
    $datos_asignatura["id_profesor"] = $_POST["id_profesor"] !== "" ? (int) $_POST["id_profesor"] : "";

    if ($datos_asignatura["nombre"] === "") {
        $errores["nombre"] = "El nombre de la asignatura es obligatorio.";
    }

    if ($datos_asignatura["codigo"] === "") {
        $errores["codigo"] = "El código de la asignatura es obligatorio.";
    }

    if ($datos_asignatura["curso"] === "") {
        $errores["curso"] = "Selecciona un curso.";
    }

    if ($datos_asignatura["grupo"] === "") {
        $errores["grupo"] = "Selecciona un grupo.";
    }

    $ids_profesores_validos = array_map("intval", array_column($profesores, "id_usuario"));

    if ($datos_asignatura["id_profesor"] !== "" && !in_array($datos_asignatura["id_profesor"], $ids_profesores_validos, true)) {
        $errores["id_profesor"] = "El profesor seleccionado no es válido.";
    }

    if (count($errores) === 0) {
        $consulta_codigo = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM asignaturas
            WHERE codigo = :codigo
        ");

        $consulta_codigo->execute([
            "codigo" => $datos_asignatura["codigo"]
        ]);

        $codigo_existente = $consulta_codigo->fetch();

        if ((int) $codigo_existente["total"] > 0) {
            $errores["codigo"] = "Ya existe una asignatura con ese código.";
        }
    }

    if (count($errores) === 0) {
        $pdo->beginTransaction();

        try {
            $insertar_asignatura = $pdo->prepare("
                INSERT INTO asignaturas
                    (nombre, codigo, descripcion, curso, grupo, estado, id_usuario_creador)
                VALUES
                    (:nombre, :codigo, :descripcion, :curso, :grupo, 'pendiente', :id_usuario_creador)
            ");

            $insertar_asignatura->execute([
                "nombre" => $datos_asignatura["nombre"],
                "codigo" => $datos_asignatura["codigo"],
                "descripcion" => $datos_asignatura["descripcion"] !== "" ? $datos_asignatura["descripcion"] : null,
                "curso" => $datos_asignatura["curso"],
                "grupo" => $datos_asignatura["grupo"],
                "id_usuario_creador" => $_SESSION["doa_id_usuario"]
            ]);

            $id_asignatura_creada = (int) $pdo->lastInsertId();

            if ($datos_asignatura["id_profesor"] !== "") {
                $insertar_profesor = $pdo->prepare("
                    INSERT INTO usuarios_asignaturas
                        (id_usuario, id_asignatura, rol_asignatura, estado)
                    VALUES
                        (:id_usuario, :id_asignatura, 'profesor', 'activa')
                ");

                $insertar_profesor->execute([
                    "id_usuario" => $datos_asignatura["id_profesor"],
                    "id_asignatura" => $id_asignatura_creada
                ]);
            }

            $pdo->commit();

            header("Location: asignaturas_secretaria.php?creada=ok");
            exit;
        } catch (Throwable $error) {
            $pdo->rollBack();
            throw $error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Crear asignatura | Secretaría DOA</title>

    <link href="css/doa.css" rel="stylesheet">
    <link href="css/doa_layout.css" rel="stylesheet">
    <link href="css/doa_componentes.css" rel="stylesheet">
    <link href="css/secretaria.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-secretaria pagina-crear-asignatura-secretaria">
    <?php require_once __DIR__ . "/includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php require_once __DIR__ . "/includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa contenido-secretaria">
            <section class="cabecera-secretaria">
                <div class="cabecera-secretaria__texto">
                    <a class="enlace-volver-secretaria" href="asignaturas_secretaria.php">
                        <span aria-hidden="true" class="enlace-volver-secretaria__icono">
                            <img alt="" src="img/iconos/grey-chevron-right.svg">
                        </span>

                        <span>Volver a asignaturas</span>
                    </a>

                    <p class="cabecera-secretaria__eyebrow">Gestión académica</p>

                    <h1>Crear asignatura</h1>

                    <p>
                        Da de alta una nueva asignatura o grupo. Puedes asignar un profesor responsable ahora y completar la asignación de alumnos más tarde.
                    </p>
                </div>
            </section>

            <div class="grid-formulario-secretaria">
                <section class="bloque-secretaria">
                    <div class="bloque-secretaria__cabecera">
                        <div>
                            <h2>Datos de la asignatura</h2>

                            <p>
                                Completa los datos básicos de la asignatura. La asignatura quedará pendiente hasta que tenga profesor y alumnos asignados.
                            </p>
                        </div>
                    </div>

                    <form class="formulario-secretaria" id="formCrearAsignatura" method="post">
                        <div class="campo-formulario-secretaria">
                            <label class="form-label" for="nombreAsignatura">
                                Nombre de la asignatura *
                            </label>

                            <input
                                class="input"
                                id="nombreAsignatura"
                                name="nombre"
                                type="text"
                                placeholder="Ej. Diseño de Interfaces"
                                value="<?php echo limpiar_texto_doa($datos_asignatura["nombre"]); ?>"
                                required>

                            <p class="mensaje-error-campo" id="errorNombreAsignatura">
                                <?php echo isset($errores["nombre"]) ? limpiar_texto_doa($errores["nombre"]) : ""; ?>
                            </p>
                        </div>

                        <div class="campo-formulario-secretaria">
                            <label class="form-label" for="codigoAsignatura">
                                Código *
                            </label>

                            <input
                                class="input"
                                id="codigoAsignatura"
                                name="codigo"
                                type="text"
                                placeholder="Ej. GTI-221"
                                value="<?php echo limpiar_texto_doa($datos_asignatura["codigo"]); ?>"
                                required>

                            <p class="mensaje-error-campo" id="errorCodigoAsignatura">
                                <?php echo isset($errores["codigo"]) ? limpiar_texto_doa($errores["codigo"]) : ""; ?>
                            </p>
                        </div>

                        <div class="campo-formulario-secretaria">
                            <label class="form-label" for="cursoAsignatura">
                                Curso *
                            </label>

                            <select class="input" id="cursoAsignatura" name="curso" required>
                                <option value="">Selecciona curso</option>
                                <option value="1º" <?php echo $datos_asignatura["curso"] === "1º" ? "selected" : ""; ?>>1º</option>
                                <option value="2º" <?php echo $datos_asignatura["curso"] === "2º" ? "selected" : ""; ?>>2º</option>
                                <option value="3º" <?php echo $datos_asignatura["curso"] === "3º" ? "selected" : ""; ?>>3º</option>
                                <option value="4º" <?php echo $datos_asignatura["curso"] === "4º" ? "selected" : ""; ?>>4º</option>
                            </select>

                            <p class="mensaje-error-campo" id="errorCursoAsignatura">
                                <?php echo isset($errores["curso"]) ? limpiar_texto_doa($errores["curso"]) : ""; ?>
                            </p>
                        </div>

                        <div class="campo-formulario-secretaria">
                            <label class="form-label" for="grupoAsignatura">
                                Grupo *
                            </label>

                            <select class="input" id="grupoAsignatura" name="grupo" required>
                                <option value="">Selecciona grupo</option>
                                <option value="A" <?php echo $datos_asignatura["grupo"] === "A" ? "selected" : ""; ?>>Grupo A</option>
                                <option value="B" <?php echo $datos_asignatura["grupo"] === "B" ? "selected" : ""; ?>>Grupo B</option>
                                <option value="C" <?php echo $datos_asignatura["grupo"] === "C" ? "selected" : ""; ?>>Grupo C</option>
                            </select>

                            <p class="mensaje-error-campo" id="errorGrupoAsignatura">
                                <?php echo isset($errores["grupo"]) ? limpiar_texto_doa($errores["grupo"]) : ""; ?>
                            </p>
                        </div>

                        <div class="campo-formulario-secretaria campo-formulario-secretaria--completo">
                            <label class="form-label" for="descripcionAsignatura">
                                Descripción
                            </label>

                            <textarea
                                class="input textarea-secretaria"
                                id="descripcionAsignatura"
                                name="descripcion"
                                placeholder="Descripción breve de la asignatura..."><?php echo limpiar_texto_doa($datos_asignatura["descripcion"]); ?></textarea>
                        </div>

                        <div class="campo-formulario-secretaria campo-formulario-secretaria--completo">
                            <label class="form-label" for="profesorResponsable">
                                Profesor responsable
                            </label>

                            <select class="input" id="profesorResponsable" name="id_profesor">
                                <option value="">
                                    Sin profesor por ahora
                                </option>

                                <?php foreach ($profesores as $profesor) { ?>
                                    <?php
                                    $nombre_profesor = trim($profesor["nombre"] . " " . $profesor["apellidos"]);
                                    ?>

                                    <option
                                        value="<?php echo (int) $profesor["id_usuario"]; ?>"
                                        <?php echo (string) $profesor["id_usuario"] === (string) $datos_asignatura["id_profesor"] ? "selected" : ""; ?>>
                                        <?php echo limpiar_texto_doa($nombre_profesor); ?>
                                        · <?php echo limpiar_texto_doa($profesor["email"]); ?>
                                    </option>
                                <?php } ?>
                            </select>

                            <p class="mensaje-error-campo" id="errorProfesorResponsable">
                                <?php echo isset($errores["id_profesor"]) ? limpiar_texto_doa($errores["id_profesor"]) : ""; ?>
                            </p>
                        </div>

                        <div class="acciones-formulario-secretaria">
                            <a class="boton-secretaria" href="asignaturas_secretaria.php">
                                Cancelar
                            </a>

                            <button class="boton-secretaria boton-secretaria--principal" type="submit">
                                Guardar asignatura
                            </button>
                        </div>
                    </form>
                </section>

                <aside class="lateral-secretaria">
                    <article class="tarjeta-lateral-secretaria">
                        <h3>Después de crearla</h3>

                        <div class="lista-lateral-secretaria">
                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>1. Revisar datos</strong>
                                <span>Comprueba que el código, curso y grupo son correctos.</span>
                            </div>

                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>2. Profesor responsable</strong>
                                <span>Si lo conoces, puedes asignarlo ahora. La asignatura seguirá pendiente hasta añadir alumnos.</span>
                            </div>

                            <div class="item-lateral-secretaria item-lateral-secretaria--sin-enlace">
                                <strong>3. Añadir alumnos</strong>
                                <span>Matricula los alumnos correspondientes desde la pantalla de asignaciones.</span>
                            </div>
                        </div>
                    </article>

                    <article class="tarjeta-lateral-secretaria">
                        <h3>Estado demo</h3>

                        <p class="texto-lateral-secretaria">
                            En esta versión PMV, las asignaturas creadas se guardan en la base de datos local. Quedarán pendientes hasta tener profesor y alumnos asignados.
                        </p>
                    </article>
                </aside>
            </div>
        </main>
    </div>
</body>

</html>