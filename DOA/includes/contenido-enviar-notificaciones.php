<?php
// Inicio configuración de envío

$id_autor = (int) $_SESSION["doa_id_usuario"];
$es_profesor = $rol_pagina === "profesor";
$es_secretaria = $rol_pagina === "secretaria";
$url_pagina_envio = $es_profesor ? "enviar_notificaciones_profesor.php" : "enviar_notificaciones_secretaria.php";

$errores_envio = [];
$asunto_formulario = "";
$mensaje_formulario = "";
$audiencia_formulario = "";
$importancia_formulario = "Informativo";
$id_asignatura_formulario = 0;

$notificacion_enviada = ($_GET["enviado"] ?? "") === "ok";
$total_destinatarios_enviados = isset($_GET["destinatarios"]) ? (int) $_GET["destinatarios"] : 0;

// Fin configuración de envío


// Inicio funciones auxiliares

function obtener_tipo_notificacion_envio($importancia)
{
    if ($importancia === "Informativo") {
        return "anuncio";
    }

    return "aviso";
}

function obtener_alcance_envio($id_asignatura)
{
    if ((int) $id_asignatura > 0) {
        return "asignatura";
    }

    return "centro";
}

function obtener_url_destino_envio($id_asignatura)
{
    if ((int) $id_asignatura > 0) {
        return "detalle_asignatura.php?id_asignatura=" . (int) $id_asignatura;
    }

    return null;
}

// Fin funciones auxiliares


// Inicio consulta de asignaturas disponibles

if ($es_profesor) {
    $consulta_asignaturas_envio = $pdo->prepare("
        SELECT
            a.id_asignatura,
            a.nombre,
            a.codigo,
            a.curso,
            a.grupo
        FROM asignaturas a
        INNER JOIN usuarios_asignaturas ua
            ON ua.id_asignatura = a.id_asignatura
            AND ua.id_usuario = :id_autor
            AND ua.rol_asignatura = 'profesor'
            AND ua.estado = 'activa'
        WHERE a.estado = 'activa'
        ORDER BY a.nombre ASC
    ");

    $consulta_asignaturas_envio->execute([
        "id_autor" => $id_autor
    ]);
} else {
    $consulta_asignaturas_envio = $pdo->query("
        SELECT
            id_asignatura,
            nombre,
            codigo,
            curso,
            grupo
        FROM asignaturas
        WHERE estado = 'activa'
        ORDER BY nombre ASC
    ");
}

$asignaturas_envio = $consulta_asignaturas_envio->fetchAll();
$ids_asignaturas_validas = array_map(
    fn($asignatura) => (int) $asignatura["id_asignatura"],
    $asignaturas_envio
);

// Fin consulta de asignaturas disponibles


// Inicio procesamiento del formulario

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $asunto_formulario = trim($_POST["asunto"] ?? "");
    $mensaje_formulario = trim($_POST["mensaje"] ?? "");
    $audiencia_formulario = $_POST["audiencia"] ?? "";
    $importancia_formulario = $_POST["importancia"] ?? "Informativo";
    $id_asignatura_formulario = (int) ($_POST["id_asignatura"] ?? 0);

    $audiencias_validas = $es_profesor
        ? ["alumnos_asignatura", "alumnos_pendientes"]
        : ["todos_alumnos", "alumnos_asignatura"];

    $importancias_validas = ["Informativo", "Recordatorio", "Urgente"];

    if ($asunto_formulario === "") {
        $errores_envio[] = "Escribe un asunto para la notificación.";
    }

    if ($mensaje_formulario === "") {
        $errores_envio[] = "Escribe el mensaje de la notificación.";
    }

    if (!in_array($audiencia_formulario, $audiencias_validas, true)) {
        $errores_envio[] = "Selecciona un tipo de destinatario válido.";
    }

    if (!in_array($importancia_formulario, $importancias_validas, true)) {
        $errores_envio[] = "Selecciona un nivel de importancia válido.";
    }

    if ($es_profesor && !in_array($id_asignatura_formulario, $ids_asignaturas_validas, true)) {
        $errores_envio[] = "Selecciona una asignatura que tengas asignada.";
    }

    if ($es_secretaria && $audiencia_formulario === "alumnos_asignatura" && !in_array($id_asignatura_formulario, $ids_asignaturas_validas, true)) {
        $errores_envio[] = "Selecciona una asignatura para enviar el aviso a sus alumnos.";
    }

    if (count($errores_envio) === 0) {
        if ($es_profesor && $audiencia_formulario === "alumnos_asignatura") {
            $consulta_destinatarios = $pdo->prepare("
                SELECT DISTINCT u.id_usuario
                FROM usuarios_asignaturas ua
                INNER JOIN usuarios u
                    ON u.id_usuario = ua.id_usuario
                    AND u.rol = 'alumno'
                    AND u.estado = 'activo'
                WHERE ua.id_asignatura = :id_asignatura
                AND ua.rol_asignatura = 'alumno'
                AND ua.estado = 'activa'
                ORDER BY u.nombre ASC, u.apellidos ASC
            ");

            $consulta_destinatarios->execute([
                "id_asignatura" => $id_asignatura_formulario
            ]);
        }

        if ($es_profesor && $audiencia_formulario === "alumnos_pendientes") {
            $consulta_destinatarios = $pdo->prepare("
                SELECT DISTINCT u.id_usuario
                FROM usuarios_asignaturas ua
                INNER JOIN usuarios u
                    ON u.id_usuario = ua.id_usuario
                    AND u.rol = 'alumno'
                    AND u.estado = 'activo'
                INNER JOIN actividades_evaluables ae
                    ON ae.id_asignatura = ua.id_asignatura
                    AND ae.id_profesor = :id_profesor
                    AND ae.tipo_actividad IN ('tarea', 'practica')
                    AND ae.visible = 1
                    AND ae.estado = 'publicada'
                LEFT JOIN entregas e
                    ON e.id_actividad = ae.id_actividad
                    AND e.id_alumno = u.id_usuario
                WHERE ua.id_asignatura = :id_asignatura
                AND ua.rol_asignatura = 'alumno'
                AND ua.estado = 'activa'
                AND e.id_entrega IS NULL
                ORDER BY u.nombre ASC, u.apellidos ASC
            ");

            $consulta_destinatarios->execute([
                "id_profesor" => $id_autor,
                "id_asignatura" => $id_asignatura_formulario
            ]);
        }

        if ($es_secretaria && $audiencia_formulario === "todos_alumnos") {
            $consulta_destinatarios = $pdo->query("
                SELECT id_usuario
                FROM usuarios
                WHERE rol = 'alumno'
                AND estado = 'activo'
                ORDER BY nombre ASC, apellidos ASC
            ");
        }

        if ($es_secretaria && $audiencia_formulario === "alumnos_asignatura") {
            $consulta_destinatarios = $pdo->prepare("
                SELECT DISTINCT u.id_usuario
                FROM usuarios_asignaturas ua
                INNER JOIN usuarios u
                    ON u.id_usuario = ua.id_usuario
                    AND u.rol = 'alumno'
                    AND u.estado = 'activo'
                WHERE ua.id_asignatura = :id_asignatura
                AND ua.rol_asignatura = 'alumno'
                AND ua.estado = 'activa'
                ORDER BY u.nombre ASC, u.apellidos ASC
            ");

            $consulta_destinatarios->execute([
                "id_asignatura" => $id_asignatura_formulario
            ]);
        }

        $destinatarios = $consulta_destinatarios->fetchAll();

        if (count($destinatarios) === 0) {
            $errores_envio[] = "No hay destinatarios para la selección realizada.";
        }
    }

    if (count($errores_envio) === 0) {
        $tipo_comunicado = $es_secretaria ? "aviso_oficial" : "anuncio";
        $tipo_notificacion = obtener_tipo_notificacion_envio($importancia_formulario);
        $alcance = obtener_alcance_envio($id_asignatura_formulario);
        $url_destino = obtener_url_destino_envio($id_asignatura_formulario);
        $contenido_comunicado = "Importancia: " . $importancia_formulario . "\n\n" . $mensaje_formulario;

        $pdo->beginTransaction();

        try {
            $insertar_comunicado = $pdo->prepare("
                INSERT INTO comunicados
                    (
                        id_autor,
                        id_asignatura,
                        tipo_comunicado,
                        alcance,
                        titulo,
                        contenido,
                        visible,
                        fecha_publicacion
                    )
                VALUES
                    (
                        :id_autor,
                        :id_asignatura,
                        :tipo_comunicado,
                        :alcance,
                        :titulo,
                        :contenido,
                        1,
                        NOW()
                    )
            ");

            $insertar_comunicado->execute([
                "id_autor" => $id_autor,
                "id_asignatura" => $id_asignatura_formulario > 0 ? $id_asignatura_formulario : null,
                "tipo_comunicado" => $tipo_comunicado,
                "alcance" => $alcance,
                "titulo" => $asunto_formulario,
                "contenido" => $contenido_comunicado
            ]);

            $id_comunicado = (int) $pdo->lastInsertId();

            $insertar_notificacion = $pdo->prepare("
                INSERT INTO notificaciones
                    (
                        id_usuario_destino,
                        id_usuario_creador,
                        id_comunicado,
                        tipo_notificacion,
                        titulo,
                        mensaje,
                        url_destino
                    )
                VALUES
                    (
                        :id_usuario_destino,
                        :id_usuario_creador,
                        :id_comunicado,
                        :tipo_notificacion,
                        :titulo,
                        :mensaje,
                        :url_destino
                    )
            ");

            foreach ($destinatarios as $destinatario) {
                $insertar_notificacion->execute([
                    "id_usuario_destino" => (int) $destinatario["id_usuario"],
                    "id_usuario_creador" => $id_autor,
                    "id_comunicado" => $id_comunicado,
                    "tipo_notificacion" => $tipo_notificacion,
                    "titulo" => $asunto_formulario,
                    "mensaje" => $mensaje_formulario,
                    "url_destino" => $url_destino
                ]);
            }

            $pdo->commit();

            header("Location: " . $url_pagina_envio . "?enviado=ok&destinatarios=" . count($destinatarios));
            exit;
        } catch (Throwable $error) {
            $pdo->rollBack();
            throw $error;
        }
    }
}

// Fin procesamiento del formulario


// Inicio datos derivados

$total_asignaturas_envio = count($asignaturas_envio);
$texto_estado_envio = $notificacion_enviada ? "Enviado" : "Real";
$texto_destinatarios_envio = $notificacion_enviada ? (string) $total_destinatarios_enviados : "BD";

$texto_ayuda_envio = $es_profesor
    ? "El aviso se enviará a alumnos de una de tus asignaturas. También puedes limitarlo a quienes tengan tareas pendientes."
    : "El aviso se enviará a todos los alumnos del módulo o a los alumnos de una asignatura concreta.";

// Fin datos derivados
?>

<main class="contenido-doa contenido-enviar-notificaciones">
    <!-- Inicio cabecera de envío -->

    <section class="cabecera-notificaciones">
        <div class="textos-cabecera-noti">
            <h1>Enviar notificación</h1>

            <p>
                Redacta un aviso para que aparezca en la bandeja de notificaciones del alumno.
            </p>
        </div>
    </section>

    <!-- Fin cabecera de envío -->


    <!-- Inicio resumen de envío -->

    <section class="resumen-metricas resumen-metricas--tres resumen-metricas--compacto resumen-metricas--con-padding" aria-label="Resumen de envío de notificaciones">
        <article class="tarjeta-metrica">
            <span>Estado</span>
            <strong><?php echo limpiar_texto_doa($texto_estado_envio); ?></strong>
        </article>

        <article class="tarjeta-metrica">
            <span>Destinatarios</span>
            <strong><?php echo limpiar_texto_doa($texto_destinatarios_envio); ?></strong>
        </article>

        <article class="tarjeta-metrica">
            <span>Asignaturas</span>
            <strong><?php echo $total_asignaturas_envio; ?></strong>
        </article>
    </section>

    <!-- Fin resumen de envío -->


    <!-- Inicio formulario y ayuda -->

    <section class="notificaciones-grid notificaciones-grid--envio">
        <aside class="panel-detalle-noti panel-ayuda-envio">
            <span class="badge-noti badge-centro">Información</span>

            <h2>Funcionamiento del envío</h2>

            <p class="detalle-meta">
                <?php echo limpiar_texto_doa($texto_ayuda_envio); ?>
            </p>

            <div class="detalle-cuerpo">
                El comunicado se guarda en la tabla de comunicados y se crea una notificación individual para cada destinatario.
            </div>
        </aside>

        <section class="panel-detalle-noti panel-formulario-envio" aria-label="Formulario de envío de notificación">
            <span class="badge-noti badge-enviada">Redacción</span>

            <h2>Redactar notificación</h2>

            <p class="detalle-meta">
                Completa los datos del aviso que se enviará a los destinatarios seleccionados.
            </p>

            <?php if ($notificacion_enviada) { ?>
                <div class="alerta-exito">
                    Notificación enviada correctamente a <?php echo $total_destinatarios_enviados; ?> destinatarios.
                </div>
            <?php } ?>

            <?php if (count($errores_envio) > 0) { ?>
                <div class="alerta-error">
                    <?php echo limpiar_texto_doa($errores_envio[0]); ?>
                </div>
            <?php } ?>

            <form method="post">
                <div class="formulario-doble">
                    <div class="campo-formulario">
                        <label for="selectAsignaturaNoti">Asignatura destino *</label>

                        <select id="selectAsignaturaNoti" name="id_asignatura" required>
                            <?php if ($es_secretaria) { ?>
                                <option value="0" <?php echo $id_asignatura_formulario === 0 ? "selected" : ""; ?>>
                                    Aviso general del centro
                                </option>
                            <?php } else { ?>
                                <option value="">Selecciona...</option>
                            <?php } ?>

                            <?php foreach ($asignaturas_envio as $asignatura) { ?>
                                <?php $id_asignatura_opcion = (int) $asignatura["id_asignatura"]; ?>

                                <option value="<?php echo $id_asignatura_opcion; ?>" <?php echo $id_asignatura_formulario === $id_asignatura_opcion ? "selected" : ""; ?>>
                                    <?php echo limpiar_texto_doa($asignatura["nombre"]); ?>
                                    · <?php echo limpiar_texto_doa($asignatura["curso"]); ?>
                                    · Grupo <?php echo limpiar_texto_doa($asignatura["grupo"]); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="campo-formulario">
                        <label for="selectAudiencia">Enviar a *</label>

                        <select id="selectAudiencia" name="audiencia" required>
                            <option value="">Selecciona...</option>

                            <?php if ($es_profesor) { ?>
                                <option value="alumnos_asignatura" <?php echo $audiencia_formulario === "alumnos_asignatura" ? "selected" : ""; ?>>
                                    Todos los alumnos de la asignatura
                                </option>

                                <option value="alumnos_pendientes" <?php echo $audiencia_formulario === "alumnos_pendientes" ? "selected" : ""; ?>>
                                    Alumnos con tareas pendientes
                                </option>
                            <?php } ?>

                            <?php if ($es_secretaria) { ?>
                                <option value="todos_alumnos" <?php echo $audiencia_formulario === "todos_alumnos" ? "selected" : ""; ?>>
                                    Todos los alumnos
                                </option>

                                <option value="alumnos_asignatura" <?php echo $audiencia_formulario === "alumnos_asignatura" ? "selected" : ""; ?>>
                                    Alumnos de una asignatura
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="campo-formulario">
                    <label for="inputAsunto">Asunto *</label>

                    <input
                        id="inputAsunto"
                        name="asunto"
                        type="text"
                        maxlength="150"
                        value="<?php echo limpiar_texto_doa($asunto_formulario); ?>"
                        placeholder="Ej. Cambio de fecha para el Parcial 2"
                        required>
                </div>

                <div class="campo-formulario">
                    <label for="inputMensaje">Mensaje *</label>

                    <textarea
                        id="inputMensaje"
                        name="mensaje"
                        rows="6"
                        placeholder="Escribe aquí los detalles del aviso..."
                        required><?php echo limpiar_texto_doa($mensaje_formulario); ?></textarea>
                </div>

                <div class="campo-formulario">
                    <label>Nivel de importancia</label>

                    <div class="contenedor-radios-importancia">
                        <label class="radio-importancia neutral">
                            <input <?php echo $importancia_formulario === "Informativo" ? "checked" : ""; ?> name="importancia" type="radio" value="Informativo">
                            <span>Informativo</span>
                        </label>

                        <label class="radio-importancia recordatorio">
                            <input <?php echo $importancia_formulario === "Recordatorio" ? "checked" : ""; ?> name="importancia" type="radio" value="Recordatorio">
                            <span>Recordatorio</span>
                        </label>

                        <label class="radio-importancia urgente">
                            <input <?php echo $importancia_formulario === "Urgente" ? "checked" : ""; ?> name="importancia" type="radio" value="Urgente">
                            <span>Urgente</span>
                        </label>
                    </div>
                </div>

                <div class="acciones-redactar">
                    <button class="boton-primario-noti btn-ancho-total" type="submit">
                        Enviar notificación
                    </button>

                    <a class="boton-secundario-noti btn-ancho-total" href="<?php echo limpiar_texto_doa($url_pagina_envio); ?>">
                        Cancelar
                    </a>
                </div>
            </form>
        </section>
    </section>

    <!-- Fin formulario y ayuda -->
</main>