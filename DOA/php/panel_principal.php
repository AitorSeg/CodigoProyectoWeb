<?php
session_start();

if (!isset($_SESSION['usuario_demo'])) {
    header('Location: elegir_perfil.php');
    exit;
}

$usuario = $_SESSION['usuario_demo'];

function limpiarTexto($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel principal | DOA</title>

    <link rel="stylesheet" href="../css/doa.css">
</head>
<body>
    <main class="doa-content">
        <section class="card" style="padding: 24px;">
            <h1>Panel principal provisional</h1>
            <p class="text-muted">
                Esta pantalla solo sirve para comprobar que el inicio de sesión funciona.
                Más adelante la sustituiremos por el inicio real de DOA.
            </p>

            <hr class="divider">

            <p><strong>Usuario:</strong> <?php echo limpiarTexto($usuario['nombre']); ?></p>
            <p><strong>Email:</strong> <?php echo limpiarTexto($usuario['email']); ?></p>
            <p><strong>Rol:</strong> <?php echo limpiarTexto($usuario['rol']); ?></p>
        </section>
    </main>
</body>
</html>
