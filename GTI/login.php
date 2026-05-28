<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

$titulo_pagina = "Iniciar Sesión - GTI";

$css = [
    "css/gti.css",
    "css/login.css"
];

$login = [
    "titulo" => "Iniciar Sesión",
    "subtitulo" => "Inicia sesión para comprar y gestionar tus módulos educativos"
];

$campos_formulario = [
    [
        "label" => "Correo electrónico",
        "tipo" => "email",
        "id" => "email",
        "name" => "email",
        "placeholder" => "ejemplo@correo.com",
        "error_id" => "error-email",
        "error_texto" => "Ingresa un correo válido."
    ],
    [
        "label" => "Contraseña",
        "tipo" => "password",
        "id" => "password",
        "name" => "password",
        "placeholder" => "Ingresa tu contraseña",
        "error_id" => "error-password",
        "error_texto" => "La contraseña es obligatoria."
    ]
];

$opciones_formulario = [
    "checkbox_id" => "rememberMe",
    "checkbox_name" => "rememberMe",
    "texto_checkbox" => "Recordarme",
    "texto_enlace" => "¿Has olvidado tu contraseña?",
    "enlace" => "#"
];

$footer = [
    "texto" => "¿No tienes cuenta?",
    "texto_enlace" => "Crea una aquí",
    "enlace" => "registro.php"
];

$errores = [];
$mensaje_ok = "";

$email = "";

$redir = "";

if (isset($_GET["redir"]) && $_GET["redir"] === "doa") {
    $redir = "doa";
}

if (isset($_POST["redir"]) && $_POST["redir"] === "doa") {
    $redir = "doa";
}

function limpiar_texto($texto)
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

if (isset($_GET["registro"]) && $_GET["registro"] === "ok") {
    $mensaje_ok = "Cuenta creada correctamente. Ahora puedes iniciar sesión.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST["email"]));
    $contrasena = $_POST["password"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no tiene un formato válido.";
    }

    if ($contrasena === "") {
        $errores[] = "La contraseña es obligatoria.";
    }

    if (count($errores) === 0) {
        $consulta_usuario = $pdo->prepare("
            SELECT id_usuario, nombre, apellidos, email, password_hash, rol, tipo_usuario, estado
            FROM usuarios
            WHERE email = :email
            AND tipo_usuario = 'real'
            LIMIT 1
        ");

        $consulta_usuario->execute([
            "email" => $email
        ]);

        $usuario = $consulta_usuario->fetch();

        if (!$usuario || !password_verify($contrasena, $usuario["password_hash"])) {
            $errores[] = "Correo o contraseña incorrectos.";
        } elseif ($usuario["estado"] !== "activo") {
            $errores[] = "Esta cuenta no está activa.";
        } else {
            $_SESSION["id_usuario"] = $usuario["id_usuario"];
            $_SESSION["nombre"] = $usuario["nombre"];
            $_SESSION["apellidos"] = $usuario["apellidos"];
            $_SESSION["email"] = $usuario["email"];
            $_SESSION["rol"] = $usuario["rol"];
            $_SESSION["tipo_usuario"] = $usuario["tipo_usuario"];

            if ($redir === "doa") {
                header("Location: ../DOA/elegir_perfil.php");
                exit;
            }

            header("Location: ../index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?php echo limpiar_texto($titulo_pagina); ?></title>

    <?php foreach ($css as $archivo_css) { ?>
        <link rel="stylesheet" href="<?php echo limpiar_texto($archivo_css); ?>">
    <?php } ?>
</head>

<body>

    <div class="auth-card">

        <h2><?php echo limpiar_texto($login["titulo"]); ?></h2>

        <p class="subtitle">
            <?php echo limpiar_texto($login["subtitulo"]); ?>
        </p>

        <?php if ($mensaje_ok !== "") { ?>
            <div class="success-msg" style="display: block; margin-bottom: 16px; color: #4ade80; font-size: 14px;">
                <?php echo limpiar_texto($mensaje_ok); ?>
            </div>
        <?php } ?>

        <?php if (count($errores) > 0) { ?>
            <div class="error-msg" style="display: block; margin-bottom: 16px; color: #ff4d4d; font-size: 14px;">
                <?php foreach ($errores as $error) { ?>
                    <p><?php echo limpiar_texto($error); ?></p>
                <?php } ?>
            </div>
        <?php } ?>

        <form id="loginForm" method="post">
            <input type="hidden" name="redir" value="<?php echo limpiar_texto($redir); ?>">

            <?php foreach ($campos_formulario as $campo) { ?>

                <div class="form-group">
                    <label for="<?php echo limpiar_texto($campo["id"]); ?>">
                        <?php echo limpiar_texto($campo["label"]); ?>
                    </label>

                    <input
                        type="<?php echo limpiar_texto($campo["tipo"]); ?>"
                        id="<?php echo limpiar_texto($campo["id"]); ?>"
                        name="<?php echo limpiar_texto($campo["name"]); ?>"
                        placeholder="<?php echo limpiar_texto($campo["placeholder"]); ?>"
                        <?php if ($campo["name"] === "email") { ?>
                        value="<?php echo limpiar_texto($email); ?>"
                        <?php } ?>
                        required>

                    <span class="error-text" id="<?php echo limpiar_texto($campo["error_id"]); ?>">
                        <?php echo limpiar_texto($campo["error_texto"]); ?>
                    </span>
                </div>

            <?php } ?>

            <div class="form-options">
                <label>
                    <input
                        type="checkbox"
                        id="<?php echo limpiar_texto($opciones_formulario["checkbox_id"]); ?>"
                        name="<?php echo limpiar_texto($opciones_formulario["checkbox_name"]); ?>">
                    <?php echo limpiar_texto($opciones_formulario["texto_checkbox"]); ?>
                </label>

                <a href="<?php echo limpiar_texto($opciones_formulario["enlace"]); ?>">
                    <?php echo limpiar_texto($opciones_formulario["texto_enlace"]); ?>
                </a>
            </div>

            <button type="submit" class="btn-primary">INICIA SESIÓN</button>

        </form>

        <div class="auth-footer">
            <?php echo limpiar_texto($footer["texto"]); ?>
            <a href="<?php echo limpiar_texto($footer["enlace"]); ?>">
                <?php echo limpiar_texto($footer["texto_enlace"]); ?>
            </a>
        </div>

    </div>

</body>

</html>