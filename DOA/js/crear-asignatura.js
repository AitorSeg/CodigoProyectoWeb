/*
    Pantalla: Crear asignatura
    Validación sencilla del formulario con datos mock.
*/

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formCrearAsignatura");

    if (form === null) {
        return;
    }

    const nombreInput = document.getElementById("nombreAsignatura");
    const codigoInput = document.getElementById("codigoAsignatura");
    const cursoInput = document.getElementById("cursoAsignatura");
    const profesorInput = document.getElementById("profesorAsignatura");
    const descripcionInput = document.getElementById("descAsignatura");
    const errorCodigo = document.getElementById("errorCodigo");
    const mensajeExito = document.getElementById("mensajeExitoAsignatura");
    const botonCancelar = document.getElementById("botonCancelarCrearAsignatura");

    const codigosExistentes = ["MAT-101", "FIS-202", "PROG-203"];

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        limpiarMensajes();

        const nombre = nombreInput.value.trim();
        const codigo = codigoInput.value.trim().toUpperCase();
        let hayErrores = false;

        if (nombre === "") {
            marcarCampoConError(nombreInput);
            hayErrores = true;
        }

        if (codigo === "") {
            errorCodigo.textContent = "Este campo es obligatorio.";
            marcarCampoConError(codigoInput);
            hayErrores = true;
        } else if (codigosExistentes.includes(codigo)) {
            errorCodigo.textContent = "Ya existe una asignatura con este código.";
            marcarCampoConError(codigoInput);
            hayErrores = true;
        }

        if (hayErrores) {
            return;
        }

        const asignaturaCreada = {
            nombre: nombre,
            codigo: codigo,
            curso: cursoInput.value,
            profesor: profesorInput.value,
            descripcion: descripcionInput.value.trim()
        };

        console.log("Asignatura creada en modo demo:", asignaturaCreada);

        mensajeExito.textContent = "Asignatura \"" + nombre + "\" creada correctamente en la demo.";
        mensajeExito.classList.remove("hidden");
        form.reset();
    });

    if (botonCancelar !== null) {
        botonCancelar.addEventListener("click", function () {
            window.history.back();
        });
    }

    function limpiarMensajes() {
        const camposConError = form.querySelectorAll(".campo-formulario--error");

        camposConError.forEach(function (campo) {
            campo.classList.remove("campo-formulario--error");
        });

        mensajeExito.classList.add("hidden");
    }

    function marcarCampoConError(input) {
        const campo = input.closest(".campo-formulario");

        if (campo !== null) {
            campo.classList.add("campo-formulario--error");
        }
    }
});
