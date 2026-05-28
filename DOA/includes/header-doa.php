<header class="cabecera-panel-doa">
    <div class="cabecera-panel-doa__logo">
        <div class="marca-doa">
            <a class="marca-doa__link" href="<?= $enlace_panel ?>" aria-label="Ir al panel principal de DOA">
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

            <input type="search" placeholder="<?= $placeholder_buscador ?>" disabled>
        </div>
    </div>

    <div class="cabecera-panel-doa__perfil">
        <div class="perfil-header">
            <span class="perfil-header__icono" aria-hidden="true">
                <img src="img/iconos/grey-user.svg" alt="">
            </span>

            <span class="perfil-header__texto">
                <strong id="nombreUsuarioHeader">Usuario demo</strong>
                <small id="rolUsuarioHeader">Perfil DOA</small>
            </span>
        </div>

        <a class="boton-cerrar-sesion-movil" href="elegir_perfil.php" aria-label="Cerrar sesión">
            <span class="boton-cerrar-sesion-movil__icono" aria-hidden="true">
                <img class="icono-estado icono-estado--gris" src="img/iconos/grey-log-out.svg" alt="">
                <img class="icono-estado icono-estado--azul" src="img/iconos/blue-log-out.svg" alt="">
            </span>
        </a>
    </div>
</header>