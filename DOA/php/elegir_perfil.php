<?php
session_start();

/*
    Cargamos los datos mock de usuarios.
    Esta forma acepta dos versiones de usuarios_demo.php:
    - una que define $usuariosDemo y $tiposUsuarioDemo;
    - una que devuelve un array con 'usuarios' y 'tipos'.
*/
$usuariosDemo = [];
$tiposUsuarioDemo = [];

$datosUsuarios = require __DIR__ . '/usuarios_demo.php';

if (is_array($datosUsuarios)) {
    $usuariosDemo = $datosUsuarios['usuarios'] ?? $datosUsuarios['usuariosDemo'] ?? $usuariosDemo;
    $tiposUsuarioDemo = $datosUsuarios['tipos'] ?? $datosUsuarios['tiposUsuarioDemo'] ?? $tiposUsuarioDemo;
}

$error = $_GET['error'] ?? '';
$emailAnterior = $_GET['email'] ?? '';

$mensajesError = [
    'campos' => 'Introduce el correo electrónico y la contraseña.',
    'credenciales' => 'Las credenciales no pertenecen a ningún usuario de prueba.',
];

$mensajeError = $mensajesError[$error] ?? '';

function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function contarUsuariosDemo($usuarios, $tipo)
{
    if ($tipo === 'todos') {
        return count($usuarios);
    }

    $total = 0;

    foreach ($usuarios as $usuario) {
        if (($usuario['tipo'] ?? '') === $tipo) {
            $total++;
        }
    }

    return $total;
}

function obtenerIconoPerfil($tipo)
{
    $iconos = [
        'todos' => 'user.svg',
        'alumno' => 'graduation-cap.svg',
        'profesor' => 'user.svg',
        'secretaria' => 'briefcase-business.svg',
    ];

    return $iconos[$tipo] ?? 'user.svg';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elegir perfil de prueba | DOA</title>

    <link rel="stylesheet" href="../css/doa.css">
    <link rel="stylesheet" href="../css/elegir_perfil.css">
</head>
<body class="demo-login-body">
    <main class="demo-login-page">
        <header class="demo-login-header">
            <img
                class="demo-login-logo"
                src="../img/LogoDoaAzulRecortado.png"
                alt="Logo de DOA"
            >

            <div class="demo-login-title">
                <span class="demo-login-icon" aria-hidden="true">
                    <img src="../img/iconos/user.svg" alt="">
                </span>

                <div>
                    <h1>Elegir perfil de prueba</h1>
                    <p>
                        Selecciona un usuario de prueba para completar el inicio de sesión.
                        También puedes escribir los datos de inicio de sesión manualmente.
                    </p>
                </div>
            </div>
        </header>

        <div class="demo-login-grid">
            <section class="card demo-users-card" aria-labelledby="tituloUsuarios">
                <h2 class="sr-only" id="tituloUsuarios">Usuarios de prueba</h2>

                <div class="demo-tabs" role="tablist" aria-label="Filtrar perfiles de prueba">
                    <?php foreach ($tiposUsuarioDemo as $tipo => $nombreTipo): ?>
                        <?php
                            $totalUsuarios = contarUsuariosDemo($usuariosDemo, $tipo);
                            $claseInicial = $tipo === 'todos' ? 'demo-tab demo-tab-active' : 'demo-tab';
                            $iconoTipo = obtenerIconoPerfil($tipo);
                        ?>

                        <button
                            class="<?php echo e($claseInicial); ?>"
                            type="button"
                            data-filtro="<?php echo e($tipo); ?>"
                        >
                            <img
                                class="demo-tab-icon"
                                src="../img/iconos/<?php echo e($iconoTipo); ?>"
                                alt=""
                                aria-hidden="true"
                            >
                            <span class="demo-tab-text">
                                <?php echo e($nombreTipo); ?>
                                <small>(<?php echo $totalUsuarios; ?>)</small>
                            </span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="demo-user-groups">
                    <?php foreach ($tiposUsuarioDemo as $tipo => $nombreTipo): ?>
                        <?php if ($tipo === 'todos') { continue; } ?>

                        <section class="demo-user-group" data-grupo="<?php echo e($tipo); ?>">
                            <h3><?php echo e($nombreTipo); ?></h3>

                            <div class="demo-user-list">
                                <?php foreach ($usuariosDemo as $usuario): ?>
                                    <?php if (($usuario['tipo'] ?? '') !== $tipo) { continue; } ?>
                                    <?php $iconoUsuario = obtenerIconoPerfil($usuario['tipo'] ?? ''); ?>

                                    <button
                                        class="demo-user-option"
                                        type="button"
                                        data-id="<?php echo e($usuario['id'] ?? ''); ?>"
                                        data-dni="<?php echo e($usuario['dni'] ?? ''); ?>"
                                        data-nombre="<?php echo e($usuario['nombre'] ?? ''); ?>"
                                        data-email="<?php echo e($usuario['email'] ?? ''); ?>"
                                        data-password="<?php echo e($usuario['password'] ?? ''); ?>"
                                        data-tipo="<?php echo e($usuario['tipo'] ?? ''); ?>"
                                        data-rol="<?php echo e($usuario['rol'] ?? ''); ?>"
                                    >
                                        <span class="demo-user-avatar" aria-hidden="true">
                                            <img src="../img/iconos/<?php echo e($iconoUsuario); ?>" alt="">
                                        </span>

                                        <span class="demo-user-info">
                                            <strong><?php echo e($usuario['nombre'] ?? ''); ?></strong>
                                            <span>
                                                <?php echo e($usuario['email'] ?? ''); ?>
                                                ·
                                                <?php echo e($usuario['password'] ?? ''); ?>
                                            </span>
                                        </span>

                                        <span class="demo-user-arrow" aria-hidden="true">
                                            <img
                                                class="demo-user-arrow-icon demo-user-arrow-grey"
                                                src="../img/iconos/grey-chevron-right.svg"
                                                alt=""
                                            >
                                            <img
                                                class="demo-user-arrow-icon demo-user-arrow-blue"
                                                src="../img/iconos/blue-chevron-right.svg"
                                                alt=""
                                            >
                                        </span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>

            <aside class="card demo-form-card" aria-labelledby="tituloLogin">
                <h2 id="tituloLogin">Inicio de Sesión</h2>

                <p class="text-muted">
                    Accede con uno de los usuarios de prueba registrados en la demo.
                </p>

                <p
                    class="<?php echo $mensajeError === '' ? 'demo-error hidden' : 'demo-error'; ?>"
                    id="mensajeError"
                    role="alert"
                >
                    <?php echo e($mensajeError); ?>
                </p>

                <form action="procesar_login_demo.php" method="post" id="formLoginDemo">
                    <input type="hidden" name="perfil_id" id="perfilId">

                    <div class="form-group">
                        <label class="form-label" for="email">Correo electrónico</label>
                        <input
                            class="input"
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo e($emailAnterior); ?>"
                            placeholder="ejemplo@upv.es"
                            autocomplete="username"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Contraseña</label>

                        <div class="demo-password-field">
                            <input
                                class="input"
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Introduce la contraseña"
                                autocomplete="current-password"
                            >

                            <button class="demo-show-password" type="button" id="botonMostrarPassword">
                                Mostrar
                            </button>
                        </div>
                    </div>

                    <div class="demo-selected-profile" id="perfilSeleccionado">
                        <p class="label">Perfil seleccionado</p>

                        <ul>
                            <li>
                                <span>Nombre:</span>
                                <strong id="perfilNombre">Ninguno</strong>
                            </li>
                            <li>
                                <span>DNI:</span>
                                <strong id="perfilDni">---</strong>
                            </li>
                            <li>
                                <span>Rol detectado:</span>
                                <strong id="perfilRol">---</strong>
                            </li>
                        </ul>
                    </div>

                    <button class="btn btn-primary demo-submit-button" type="submit">
                        Acceder a DOA
                    </button>
                </form>

                <div class="demo-info-box">
                    <strong>Información</strong>
                    <p>
                        Selecciona un perfil o introduce credenciales manualmente.
                        Si los datos no coinciden con ningún usuario de prueba, no se podrá acceder a DOA.
                    </p>
                </div>
            </aside>
        </div>
    </main>

    <script src="../js/elegir_perfil.js"></script>
</body>
</html>
