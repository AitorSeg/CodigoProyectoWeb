<?php
session_start();

$sesion_iniciada = isset($_SESSION["id_usuario"]);
$nombre_usuario = $sesion_iniciada ? $_SESSION["nombre"] : "";
$enlace_prueba_doa = $sesion_iniciada ? "../DOA/elegir_perfil.php" : "login.php?redir=doa";

$tituloPagina = "DOA - Nuestro Último Módulo";

$cssGlobal = "css/gti.css";
$cssCatalogo = "css/catalogo.css";

$logo = "img/logoGTI_blanco.png";

$menu = [
    [
        "texto" => "FAQ",
        "enlace" => "../index.php#faq",
        "activo" => false
    ],
    [
        "texto" => "Sobre Nosotros",
        "enlace" => "../index.php#sobre-nosotros",
        "activo" => false
    ],
    [
        "texto" => "Contactanos",
        "enlace" => "../index.php#contacto",
        "activo" => false
    ],
    [
        "texto" => "Catalogo de Modulos",
        "enlace" => "catalogo.php",
        "activo" => true
    ]
];

$modulo = [
    "titulo" => "DOA",
    "descripcion" => "Nuestra app educativa convierte la enseñanza y la comunicación entre docentes y alumnos en acciones rápidas y cerradas sin chats caóticos ni tareas perdidas."
];

$caracteristicas = [
    [
        "titulo" => "Asignaturas",
        "descripcion" => "Capacidad para crear, impartir y cursar infinidad de asignaturas de diferentes maneras y estilos",
        "imagen" => "img/libro.svg",
        "alt" => "Libro",
        "clase" => "feature-card border-cream"
    ],
    [
        "titulo" => "Notificaciones",
        "descripcion" => "Reciba alertas en tiempo real sobre novedades académicas y eventos importantes.",
        "imagen" => "img/calendario.svg",
        "alt" => "Notificaciones",
        "clase" => "feature-card blue"
    ],
    [
        "titulo" => "Perfil estudiantil",
        "descripcion" => "Base de datos centralizada con historial académico y disciplinario de alta seguridad.",
        "imagen" => "img/id.svg",
        "alt" => "id",
        "clase" => "feature-card border-cream"
    ]
];

$licencia = [
    "titulo" => "LICENCIA DEL MODULO",
    "icono" => "img/credit-card.svg",
    "alt" => "card",
    "items" => [
        "Actualizaciones de Seguridad",
        "Usuarios Ilimitados"
    ]
];

$precios = [
    [
        "texto" => "Precio base de la licencia",
        "valor" => "$4,999.00"
    ],
    [
        "texto" => "SOPORTE TÉCNICO (1 AÑO)",
        "valor" => "INCLUIDO"
    ]
];

function limpiarTexto($texto)
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?php echo limpiarTexto($tituloPagina); ?></title>

    <link rel="stylesheet" href="<?php echo limpiarTexto($cssGlobal); ?>" />
    <link rel="stylesheet" href="<?php echo limpiarTexto($cssCatalogo); ?>" />
</head>

<body>

    <header>
        <div class="header-logo">
            <a href="../index.php">
                <img src="<?php echo limpiarTexto($logo); ?>" alt="Logo GTI" />
            </a>
        </div>

        <nav>
            <?php foreach ($menu as $enlace) { ?>
                <a
                    href="<?php echo limpiarTexto($enlace["enlace"]); ?>"
                    class="<?php echo $enlace["activo"] ? "nav-active" : ""; ?>">
                    <?php echo limpiarTexto($enlace["texto"]); ?>
                </a>
            <?php } ?>
        </nav>

        <div class="header-actions">
            <?php if ($sesion_iniciada) { ?>
                <span class="header-user">
                    <?php echo limpiarTexto($nombre_usuario); ?>
                </span>

                 <a href="GTI/logout.php" class="login-btn logout-btn">
                  Cerrar sesión
                 </a>
            <?php } else { ?>
                <a href="registro.php" class="register-btn">Registro</a>
                <a href="login.php" class="login-btn">Log in</a>
            <?php } ?>
        </div>
    </header>

    <main class="module-page">

        <section class="module-hero">
            <div class="module-hero-text">
                <h1><?php echo limpiarTexto($modulo["titulo"]); ?></h1>

                <p>
                    <?php echo limpiarTexto($modulo["descripcion"]); ?>
                </p>
            </div>

            <div class="module-hero-actions">
                <a href="#comprar" class="buy-btn">Comprar</a>
                <a href="<?php echo limpiarTexto($enlace_prueba_doa); ?>" class="trial-btn">
                    Prueba Gratuita
                </a>
            </div>
        </section>

        <section class="features-section">
            <div class="features-grid">

                <?php foreach ($caracteristicas as $caracteristica) { ?>
                    <article class="<?php echo limpiarTexto($caracteristica["clase"]); ?>">
                        <img
                            src="<?php echo limpiarTexto($caracteristica["imagen"]); ?>"
                            alt="<?php echo limpiarTexto($caracteristica["alt"]); ?>" />

                        <h3>
                            <?php echo limpiarTexto($caracteristica["titulo"]); ?>
                        </h3>

                        <p>
                            <?php echo limpiarTexto($caracteristica["descripcion"]); ?>
                        </p>
                    </article>
                <?php } ?>

            </div>
        </section>

        <section class="payment-section" id="comprar">

            <div class="payment-text">
                <h2>PAGO DE UNA SOLA VEZ</h2>

                <p>
                    Elimine las suscripciones recurrentes. Invierta en una infraestructura
                    propia que crece con su institución. Control total de sus datos y sistemas.
                </p>
            </div>

            <div class="license-card">

                <div class="license-title">
                    <img
                        src="<?php echo limpiarTexto($licencia["icono"]); ?>"
                        alt="<?php echo limpiarTexto($licencia["alt"]); ?>" />

                    <h3>
                        <?php echo limpiarTexto($licencia["titulo"]); ?>
                    </h3>
                </div>

                <?php foreach ($licencia["items"] as $item) { ?>
                    <div class="license-item">
                        <img src="img/check.svg" alt="check" />
                        <span><?php echo limpiarTexto($item); ?></span>
                    </div>
                <?php } ?>

                <button class="buy-now-btn">Comprar ahora</button>
            </div>

            <div class="price-list">

                <?php foreach ($precios as $precio) { ?>
                    <div class="price-row">
                        <span><?php echo limpiarTexto($precio["texto"]); ?></span>
                        <strong><?php echo limpiarTexto($precio["valor"]); ?></strong>
                    </div>
                <?php } ?>

            </div>
        </section>

    </main>

</body>

</html>