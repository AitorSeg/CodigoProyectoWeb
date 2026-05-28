<?php
    $rol_pagina = "alumno";
    $pagina_activa = "notificaciones";
    $enlace_panel = "panel_principal.php";
    $placeholder_buscador = "Buscar notificación...";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones | DOA</title>

    <link rel="stylesheet" href="css/doa.css">
    <link rel="stylesheet" href="css/doa_layout.css">
    <link rel="stylesheet" href="css/doa_componentes.css">
    <link rel="stylesheet" href="css/notificaciones.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-notificaciones">
    <?php include "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa contenido-notificaciones">
            <section class="cabecera-notificaciones">
                <div>
                    <h1>Notificaciones</h1>

                    <p>
                        Consulta avisos del centro, recordatorios de asignaturas y comunicaciones importantes.
                    </p>
                </div>

                <button type="button" class="boton-marcar-todas" id="botonMarcarTodas">
                    Marcar todas como leídas
                </button>
            </section>

            <section class="resumen-notificaciones" aria-label="Resumen de notificaciones">
                <article class="tarjeta-resumen-notificacion">
                    <span>No leídas</span>
                    <strong id="totalNoLeidas">0</strong>
                </article>

                <article class="tarjeta-resumen-notificacion">
                    <span>Tareas</span>
                    <strong id="totalTareas">0</strong>
                </article>

                <article class="tarjeta-resumen-notificacion">
                    <span>Avisos</span>
                    <strong id="totalAvisos">0</strong>
                </article>
            </section>

            <section class="notificaciones-grid">
                <div class="panel-listado-notificaciones">
                    <div class="filtros-notificaciones" aria-label="Filtros de notificaciones">
                        <button
                            type="button"
                            class="filtro-notificacion filtro-notificacion--activo"
                            data-filtro="todas"
                        >
                            Todas
                        </button>

                        <button
                            type="button"
                            class="filtro-notificacion"
                            data-filtro="no-leidas"
                        >
                            No leídas
                        </button>

                        <button
                            type="button"
                            class="filtro-notificacion"
                            data-filtro="tarea"
                        >
                            Tareas
                        </button>

                        <button
                            type="button"
                            class="filtro-notificacion"
                            data-filtro="aviso"
                        >
                            Avisos
                        </button>
                    </div>

                    <div class="lista-notificaciones" id="listaNotificaciones"></div>
                </div>

                <aside class="detalle-notificacion" aria-label="Detalle de la notificación seleccionada">
                    <div class="detalle-notificacion__cabecera">
                        <span class="etiqueta-notificacion" id="detalleTipoNotificacion">
                            Notificación
                        </span>

                        <h2 id="detalleTituloNotificacion">
                            Selecciona una notificación
                        </h2>

                        <p id="detalleMetaNotificacion">
                            --
                        </p>
                    </div>

                    <div class="detalle-notificacion__contenido">
                        <p id="detalleTextoNotificacion">
                            Elige una notificación del listado para ver su contenido.
                        </p>
                    </div>

                    <div class="detalle-notificacion__acciones">
                        <button type="button" class="boton-lectura-notificacion" id="botonLecturaNotificacion">
                            Marcar como leída
                        </button>

                        <a href="#" class="boton-accion-notificacion hidden" id="botonAccionNotificacion">
                            Ver detalle
                        </a>
                    </div>
                </aside>
            </section>
        </main>
    </div>

    <script src="js/doa_datos.js"></script>
    <script src="js/doa_layout.js"></script>
    <script src="js/notificaciones.js"></script>
</body>
</html>
