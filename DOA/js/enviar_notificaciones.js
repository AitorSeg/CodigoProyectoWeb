/*
    Pantalla: Enviar notificaciones
*/

const formulario_notificacion = document.getElementById("formularioNotificacion");
const boton_cancelar_notificacion = document.getElementById("btnCancelarNoti");

formulario_notificacion.addEventListener("submit", function (evento) {
    evento.preventDefault();
    procesar_envio_notificacion();
});

boton_cancelar_notificacion.addEventListener("click", function () {
    limpiar_formulario_envio_notificaciones();
});

function procesar_envio_notificacion() {
    limpiar_errores_envio_notificaciones();

    const asignatura = document.getElementById("selectAsignaturaNoti").value.trim();
    const audiencia = document.getElementById("selectAudiencia").value.trim();
    const asunto = document.getElementById("inputAsunto").value.trim();
    const mensaje = document.getElementById("inputMensaje").value.trim();

    let formulario_valido = true;

    if (asignatura === "") {
        mostrar_error_envio_notificaciones("errorAsignaturaNoti", "Selecciona una asignatura o ámbito.");
        formulario_valido = false;
    }

    if (audiencia === "") {
        mostrar_error_envio_notificaciones("errorAudienciaNoti", "Selecciona los destinatarios.");
        formulario_valido = false;
    }

    if (asunto === "") {
        mostrar_error_envio_notificaciones("errorAsuntoNoti", "Introduce un asunto.");
        formulario_valido = false;
    }

    if (mensaje === "") {
        mostrar_error_envio_notificaciones("errorMensajeNoti", "Escribe el mensaje de la notificación.");
        formulario_valido = false;
    }

    if (!formulario_valido) {
        return;
    }

    guardar_ultima_notificacion_simulada(asignatura, audiencia, asunto, mensaje);
    mostrar_confirmacion_envio_notificaciones();
    limpiar_formulario_envio_notificaciones();
}

function mostrar_error_envio_notificaciones(id_elemento, texto) {
    document.getElementById(id_elemento).textContent = texto;
}

function limpiar_errores_envio_notificaciones() {
    const errores = document.querySelectorAll(".mensaje-error-campo");

    errores.forEach(function (error) {
        error.textContent = "";
    });
}

function guardar_ultima_notificacion_simulada(asignatura, audiencia, asunto, mensaje) {
    const importancia = document.querySelector('input[name="importancia"]:checked').value;

    const notificacion = {
        asignatura: asignatura,
        audiencia: audiencia,
        asunto: asunto,
        mensaje: mensaje,
        importancia: importancia,
        fecha_simulada: new Date().toISOString()
    };

    localStorage.setItem("doaUltimaNotificacionSimulada", JSON.stringify(notificacion));
}

function mostrar_confirmacion_envio_notificaciones() {
    const alerta = document.getElementById("alertaExito");

    alerta.classList.remove("oculto");

    setTimeout(function () {
        alerta.classList.add("oculto");
    }, 3500);
}

function limpiar_formulario_envio_notificaciones() {
    formulario_notificacion.reset();
    limpiar_errores_envio_notificaciones();
}