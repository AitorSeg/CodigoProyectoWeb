/*
    Pantalla: Gestión de exámenes del profesor
*/

let examenesProfesorActuales = [];
let filtroProfesorActivo = "todos";

const revisionesPendientesMock = {
    matematicas: 7,
    programacion: 4,
    fisica: 5
};

document.addEventListener("DOMContentLoaded", function () {
    const idAsignatura = obtenerAsignaturaProfesorActual();
    const asignatura = window.DOA_ASIGNATURAS[idAsignatura] || window.DOA_ASIGNATURAS.matematicas;

    examenesProfesorActuales = window.obtenerExamenesAsignatura(idAsignatura);

    cargarCabeceraExamenesProfesor(asignatura);
    cargarResumenExamenesProfesor(idAsignatura, examenesProfesorActuales);
    cargarExamenDestacadoProfesor(examenesProfesorActuales);
    prepararFiltrosExamenesProfesor();
    renderizarExamenesProfesor();
});

function obtenerAsignaturaProfesorActual() {
    const parametrosURL = new URLSearchParams(window.location.search);
    const asignaturaURL = parametrosURL.get("asignatura") || parametrosURL.get("materia");

    if (asignaturaURL && window.DOA_ASIGNATURAS[asignaturaURL]) {
        if (typeof window.guardarAsignaturaSeleccionada === "function") {
            window.guardarAsignaturaSeleccionada(asignaturaURL);
        }

        return asignaturaURL;
    }

    if (typeof window.obtenerAsignaturaSeleccionada === "function") {
        return window.obtenerAsignaturaSeleccionada();
    }

    return "matematicas";
}

function ponerTextoExamenProfesor(idElemento, texto) {
    const elemento = document.getElementById(idElemento);

    if (elemento !== null) {
        elemento.textContent = texto;
    }
}

function cargarCabeceraExamenesProfesor(asignatura) {
    document.title = "Gestión de exámenes · " + asignatura.nombre + " | DOA";

    ponerTextoExamenProfesor("tituloAsignatura", asignatura.nombre);
    ponerTextoExamenProfesor("profesorAsignatura", asignatura.profesor);
    ponerTextoExamenProfesor("unidadActualTextoAsignatura", asignatura.unidadActualTexto);
}

function cargarResumenExamenesProfesor(idAsignatura, examenes) {
    const abiertos = examenes.filter(function (examen) {
        return examen.estadoFiltro === "abierto";
    }).length;

    ponerTextoExamenProfesor("totalExamenesProfesor", examenes.length);
    ponerTextoExamenProfesor("totalExamenesAbiertosProfesor", abiertos);
    ponerTextoExamenProfesor(
        "totalRevisionesPendientesProfesor",
        revisionesPendientesMock[idAsignatura] || 0
    );
}

function cargarExamenDestacadoProfesor(examenes) {
    const examenAbierto = examenes.find(function (examen) {
        return examen.estadoFiltro === "abierto";
    });

    const examenDestacado = examenAbierto || examenes[0];

    if (!examenDestacado) {
        return;
    }

    const etiqueta = document.getElementById("estadoExamenDestacadoProfesor");
    const botonEditar = document.getElementById("botonEditarExamenDestacadoProfesor");
    const botonResultados = document.getElementById("botonResultadosExamenDestacadoProfesor");

    ponerTextoExamenProfesor("tituloExamenDestacadoProfesor", examenDestacado.nombre);
    ponerTextoExamenProfesor("descripcionExamenDestacadoProfesor", examenDestacado.descripcion);
    ponerTextoExamenProfesor("fechaLimiteExamenDestacadoProfesor", examenDestacado.fechaCompleta);

    if (etiqueta !== null) {
        etiqueta.className = "etiqueta-examen etiqueta-examen--" + examenDestacado.estadoFiltro;
        etiqueta.textContent = examenDestacado.estado;
    }

    if (botonEditar !== null) {
        botonEditar.addEventListener("click", function () {
            guardarExamenProfesorSeleccionado(examenDestacado.id);
        });
    }

    if (botonResultados !== null) {
        botonResultados.addEventListener("click", function () {
            guardarExamenProfesorSeleccionado(examenDestacado.id);
            mostrarAvisoResultadosExamenProfesor(examenDestacado.nombre);
        });
    }
}

function prepararFiltrosExamenesProfesor() {
    const filtros = document.querySelectorAll(".filtro-examen");

    filtros.forEach(function (filtro) {
        filtro.addEventListener("click", function () {
            filtroProfesorActivo = filtro.dataset.filtro || "todos";

            filtros.forEach(function (boton) {
                boton.classList.toggle("filtro-examen--activo", boton === filtro);
            });

            renderizarExamenesProfesor();
        });
    });
}

function renderizarExamenesProfesor() {
    const contenedor = document.getElementById("listadoExamenesProfesor");

    if (contenedor === null) {
        return;
    }

    const examenesFiltrados = examenesProfesorActuales.filter(function (examen) {
        return filtroProfesorActivo === "todos" || examen.estadoFiltro === filtroProfesorActivo;
    });

    contenedor.innerHTML = "";

    if (examenesFiltrados.length === 0) {
        const mensaje = document.createElement("p");
        mensaje.className = "mensaje-sin-examenes";
        mensaje.textContent = "No hay exámenes con este filtro.";
        contenedor.appendChild(mensaje);
        return;
    }

    examenesFiltrados.forEach(function (examen) {
        const fila = document.createElement("article");
        fila.className = "fila-examen fila-examen-profesor";

        fila.innerHTML =
            '<div class="fila-examen__nombre">' +
                '<strong>' + examen.nombre + '</strong>' +
                '<span>' + examen.descripcionCorta + '</span>' +
            '</div>' +
            '<p class="fila-examen__fecha" data-duracion="' + examen.duracion + '">' + examen.fechaCompleta + '</p>' +
            '<p class="fila-examen__duracion">' + examen.duracion + '</p>' +
            '<p class="fila-examen__estado">' +
                '<span class="etiqueta-examen etiqueta-examen--' + examen.estadoFiltro + '">' + examen.estado + '</span>' +
            '</p>' +
            '<div class="acciones-fila-examen-profesor">' +
                '<a href="crearexamen.html" class="fila-examen__accion fila-examen__accion--principal" data-accion="editar" data-examen="' + examen.id + '">Editar</a>' +
                '<button type="button" class="fila-examen__accion" data-accion="resultados" data-examen="' + examen.id + '">Resultados</button>' +
            '</div>';

        const acciones = fila.querySelectorAll("[data-examen]");

        acciones.forEach(function (accion) {
            accion.addEventListener("click", function () {
                guardarExamenProfesorSeleccionado(examen.id);

                if (accion.dataset.accion === "resultados") {
                    mostrarAvisoResultadosExamenProfesor(examen.nombre);
                }
            });
        });

        contenedor.appendChild(fila);
    });
}

function guardarExamenProfesorSeleccionado(idExamen) {
    if (typeof window.guardarExamenSeleccionado === "function") {
        window.guardarExamenSeleccionado(idExamen);
    }
}

function mostrarAvisoResultadosExamenProfesor(nombreExamen) {
    alert("Vista de resultados simulada para: " + nombreExamen + ". En el PMV no hay base de datos de entregas de exámenes.");
}
