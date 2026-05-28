<?php
session_start();

$tituloPagina = "GTI - Contacto";

$css = [
    "css/gti.css",
    "css/contacto.css"
];

$botonVolver = [
    "texto" => "← Volver al inicio",
    "enlace" => "../index.php",
    "clase" => "back-btn"
];

$contacto = [
    "titulo" => "Contactanos",
    "subtitulo" => "Escríbenos tu duda y te responderemos lo antes posible."
];

$camposFormulario = [
    [
        "label" => "Correo electrónico",
        "tipo" => "email",
        "id" => "email",
        "name" => "email",
        "placeholder" => "ejemplo@correo.com",
        "textarea" => false
    ],
    [
        "label" => "Título de la duda",
        "tipo" => "text",
        "id" => "titulo",
        "name" => "titulo",
        "placeholder" => "Escribe el título de tu duda",
        "textarea" => false
    ],
    [
        "label" => "Escribe tu duda",
        "tipo" => "",
        "id" => "duda",
        "name" => "duda",
        "placeholder" => "Explica aquí tu duda...",
        "textarea" => true
    ]
];

$script = "js/contacto.js";

function limpiarTexto($texto) {
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?php echo limpiarTexto($tituloPagina); ?></title>

    <?php foreach ($css as $archivoCss) { ?>
        <link rel="stylesheet" href="<?php echo limpiarTexto($archivoCss); ?>" />
    <?php } ?>
</head>

<body>

    <main class="contact-page">

        <section class="contact-card">

            <a 
                href="<?php echo limpiarTexto($botonVolver["enlace"]); ?>" 
                class="<?php echo limpiarTexto($botonVolver["clase"]); ?>"
            >
                <?php echo limpiarTexto($botonVolver["texto"]); ?>
            </a>

            <h1><?php echo limpiarTexto($contacto["titulo"]); ?></h1>

            <p class="contact-subtitle">
                <?php echo limpiarTexto($contacto["subtitulo"]); ?>
            </p>

            <form id="contactForm" method="post">

                <?php foreach ($camposFormulario as $campo) { ?>

                    <div class="form-group">

                        <label for="<?php echo limpiarTexto($campo["id"]); ?>">
                            <?php echo limpiarTexto($campo["label"]); ?>
                        </label>

                        <?php if ($campo["textarea"]) { ?>

                            <textarea 
                                id="<?php echo limpiarTexto($campo["id"]); ?>" 
                                name="<?php echo limpiarTexto($campo["name"]); ?>" 
                                placeholder="<?php echo limpiarTexto($campo["placeholder"]); ?>"
                                required
                            ></textarea>

                        <?php } else { ?>

                            <input 
                                type="<?php echo limpiarTexto($campo["tipo"]); ?>" 
                                id="<?php echo limpiarTexto($campo["id"]); ?>" 
                                name="<?php echo limpiarTexto($campo["name"]); ?>" 
                                placeholder="<?php echo limpiarTexto($campo["placeholder"]); ?>" 
                                required
                            />

                        <?php } ?>

                    </div>

                <?php } ?>

                <button type="submit" class="send-btn">Enviar</button>

            </form>

        </section>

    </main>

    <script src="<?php echo limpiarTexto($script); ?>"></script>

</body>
</html>