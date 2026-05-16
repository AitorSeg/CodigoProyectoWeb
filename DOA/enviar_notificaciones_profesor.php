
<?php
    $rol_pagina = "profesor";
    $pagina_activa = "notificaciones";
    $enlace_panel = "panel_profesor.php";
    $placeholder_buscador = "Buscar asignatura, tarea...";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar notificación | DOA</title>

    <link rel="stylesheet" href="css/doa.css">
    <link rel="stylesheet" href="css/doa-layout.css">
    <link rel="stylesheet" href="css/doa-componentes.css">
    <link rel="stylesheet" href="css/enviarnotificaciones.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="pagina-doa pagina-notificaciones pagina-enviar-notificaciones">
    <?php include_once "includes/header-doa.php"; ?>

    <div class="layout-doa">
        <?php include_once "includes/barra-lateral-doa.php"; ?>

        <main class="contenido-doa contenido-enviar-notificaciones">
            <section class="cabecera-notificaciones">
                <div class="textos-cabecera-noti">
                    <h1>Enviar notificación</h1>

                    <p>
                        Redacta un aviso para una asignatura, un grupo de alumnos o toda la comunidad del módulo.
                    </p>
                </div>
            </section>

            <section class="resumen-notificaciones" aria-label="Resumen de envío de notificaciones">
                <article class="tarjeta-resumen-noti">
                    <span>Estado</span>
                    <strong>Demo</strong>
                </article>

                <article class="tarjeta-resumen-noti">
                    <span>Destinatarios</span>
                    <strong>Mock</strong>
                </article>

                <article class="tarjeta-resumen-noti">
                    <span>Guardado</span>
                    <strong>Local</strong>
                </article>
            </section>

            <section class="notificaciones-grid notificaciones-grid--envio">
                <aside class="panel-detalle-noti panel-ayuda-envio">
                    <span class="badge-noti badge-centro">Información</span>

                    <h2>Funcionamiento en el PMV</h2>

                    <p class="detalle-meta">
                        Esta pantalla representa el flujo de envío de avisos sin conectarse todavía a una base de datos.
                    </p>

                    <div class="detalle-cuerpo">
                        El formulario valida los campos obligatorios, muestra una confirmación visual y reinicia los datos.
                        En una versión posterior, este envío se guardaría en base de datos y aparecería en la bandeja del alumno.
                    </div>
                </aside>

                <section class="panel-detalle-noti panel-formulario-envio" aria-label="Formulario de envío de notificación">
                    <span class="badge-noti badge-enviada">Redacción</span>

                    <h2>Redactar notificación</h2>

                    <p class="detalle-meta">
                        Completa los datos del aviso que se enviaría a los destinatarios seleccionados.
                    </p>

                    <form id="formularioNotificacion" novalidate>
                        <div class="formulario-doble">
                            <div class="campo-formulario">
                                <label for="selectAsignaturaNoti">Asignatura destino *</label>
                                <select id="selectAsignaturaNoti" required>
                                    <option value="">Selecciona...</option>
                                    <option value="Programación II">Programación II</option>
                                    <option value="Matemáticas">Matemáticas</option>
                                    <option value="Física">Física</option>
                                    <option value="Centro">Aviso general del centro</option>
                                </select>
                                <p class="mensaje-error-campo" id="errorAsignaturaNoti"></p>
                            </div>

                            <div class="campo-formulario">
                                <label for="selectAudiencia">Enviar a *</label>
                                <select id="selectAudiencia" required>
                                    <option value="">Selecciona...</option>
                                    <option value="Todos los alumnos">Todos los alumnos</option>
                                    <option value="Con tareas pendientes">Con tareas pendientes</option>
                                    <option value="Grupo concreto">Grupo concreto</option>
                                    <option value="Profesorado">Profesorado</option>
                                </select>
                                <p class="mensaje-error-campo" id="errorAudienciaNoti"></p>
                            </div>
                        </div>

                        <div class="campo-formulario">
                            <label for="inputAsunto">Asunto *</label>
                            <input
                                id="inputAsunto"
                                type="text"
                                placeholder="Ej. Cambio de fecha para el Parcial 2"
                                required
                            >
                            <p class="mensaje-error-campo" id="errorAsuntoNoti"></p>
                        </div>

                        <div class="campo-formulario">
                            <label for="inputMensaje">Mensaje *</label>
                            <textarea
                                id="inputMensaje"
                                rows="6"
                                placeholder="Escribe aquí los detalles del anuncio..."
                                required
                            ></textarea>
                            <p class="mensaje-error-campo" id="errorMensajeNoti"></p>
                        </div>

                        <div class="campo-formulario">
                            <label>Nivel de importancia</label>

                            <div class="contenedor-radios-importancia">
                                <label class="radio-importancia neutral">
                                    <input checked name="importancia" type="radio" value="Informativo">
                                    <span>Informativo</span>
                                </label>

                                <label class="radio-importancia recordatorio">
                                    <input name="importancia" type="radio" value="Recordatorio">
                                    <span>Recordatorio</span>
                                </label>

                                <label class="radio-importancia urgente">
                                    <input name="importancia" type="radio" value="Urgente">
                                    <span>Urgente</span>
                                </label>
                            </div>
                        </div>

                        <div class="alerta-exito oculto" id="alertaExito">
                            Notificación simulada correctamente. No se ha guardado en base de datos.
                        </div>

                        <div class="acciones-redactar">
                            <button class="boton-primario-noti btn-ancho-total" id="btnEnviarNoti" type="submit">
                                Enviar notificación
                            </button>

                            <button class="boton-secundario-noti btn-ancho-total" id="btnCancelarNoti" type="button">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </section>
            </section>
        </main>
    </div>

    <script src="js/doa_layout.js"></script>
    <script src="js/enviar_notificaciones.js"></script>
</body>
</html>
