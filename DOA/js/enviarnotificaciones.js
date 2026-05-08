/*
    Pantalla: Enviar notificaciones
    Uso: Profesor y Secretaría.
*/

document.addEventListener("DOMContentLoaded", function () {
    configurarNavegacionEnvioNotificaciones();
    prepararFormularioEnvioNotificaciones();
});

function configurarNavegacionEnvioNotificaciones() {
    const usuario = obtenerUsuarioDemoEnvioNotificaciones();

    if (usuario === null) {
        return;
    }

    const rol = usuario.rol;

    if (rol === "Secretaría" || rol === "PAS") {
        document.body.classList.add("pagina-enviar-notificaciones--secretaria");

        configurarRutasEnvioNotificaciones("panel_secretaria.html", "asignaturas_secretaria.html", "Asignaturas");
        mostrarElementoEnvioNotificaciones("enlaceAsignacionesEnvioNotificaciones");

        return;
    }

    configurarRutasEnvioNotificaciones("panel_profesor.html", "asignaturas_profesor.html", "Mis Asignaturas");
}

function configurarRutasEnvioNotificaciones(rutaPanel, rutaAsignaturas, textoAsignaturas) {
    const enlaceLogo = document.getElementById("enlaceLogoEnvioNotificaciones");
    const enlacePanel = document.getElementById("enlacePanelEnvioNotificaciones");
    const enlaceAsignaturas = document.getElementById("enlaceAsignaturasEnvioNotificaciones");
    const textoAsignaturasElemento = document.getElementById("textoAsignaturasEnvioNotificaciones");

    if (enlaceLogo !== null) {
        enlaceLogo.href = rutaPanel;
    }

    if (enlacePanel !== null) {
        enlacePanel.href = rutaPanel;
    }

    if (enlaceAsignaturas !== null) {
        enlaceAsignaturas.href = rutaAsignaturas;
    }

    if (textoAsignaturasElemento !== null) {
        textoAsignaturasElemento.textContent = textoAsignaturas;
    }
}

function mostrarElementoEnvioNotificaciones(idElemento) {
    const elemento = document.getElementById(idElemento);

    if (elemento !== null) {
        elemento.hidden = false;
    }
}

function obtenerUsuarioDemoEnvioNotificaciones() {
    const usuarioGuardado = sessionStorage.getItem("usuarioDemoDOA");

    if (usuarioGuardado === null) {
        return null;
    }

    try {
        return JSON.parse(usuarioGuardado);
    } catch (error) {
        return null;
    }
}

function prepararFormularioEnvioNotificaciones() {
    const formulario = document.getElementById("formularioNotificacion");
    const botonCancelar = document.getElementById("btnCancelarNoti");

    if (formulario !== null) {
        formulario.addEventListener("submit", function (evento) {
            evento.preventDefault();
            procesarEnvioNotificacion(formulario);
        });
    }

    if (botonCancelar !== null) {
        botonCancelar.addEventListener("click", function () {
            limpiarFormularioEnvioNotificaciones(formulario);
        });
    }
}

function procesarEnvioNotificacion(formulario) {
    limpiarErroresEnvioNotificaciones();

    const asignatura = obtenerValorCampoEnvioNotificaciones("selectAsignaturaNoti");
    const audiencia = obtenerValorCampoEnvioNotificaciones("selectAudiencia");
    const asunto = obtenerValorCampoEnvioNotificaciones("inputAsunto");
    const mensaje = obtenerValorCampoEnvioNotificaciones("inputMensaje");

    let formularioValido = true;

    if (asignatura === "") {
        mostrarErrorEnvioNotificaciones("errorAsignaturaNoti", "Selecciona una asignatura o ámbito.");
        formularioValido = false;
    }

    if (audiencia === "") {
        mostrarErrorEnvioNotificaciones("errorAudienciaNoti", "Selecciona los destinatarios.");
        formularioValido = false;
    }

    if (asunto === "") {
        mostrarErrorEnvioNotificaciones("errorAsuntoNoti", "Introduce un asunto.");
        formularioValido = false;
    }

    if (mensaje === "") {
        mostrarErrorEnvioNotificaciones("errorMensajeNoti", "Escribe el mensaje de la notificación.");
        formularioValido = false;
    }

    if (!formularioValido) {
        return;
    }

    guardarUltimaNotificacionSimulada(asignatura, audiencia, asunto, mensaje);
    mostrarConfirmacionEnvioNotificaciones();
    limpiarFormularioEnvioNotificaciones(formulario);
}

function obtenerValorCampoEnvioNotificaciones(idCampo) {
    const campo = document.getElementById(idCampo);

    if (campo === null) {
        return "";
    }

    return campo.value.trim();
}

function mostrarErrorEnvioNotificaciones(idElemento, texto) {
    const elemento = document.getElementById(idElemento);

    if (elemento !== null) {
        elemento.textContent = texto;
    }
}

function limpiarErroresEnvioNotificaciones() {
    const errores = document.querySelectorAll(".mensaje-error-campo");

    errores.forEach(function (error) {
        error.textContent = "";
    });
}

function guardarUltimaNotificacionSimulada(asignatura, audiencia, asunto, mensaje) {
    const importanciaSeleccionada = document.querySelector('input[name="importancia"]:checked');
    const importancia = importanciaSeleccionada !== null ? importanciaSeleccionada.value : "Informativo";

    const notificacion = {
        asignatura: asignatura,
        audiencia: audiencia,
        asunto: asunto,
        mensaje: mensaje,
        importancia: importancia,
        fechaSimulada: new Date().toISOString()
    };

    localStorage.setItem("doaUltimaNotificacionSimulada", JSON.stringify(notificacion));
}

function mostrarConfirmacionEnvioNotificaciones() {
    const alerta = document.getElementById("alertaExito");

    if (alerta === null) {
        return;
    }

    alerta.classList.remove("oculto");

    setTimeout(function () {
        alerta.classList.add("oculto");
    }, 3500);
}

function limpiarFormularioEnvioNotificaciones(formulario) {
    if (formulario !== null) {
        formulario.reset();
    }

    limpiarErroresEnvioNotificaciones();
}
