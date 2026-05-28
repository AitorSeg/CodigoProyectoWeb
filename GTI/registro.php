<?php
session_start();

$tituloPagina = "GTI - Crear Cuenta";

$css = [
    "css/gti.css",
    "css/registro.css"
];

$textoPrincipal = [
    "tituloParte1" => "Crea tu cuenta",
    "tituloParte2" => "y comienza hoy",
    "descripcion" => "Regístrate para comprar, gestionar tus licencias y acceder a tus módulos educativos de pago único."
];

$formulario = [
    "titulo" => "Crear cuenta",
    "id" => "registerForm",
    "boton" => "REGISTRARSE"
];

$camposFila = [
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

$camposFormulario = [
    [
        "label" => "Correo electrónico *",
        "tipo" => "email",
        "id" => "email",
        "name" => "email",
        "placeholder" => "ejemplo@correo.com",
        "error" => false
    ],
    [
        "label" => "Contraseña *",
        "tipo" => "password",
        "id" => "pass",
        "name" => "pass",
        "placeholder" => "Ingresa tu contraseña",
        "error" => false
    ],
    [
        "label" => "Confirmar Contraseña *",
        "tipo" => "password",
        "id" => "passConfirm",
        "name" => "passConfirm",
        "placeholder" => "Repite tu contraseña",
        "error" => true,
        "errorId" => "passError",
        "errorTexto" => "Las contraseñas no coinciden"
    ]
];

$enlaceLogin = [
    "texto" => "¿Ya tienes una cuenta?",
    "textoEnlace" => "Inicia sesión",
    "enlace" => "login.php"
];

$script = "js/registro.js";

function limpiarTexto($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo limpiarTexto($tituloPagina); ?></title>

    <?php foreach ($css as $archivoCss) { ?>
        <link rel="stylesheet" href="<?php echo limpiarTexto($archivoCss); ?>" />
    <?php } ?>
</head>

<body>

    <div class="auth-container">

        <div class="auth-text">
            <h1>
                <?php echo limpiarTexto($textoPrincipal["tituloParte1"]); ?> <br>
                <span style="color: var(--primary-orange)">
                    <?php echo limpiarTexto($textoPrincipal["tituloParte2"]); ?>
                </span>
            </h1>

            <p>
                <?php echo limpiarTexto($textoPrincipal["descripcion"]); ?>
            </p>
        </div>

        <div class="auth-card">
            <h2><?php echo limpiarTexto($formulario["titulo"]); ?></h2>

            <form id="<?php echo limpiarTexto($formulario["id"]); ?>" method="post">

                <div class="form-row">
                    <?php foreach ($camposFila as $campo) { ?>
                        <div class="form-group">
                            <label for="<?php echo limpiarTexto($campo["id"]); ?>">
                                <?php echo limpiarTexto($campo["label"]); ?>
                            </label>

                            <input 
                                type="<?php echo limpiarTexto($campo["tipo"]); ?>" 
                                id="<?php echo limpiarTexto($campo["id"]); ?>" 
                                name="<?php echo limpiarTexto($campo["name"]); ?>"
                                placeholder="<?php echo limpiarTexto($campo["placeholder"]); ?>" 
                                required
                            >
                        </div>
                    <?php } ?>
                </div>

                <?php foreach ($camposFormulario as $campo) { ?>
                    <div class="form-group">
                        <label for="<?php echo limpiarTexto($campo["id"]); ?>">
                            <?php echo limpiarTexto($campo["label"]); ?>
                        </label>

                        <input 
                            type="<?php echo limpiarTexto($campo["tipo"]); ?>" 
                            id="<?php echo limpiarTexto($campo["id"]); ?>" 
                            name="<?php echo limpiarTexto($campo["name"]); ?>"
                            placeholder="<?php echo limpiarTexto($campo["placeholder"]); ?>" 
                            required
                        >

                        <?php if ($campo["error"]) { ?>
                            <div class="error-msg" id="<?php echo limpiarTexto($campo["errorId"]); ?>">
                                <?php echo limpiarTexto($campo["errorTexto"]); ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <button type="submit" class="btn-primary">
                    <?php echo limpiarTexto($formulario["boton"]); ?>
                </button>

                <p style="margin-top: 24px; text-align: center; font-size: 14px; color: var(--text-gray)">
                    <?php echo limpiarTexto($enlaceLogin["texto"]); ?>
                    <a href="<?php echo limpiarTexto($enlaceLogin["enlace"]); ?>" style="color: var(--primary-orange); text-decoration: none; font-weight: 600;">
                        <?php echo limpiarTexto($enlaceLogin["textoEnlace"]); ?>
                    </a>
                </p>

            </form>
        </div>

    </div>

    <script src="<?php echo limpiarTexto($script); ?>"></script>

</body>
</html>