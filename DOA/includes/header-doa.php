<header class="cabecera-panel-doa">
    <div class="cabecera-panel-doa__logo">
        <div class="marca-doa">
            <a class="marca-doa__link" href="<?php echo limpiar_texto_doa($enlace_panel); ?>" aria-label="Ir al panel principal de DOA">
                <img class="logo-doa" src="img/LogoDoaAzulRecortado.png" alt="Logo de DOA">
            </a>

            <a class="marca-doa__powered" href="../index.php" aria-label="Volver a GTI">
                <span class="marca-doa__powered-text">Powered by</span>
                <img class="marca-doa__powered-logo" src="img/logoGTI_blanco_fondo_negro-removebg-preview.png" alt="Grado en Tecnologías Interactivas">
            </a>
        </div>
    </div>

    <div class="cabecera-panel-doa__buscador">
        <div class="buscador-header buscador-header--desactivado" aria-label="Buscador visual pendiente de activar">
            <span class="buscador-header__icono" aria-hidden="true">
                <img src="img/iconos/grey-search.svg" alt="">
            </span>

            <input type="search" placeholder="<?php echo limpiar_texto_doa($placeholder_buscador); ?>" disabled>
        </div>
    </div>

    <div class="cabecera-panel-doa__perfil">
        <a class="boton-cambiar-perfil-header" href="cerrar_perfil.php">
            <span class="boton-cambiar-perfil-header__icono" aria-hidden="true">
                <img class="icono-estado icono-estado--gris" src="img/iconos/grey-log-out.svg" alt="">
                <img class="icono-estado icono-estado--azul" src="img/iconos/blue-log-out.svg" alt="">
            </span>
            <span>Cambiar perfil</span>
        </a>
        <div class="perfil-header">
            <span class="perfil-header__icono" aria-hidden="true">
                <img src="img/iconos/grey-user.svg" alt="">
            </span>

            <span class="perfil-header__texto">
                <strong>
                    <?php echo limpiar_texto_doa($nombre_usuario_doa); ?>
                </strong>
                <small>
                    <?php echo limpiar_texto_doa($rol_usuario_doa_texto); ?>
                </small>
            </span>
        </div>
    </div>
</header>