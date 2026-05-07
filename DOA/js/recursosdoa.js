/*
    Pantalla: Recursos de asignatura
    Carga datos de asignatura y simula la subida de recursos.
*/

document.addEventListener("DOMContentLoaded", function () {
    cargarDatosAsignatura();
    prepararFormularioRecursos();
});

function cargarDatosAsignatura() {
    if (!window.DOA_ASIGNATURAS || !window.obtenerAsignaturaSeleccionada) {
        return;
    }

    const idAsignatura = window.obtenerAsignaturaSeleccionada();
    const datos = window.DOA_ASIGNATURAS[idAsignatura] || window.DOA_ASIGNATURAS.matematicas;

    const tituloAsignatura = document.getElementById("tituloAsignatura");
    const profesorAsignatura = document.getElementById("profesorAsignatura");
    const unidadActualTextoAsignatura = document.getElementById("unidadActualTextoAsignatura");

    document.title = "Recursos de " + datos.nombre + " | DOA";

    if (tituloAsignatura !== null) {
        tituloAsignatura.textContent = datos.nombre;
    }

    if (profesorAsignatura !== null) {
        profesorAsignatura.textContent = datos.profesor;
    }

    if (unidadActualTextoAsignatura !== null) {
        unidadActualTextoAsignatura.textContent = datos.unidadActualTexto;
    }
}

function prepararFormularioRecursos() {
    const form = document.getElementById("formSubirRecurso");

    if (form === null) {
        return;
    }

    const tituloInput = document.getElementById("tituloRecurso");
    const archivoInput = document.getElementById("archivoRecurso");
    const listaRecursos = document.getElementById("listaRecursos");
    const estadoVacio = document.getElementById("estadoVacioRecursos");
    const mensajeExito = document.getElementById("mensajeExitoRecurso");

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        limpiarErrores(form, mensajeExito);

        const titulo = tituloInput.value.trim();
        const archivo = archivoInput.files[0];
        let hayErrores = false;

        if (titulo === "") {
            marcarCampoConError(tituloInput);
            hayErrores = true;
        }

        if (!archivo) {
            marcarCampoConError(archivoInput);
            hayErrores = true;
        }

        if (hayErrores) {
            return;
        }

        const nuevoRecurso = crearElementoRecurso(titulo, archivo.name);
        listaRecursos.prepend(nuevoRecurso);

        estadoVacio.classList.add("hidden");
        mensajeExito.textContent = "Recurso \"" + titulo + "\" añadido correctamente en la demo.";
        mensajeExito.classList.remove("hidden");
        form.reset();
    });
}

function crearElementoRecurso(titulo, nombreArchivo) {
    const recurso = document.createElement("li");
    const info = document.createElement("div");
    const tituloRecurso = document.createElement("strong");
    const meta = document.createElement("span");
    const boton = document.createElement("a");

    recurso.className = "recurso-publicado";
    info.className = "recurso-publicado__info";
    boton.className = "boton-secundario-panel";
    boton.href = "#";

    tituloRecurso.textContent = titulo;
    meta.textContent = nombreArchivo + " · Publicado ahora";
    boton.textContent = "Descargar";

    info.append(tituloRecurso, meta);
    recurso.append(info, boton);

    return recurso;
}

function limpiarErrores(form, mensajeExito) {
    const camposConError = form.querySelectorAll(".campo-formulario--error");

    camposConError.forEach(function (campo) {
        campo.classList.remove("campo-formulario--error");
    });

    if (mensajeExito !== null) {
        mensajeExito.classList.add("hidden");
    }
}

function marcarCampoConError(input) {
    const campo = input.closest(".campo-formulario");

    if (campo !== null) {
        campo.classList.add("campo-formulario--error");
    }
}
