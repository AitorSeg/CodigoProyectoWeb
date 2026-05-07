/*
    Pantalla: Detalle de examen
*/

document.addEventListener("DOMContentLoaded", function () {
    const examen = window.obtenerExamenActual();

    if (!examen) {
        return;
    }

    cargarDetalleExamen(examen);
});

function ponerTexto(idElemento, texto) {
    const elemento = document.getElementById(idElemento);

    if (elemento !== null) {
        elemento.textContent = texto;
    }
}

function cargarDetalleExamen(examen) {
    document.title = examen.nombre + " | DOA";

    ponerTexto("tituloExamen", examen.nombre);
    ponerTexto("asignaturaExamen", examen.asignatura);
    ponerTexto("fechaExamen", examen.fechaCompleta);

    ponerTexto("nombreExamen", examen.nombre);
    ponerTexto("descripcionExamen", examen.descripcion);
    ponerTexto("estadoExamen", examen.estado);

    ponerTexto("aperturaExamen", examen.fechaApertura);
    ponerTexto("cierreExamen", examen.fechaCierre);
    ponerTexto("duracionExamen", examen.duracion);
    ponerTexto("preguntasExamen", examen.preguntas);
    ponerTexto("intentosExamen", examen.intentos);

    cargarEstadoExamen(examen);
    cargarTemasExamen(examen.temas);
    prepararBotonRealizarExamen(examen);
}

function cargarEstadoExamen(examen) {
    const estado = document.getElementById("estadoExamen");

    if (estado === null) {
        return;
    }

    estado.classList.remove("estado-detalle-examen--cerrado", "estado-detalle-examen--proximo");

    if (examen.estadoFiltro === "cerrado") {
        estado.classList.add("estado-detalle-examen--cerrado");
    }

    if (examen.estadoFiltro === "proximo") {
        estado.classList.add("estado-detalle-examen--proximo");
    }
}

function cargarTemasExamen(temas) {
    const lista = document.getElementById("temasExamen");

    if (lista === null) {
        return;
    }

    lista.innerHTML = "";

    temas.forEach(function (tema) {
        const elemento = document.createElement("li");

        elemento.textContent = tema;
        lista.appendChild(elemento);
    });
}

function prepararBotonRealizarExamen(examen) {
    const boton = document.getElementById("botonRealizarExamen");
    const mensaje = document.getElementById("mensajeAccesoExamen");

    if (boton === null || mensaje === null) {
        return;
    }

    if (examen.estadoFiltro === "abierto") {
        boton.textContent = "Realizar examen";
        boton.href = "realizar_examen.html";
        boton.classList.remove("boton-realizar-examen--desactivado");
        mensaje.textContent = "El examen está disponible. Puedes empezar cuando quieras.";

        boton.addEventListener("click", function () {
            window.guardarExamenSeleccionado(examen.id);
        });

        return;
    }

    boton.removeAttribute("href");
    boton.classList.add("boton-realizar-examen--desactivado");

    if (examen.estadoFiltro === "cerrado") {
        boton.textContent = "Examen cerrado";
        mensaje.textContent = "Este examen ya no está disponible para realizarse.";
    }

    if (examen.estadoFiltro === "proximo") {
        boton.textContent = "No disponible";
        mensaje.textContent = "Este examen todavía no está disponible.";
    }
}