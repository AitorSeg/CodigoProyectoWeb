document.addEventListener("DOMContentLoaded", function () {
    const formulario = document.getElementById("formCrearAsignatura");

    if (formulario === null) {
        return;
    }

    formulario.addEventListener("submit", function (evento) {
        evento.preventDefault();

        limpiarErroresCrearAsignatura();

        const nombre = document.getElementById("nombreAsignatura").value.trim();
        const codigo = document.getElementById("codigoAsignatura").value.trim();
        const curso = document.getElementById("cursoAsignatura").value;
        const grupo = document.getElementById("grupoAsignatura").value;
        const descripcion = document.getElementById("descripcionAsignatura").value.trim();
        const estado = document.getElementById("estadoAsignatura").value;

        let formularioValido = true;

        if (nombre === "") {
            mostrarErrorCrearAsignatura("errorNombreAsignatura", "Introduce el nombre de la asignatura.");
            formularioValido = false;
        }

        if (codigo === "") {
            mostrarErrorCrearAsignatura("errorCodigoAsignatura", "Introduce el código de la asignatura.");
            formularioValido = false;
        }

        if (curso === "") {
            mostrarErrorCrearAsignatura("errorCursoAsignatura", "Selecciona el curso.");
            formularioValido = false;
        }

        if (grupo === "") {
            mostrarErrorCrearAsignatura("errorGrupoAsignatura", "Selecciona el grupo.");
            formularioValido = false;
        }

        if (!formularioValido) {
            return;
        }

        const nuevaAsignatura = {
            id: crearIdAsignatura(nombre, grupo),
            nombre: nombre,
            codigo: codigo,
            curso: curso,
            grupo: grupo,
            descripcion: descripcion,
            estado: estado,
            profesor: "Pendiente",
            alumnos: 0
        };

        guardarAsignaturaDemo(nuevaAsignatura);
        mostrarMensajeAsignaturaCreada();
        formulario.reset();
    });
});

function mostrarErrorCrearAsignatura(idElemento, mensaje) {
    const elemento = document.getElementById(idElemento);

    if (elemento !== null) {
        elemento.textContent = mensaje;
    }
}

function limpiarErroresCrearAsignatura() {
    const errores = document.querySelectorAll(".mensaje-error-campo");

    errores.forEach(function (error) {
        error.textContent = "";
    });
}

function crearIdAsignatura(nombre, grupo) {
    return nombre
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-|-$/g, "") + "-" + grupo.toLowerCase();
}

function guardarAsignaturaDemo(asignatura) {
    const asignaturasGuardadas = localStorage.getItem("doaAsignaturasSecretaria");
    let asignaturas = [];

    if (asignaturasGuardadas !== null) {
        try {
            asignaturas = JSON.parse(asignaturasGuardadas);
        } catch (error) {
            asignaturas = [];
        }
    }

    asignaturas.push(asignatura);
    localStorage.setItem("doaAsignaturasSecretaria", JSON.stringify(asignaturas));
}

function mostrarMensajeAsignaturaCreada() {
    const mensaje = document.getElementById("mensajeFormularioAsignatura");

    if (mensaje === null) {
        return;
    }

    mensaje.classList.remove("mensaje-formulario-secretaria--oculto");

    setTimeout(function () {
        mensaje.classList.add("mensaje-formulario-secretaria--oculto");
    }, 3500);
}