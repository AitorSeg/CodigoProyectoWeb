<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

$titulo_pagina = "GTI - Crear Cuenta";

$css = [
    "css/gti.css",
    "css/registro.css"
];

$texto_principal = [
    "titulo_parte_1" => "Crea tu cuenta",
    "titulo_parte_2" => "y comienza hoy",
    "descripcion" => "Regístrate para comprar, gestionar tus licencias y acceder a tus módulos educativos de pago único."
];

$formulario = [
    "titulo" => "Crear cuenta",
    "id" => "registerForm",
    "boton" => "REGISTRARSE"
];

$campos_fila = [
    [
        "label" => "Nombre *",
        "tipo" => "text",
        "id" => "nombre",
        "name" => "nombre",
        "placeholder" => "Tu nombre"
    ],
    [
        "label" => "Apellidos *",
        "tipo" => "text",
        "id" => "apellidos",
        "name" => "apellidos",
        "placeholder" => "Tus apellidos"
    ]
];

$campos_formulario = [
    [
        "label" => "Correo electrónico *",
        "tipo" => "email",
        "id" => "email",
        "name" => "email",
        "placeholder" => "ejemplo@correo.com"
    ],
    [
        "label" => "Contraseña *",
        "tipo" => "password",
        "id" => "pass",
        "name" => "pass",
        "placeholder" => "Ingresa tu contraseña"
    ],
    [
        "label" => "Confirmar Contraseña *",
        "tipo" => "password",
        "id" => "passConfirm",
        "name" => "passConfirm",
        "placeholder" => "Repite tu contraseña"
    ]
];

$enlace_login = [
    "texto" => "¿Ya tienes una cuenta?",
    "texto_enlace" => "Inicia sesión",
    "enlace" => "login.php"
];

$errores = [];

$datos_formulario = [
    "nombre" => "",
    "apellidos" => "",
    "email" => ""
];

function limpiar_texto($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $datos_formulario["nombre"] = trim($_POST["nombre"]);
    $datos_formulario["apellidos"] = trim($_POST["apellidos"]);
    $datos_formulario["email"] = strtolower(trim($_POST["email"]));

    $contrasena = $_POST["pass"];
    $confirmar_contrasena = $_POST["passConfirm"];

    if ($datos_formulario["nombre"] === "") {
        $errores[] = "El nombre es obligatorio.";
    }

    if ($datos_formulario["apellidos"] === "") {
        $errores[] = "Los apellidos son obligatorios.";
    }

    if (!filter_var($datos_formulario["email"], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no tiene un formato válido.";
    }

    if ($contrasena === "") {
        $errores[] = "La contraseña es obligatoria.";
    }

    if ($contrasena !== $confirmar_contrasena) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (count($errores) === 0) {
        $consulta_usuario = $pdo->prepare("
            SELECT COUNT(*) AS total 
            FROM usuarios 
            WHERE email = :email
        ");

        $consulta_usuario->execute([
            "email" => $datos_formulario["email"]
        ]);

        $usuario_existente = $consulta_usuario->fetch();

        if ((int) $usuario_existente["total"] > 0) {
            $errores[] = "Ya existe una cuenta con este correo electrónico.";
        }
    }

    if (count($errores) === 0) {
        $password_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $crear_usuario = $pdo->prepare("
            INSERT INTO usuarios 
                (nombre, apellidos, email, password_hash, rol, tipo_usuario, estado)
            VALUES 
                (:nombre, :apellidos, :email, :password_hash, 'usuario_gti', 'real', 'activo')
        ");

        $crear_usuario->execute([
            "nombre" => $datos_formulario["nombre"],
            "apellidos" => $datos_formulario["apellidos"],
            "email" => $datos_formulario["email"],
            "password_hash" => $password_hash
        ]);

        header("Location: login.php?registro=ok");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo limpiar_texto($titulo_pagina); ?></title>

    <?php foreach ($css as $archivo_css) { ?>
        <link rel="stylesheet" href="<?php echo limpiar_texto($archivo_css); ?>" />
    <?php } ?>
</head>

<body>

    <div class="auth-container">

        <div class="auth-text">
            <h1>
                <?php echo limpiar_texto($texto_principal["titulo_parte_1"]); ?> <br>
                <span class="auth-highlight">
                    <?php echo limpiar_texto($texto_principal["titulo_parte_2"]); ?>
                </span>
            </h1>

            <p>
                <?php echo limpiar_texto($texto_principal["descripcion"]); ?>
            </p>
        </div>

        <div class="auth-card">
            <h2><?php echo limpiar_texto($formulario["titulo"]); ?></h2>

            <?php if (count($errores) > 0) { ?>
                <div class="form-message form-message--error">
                    <?php foreach ($errores as $error) { ?>
                        <p><?php echo limpiar_texto($error); ?></p>
                    <?php } ?>
                </div>
            <?php } ?>

            <form id="<?php echo limpiar_texto($formulario["id"]); ?>" method="post">

                <div class="form-row">
                    <?php foreach ($campos_fila as $campo) { ?>
                        <div class="form-group">
                            <label for="<?php echo limpiar_texto($campo["id"]); ?>">
                                <?php echo limpiar_texto($campo["label"]); ?>
                            </label>

                            <input 
                                type="<?php echo limpiar_texto($campo["tipo"]); ?>" 
                                id="<?php echo limpiar_texto($campo["id"]); ?>" 
                                name="<?php echo limpiar_texto($campo["name"]); ?>"
                                placeholder="<?php echo limpiar_texto($campo["placeholder"]); ?>" 
                                value="<?php echo limpiar_texto($datos_formulario[$campo["name"]]); ?>"
                                required
                            >
                        </div>
                    <?php } ?>
                </div>

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
                                value="<?php echo limpiar_texto($datos_formulario["email"]); ?>"
                            <?php } ?>
                            required
                        >
                    </div>
                <?php } ?>

                <button type="submit" class="btn-primary">
                    <?php echo limpiar_texto($formulario["boton"]); ?>
                </button>

                <p class="auth-login-link">
                    <?php echo limpiar_texto($enlace_login["texto"]); ?>
                    <a href="<?php echo limpiar_texto($enlace_login["enlace"]); ?>" class="auth-login-link__anchor">
                        <?php echo limpiar_texto($enlace_login["texto_enlace"]); ?>
                    </a>
                </p>

            </form>
        </div>

    </div>

</body>
</html>