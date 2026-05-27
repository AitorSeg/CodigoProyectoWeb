<aside class="barra-lateral-doa">
    <div class="tarjeta-modo-prueba">
        <div class="tarjeta-modo-prueba__cabecera">
            <span class="tarjeta-modo-prueba__icono" aria-hidden="true">
                <img src="img/iconos/grey-info.svg" alt="">
            </span>
            <strong>Modo de prueba</strong>
        </div>

        <p>
            Estás usando DOA con datos de prueba. Las acciones no afectan a usuarios reales
            y los permisos dependen del perfil elegido.
        </p>
    </div>

    <nav class="navegacion-lateral-doa" aria-label="Navegación principal de DOA">
        <?php if ($rol_pagina === "alumno") { ?>
            <a class="navegacion-lateral-doa__item <?= $pagina_activa === "panel" ? "navegacion-lateral-doa__item--activo" : "" ?>" href="panel_principal.php">
                <span class="navegacion-lateral-doa__icono" aria-hidden="true">
                    <img class="icono-estado icono-estado--gris" src="img/iconos/grey-layout-dashboard.svg" alt="">
                    <img class="icono-estado icono-estado--azul" src="img/iconos/blue-layout-dashboard.svg" alt="">
                </span>
                <span>Panel Principal</span>
            </a>

            <a class="navegacion-lateral-doa__item <?= $pagina_activa === "asignaturas" ? "navegacion-lateral-doa__item--activo" : "" ?>" href="asignaturas.php">
                <span class="navegacion-lateral-doa__icono" aria-hidden="true">
                    <img class="icono-estado icono-estado--gris" src="img/iconos/grey-notebook.svg" alt="">
                    <img class="icono-estado icono-estado--azul" src="img/iconos/blue-notebook.svg" alt="">
                </span>
                <span>Mis Asignaturas</span>
            </a>

            <a class="navegacion-lateral-doa__item <?= $pagina_activa === "notificaciones" ? "navegacion-lateral-doa__item--activo" : "" ?>" href="notificaciones.php">
                <span class="navegacion-lateral-doa__icono" aria-hidden="true">
                    <img class="icono-estado icono-estado--gris" src="img/iconos/grey-bell.svg" alt="">
                    <img class="icono-estado icono-estado--azul" src="img/iconos/blue-bell.svg" alt="">
                </span>
                <span>Notificaciones</span>
            </a>
        <?php } ?>

        <?php if ($rol_pagina === "profesor") { ?>
            <a class="navegacion-lateral-doa__item <?= $pagina_activa === "panel" ? "navegacion-lateral-doa__item--activo" : "" ?>" href="panel_profesor.php">
                <span class="navegacion-lateral-doa__icono" aria-hidden="true">
                    <img class="icono-estado icono-estado--gris" src="img/iconos/grey-layout-dashboard.svg" alt="">
                    <img class="icono-estado icono-estado--azul" src="img/iconos/blue-layout-dashboard.svg" alt="">
                </span>
                <span>Panel Principal</span>
            </a>

            <a class="navegacion-lateral-doa__item <?= $pagina_activa === "asignaturas" ? "navegacion-lateral-doa__item--activo" : "" ?>" href="asignaturas_profesor.html">
                <span class="navegacion-lateral-doa__icono" aria-hidden="true">
                    <img class="icono-estado icono-estado--gris" src="img/iconos/grey-notebook.svg" alt="">
                    <img class="icono-estado icono-estado--azul" src="img/iconos/blue-notebook.svg" alt="">
                </span>
                <span>Mis Asignaturas</span>
            </a>

            <a class="navegacion-lateral-doa__item <?= $pagina_activa === "notificaciones" ? "navegacion-lateral-doa__item--activo" : "" ?>" href="enviar_notificaciones_profesor.php">
                <span class="navegacion-lateral-doa__icono" aria-hidden="true">
                    <img class="icono-estado icono-estado--gris" src="img/iconos/grey-bell.svg" alt="">
                    <img class="icono-estado icono-estado--azul" src="img/iconos/blue-bell.svg" alt="">
                </span>
                <span>Notificaciones</span>
            </a>
        <?php } ?>

        <?php if ($rol_pagina === "secretaria") { ?>
            <a class="navegacion-lateral-doa__item <?= $pagina_activa === "panel" ? "navegacion-lateral-doa__item--activo" : "" ?>" href="panel_secretaria.php">
                <span class="navegacion-lateral-doa__icono" aria-hidden="true">
                    <img class="icono-estado icono-estado--gris" src="img/iconos/grey-layout-dashboard.svg" alt="">
                    <img class="icono-estado icono-estado--azul" src="img/iconos/blue-layout-dashboard.svg" alt="">
                </span>
                <span>Panel Principal</span>
            </a>

            <a class="navegacion-lateral-doa__item <?= $pagina_activa === "asignaturas" ? "navegacion-lateral-doa__item--activo" : "" ?>" href="asignaturas_secretaria.php">
                <span class="navegacion-lateral-doa__icono" aria-hidden="true">
                    <img class="icono-estado icono-estado--gris" src="img/iconos/grey-notebook.svg" alt="">
                    <img class="icono-estado icono-estado--azul" src="img/iconos/blue-notebook.svg" alt="">
                </span>
                <span>Asignaturas</span>
            </a>

            <a class="navegacion-lateral-doa__item <?= $pagina_activa === "asignaciones" ? "navegacion-lateral-doa__item--activo" : "" ?>" href="asignaciones_secretaria.php">
                <span class="navegacion-lateral-doa__icono" aria-hidden="true">
                    <img class="icono-estado icono-estado--gris" src="img/iconos/grey-user.svg" alt="">
                    <img class="icono-estado icono-estado--azul" src="img/iconos/blue-user.svg" alt="">
                </span>
                <span>Asignaciones</span>
            </a>

            <a class="navegacion-lateral-doa__item <?= $pagina_activa === "notificaciones" ? "navegacion-lateral-doa__item--activo" : "" ?>" href="enviar_notificaciones_secretaria.php">
                <span class="navegacion-lateral-doa__icono" aria-hidden="true">
                    <img class="icono-estado icono-estado--gris" src="img/iconos/grey-bell.svg" alt="">
                    <img class="icono-estado icono-estado--azul" src="img/iconos/blue-bell.svg" alt="">
                </span>
                <span>Notificaciones</span>
            </a>
        <?php } ?>
    </nav>

    <div class="barra-lateral-doa__salida">
        <a class="boton-cerrar-sesion" href="elegir_perfil.php">
            <span class="boton-cerrar-sesion__icono" aria-hidden="true">
                <img class="icono-estado icono-estado--gris" src="img/iconos/grey-log-out.svg" alt="">
                <img class="icono-estado icono-estado--azul" src="img/iconos/blue-log-out.svg" alt="">
            </span>
            <span>Cerrar sesión</span>
        </a>
    </div>
</aside>