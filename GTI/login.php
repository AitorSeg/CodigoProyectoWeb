<?php
session_start();

$tituloPagina = "Iniciar Sesión - GTI";

$css = [
    "css/gti.css",
    "css/login.css"
];

$login = [
    "titulo" => "Iniciar Sesión",
    "subtitulo" => "Inicia sesión para comprar y gestionar tus módulos educativos"
];

$camposFormulario = [
    [
        "label" => "Correo electrónico",
        "tipo" => "email",
        "id" => "email",
        "name" => "email",
        "placeholder" => "ejemplo@correo.com",
        "errorId" => "error-email",
        "errorTexto" => "Ingresa un correo válido."
    ],
    [
        "label" => "Contraseña",
        "tipo" => "password",
        "id" => "password",
        "name" => "password",
        "placeholder" => "Ingresa tu contraseña",
        "errorId" => "error-password",
        "errorTexto" => "La contraseña es obligatoria."
    ]
];

$opcionesFormulario = [
    "checkboxId" => "rememberMe",
    "checkboxName" => "rememberMe",
    "textoCheckbox" => "Recordarme",
    "textoEnlace" => "¿Has olvidado tu contraseña?",
    "enlace" => "#"
];

$footer = [
    "texto" => "¿No tienes cuenta?",
    "textoEnlace" => "Crea una aquí",
    "enlace" => "registro.php"
];

$script = "js/login.js";

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
        <link rel="stylesheet" href="<?php echo limpiarTexto($archivoCss); ?>">
    <?php } ?>
</head>

<body>

    <div class="auth-card">

        <h2><?php echo limpiarTexto($login["titulo"]); ?></h2>

        <p class="subtitle">
            <?php echo limpiarTexto($login["subtitulo"]); ?>
        </p>

        <form id="loginForm" method="post">

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

                    <span class="error-text" id="<?php echo limpiarTexto($campo["errorId"]); ?>">
                        <?php echo limpiarTexto($campo["errorTexto"]); ?>
                    </span>
                </div>

            <?php } ?>

            <div class="form-options">
                <label>
                    <input 
                        type="checkbox" 
                        id="<?php echo limpiarTexto($opcionesFormulario["checkboxId"]); ?>"
                        name="<?php echo limpiarTexto($opcionesFormulario["checkboxName"]); ?>"
                    >
                    <?php echo limpiarTexto($opcionesFormulario["textoCheckbox"]); ?>
                </label>

                <a href="<?php echo limpiarTexto($opcionesFormulario["enlace"]); ?>">
                    <?php echo limpiarTexto($opcionesFormulario["textoEnlace"]); ?>
                </a>
            </div>

            <button type="submit" class="btn-primary">INICIA SESIÓN</button>

        </form>

        <div class="auth-footer">
            <?php echo limpiarTexto($footer["texto"]); ?>
            <a href="<?php echo limpiarTexto($footer["enlace"]); ?>">
                <?php echo limpiarTexto($footer["textoEnlace"]); ?>
            </a>
        </div>

    </div>

    <script src="<?php echo limpiarTexto($script); ?>"></script>

</body>
</html>