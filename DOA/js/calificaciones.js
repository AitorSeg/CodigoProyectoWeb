/*
    Pantalla: Calificaciones
*/

let historialCalificacionesActual = [];

document.addEventListener("DOMContentLoaded", function () {
    const idAsignatura = window.obtenerAsignaturaSeleccionada();
    const asignatura = window.DOA_ASIGNATURAS[idAsignatura] || window.DOA_ASIGNATURAS.matematicas;
    const calificaciones = obtenerCalificacionesAsignatura(idAsignatura);

    historialCalificacionesActual = calificaciones.historial;

    cargarCabeceraAsignatura(asignatura);
    cargarResumenCalificaciones(calificaciones);
    prepararFiltrosCalificaciones();
    aplicarFiltrosCalificaciones();
});

function ponerTexto(idElemento, texto) {
    const elemento = document.getElementById(idElemento);

    if (elemento !== null) {
        elemento.textContent = texto;
    }
}

function cargarCabeceraAsignatura(asignatura) {
    document.title = "Calificaciones · " + asignatura.nombre + " | DOA";

    ponerTexto("tituloCalificaciones", asignatura.nombre);
    ponerTexto("profesorAsignatura", asignatura.profesor);
    ponerTexto("unidadActualAsignatura", asignatura.unidadActualTexto);
}

function cargarResumenCalificaciones(calificaciones) {
    ponerTexto("notaMedia", calificaciones.notaMedia);
    ponerTexto("notaMediaExamenes", calificaciones.notaMediaExamenes);
    ponerTexto("notaMediaTareas", calificaciones.notaMediaTareas);
    ponerTexto("notaMediaPracticas", calificaciones.notaMediaPracticas);
}

function prepararFiltrosCalificaciones() {
    const filtroTipo = document.getElementById("filtroTipoCalificacion");
    const filtroEstado = document.getElementById("filtroEstadoCalificacion");
    const orden = document.getElementById("ordenCalificacion");

    if (filtroTipo !== null) {
        filtroTipo.addEventListener("change", aplicarFiltrosCalificaciones);
    }

    if (filtroEstado !== null) {
        filtroEstado.addEventListener("change", aplicarFiltrosCalificaciones);
    }

    if (orden !== null) {
        orden.addEventListener("change", aplicarFiltrosCalificaciones);
    }
}

function aplicarFiltrosCalificaciones() {
    const filtroTipo = document.getElementById("filtroTipoCalificacion");
    const filtroEstado = document.getElementById("filtroEstadoCalificacion");
    const orden = document.getElementById("ordenCalificacion");

    const tipoSeleccionado = filtroTipo !== null ? filtroTipo.value : "todas";
    const estadoSeleccionado = filtroEstado !== null ? filtroEstado.value : "todos";
    const ordenSeleccionado = orden !== null ? orden.value : "fecha";

    let historialFiltrado = historialCalificacionesActual.filter(function (actividad) {
        const coincideTipo = tipoSeleccionado === "todas" || actividad.tipoFiltro === tipoSeleccionado;
        const coincideEstado = estadoSeleccionado === "todos" || actividad.estadoFiltro === estadoSeleccionado;

        return coincideTipo && coincideEstado;
    });

    historialFiltrado = ordenarCalificaciones(historialFiltrado, ordenSeleccionado);

    cargarTablaCalificaciones(historialFiltrado);
}

function ordenarCalificaciones(historial, ordenSeleccionado) {
    const copiaHistorial = historial.slice();

    if (ordenSeleccionado === "nombre") {
        copiaHistorial.sort(function (a, b) {
            return a.nombre.localeCompare(b.nombre);
        });
    }

    if (ordenSeleccionado === "nota") {
        copiaHistorial.sort(function (a, b) {
            const notaA = a.notaNumero === null ? -1 : a.notaNumero;
            const notaB = b.notaNumero === null ? -1 : b.notaNumero;

            return notaB - notaA;
        });
    }

    if (ordenSeleccionado === "fecha") {
        copiaHistorial.sort(function (a, b) {
            return b.ordenFecha - a.ordenFecha;
        });
    }

    return copiaHistorial;
}

function cargarTablaCalificaciones(historial) {
    const tabla = document.getElementById("tablaCalificaciones");

    if (tabla === null) {
        return;
    }

    tabla.innerHTML = "";

    if (historial.length === 0) {
        const filaVacia = document.createElement("tr");

        filaVacia.className = "fila-sin-resultados";
        filaVacia.innerHTML = '<td colspan="8">No hay calificaciones con estos filtros.</td>';

        tabla.appendChild(filaVacia);
        return;
    }

    historial.forEach(function (actividad) {
        const fila = document.createElement("tr");

        const claseNota = actividad.notaNumero !== null && actividad.notaNumero < 5 ? "nota-negativa" : "nota-positiva";

        const nota = actividad.nota === null
            ? '<span class="barra-nota-pendiente"></span>'
            : '<span class="' + claseNota + '">' + actividad.nota + '</span>';

        const estadoClase = actividad.estadoFiltro === "proxima"
            ? "estado-calificacion estado-calificacion--pendiente"
            : "estado-calificacion";

        fila.innerHTML =
            '<td>' + actividad.nombre + '</td>' +
            '<td>' + actividad.tipo + '</td>' +
            '<td>' + actividad.unidad + '</td>' +
            '<td>' + actividad.peso + '</td>' +
            '<td>' + nota + '</td>' +
            '<td><span class="' + estadoClase + '">' + actividad.estado + '</span></td>' +
            '<td>' + actividad.fecha + '</td>' +
            '<td><a href="#" class="enlace-tabla">' + actividad.accion + '</a></td>';

        tabla.appendChild(fila);
    });
}

function obtenerCalificacionesAsignatura(idAsignatura) {
    const datos = {
        matematicas: {
            notaMedia: "5,9",
            notaMediaExamenes: "3,7",
            notaMediaTareas: "8,3",
            notaMediaPracticas: "5,7",
            historial: [
                {
                    nombre: "Parcial 01",
                    tipo: "Examen",
                    tipoFiltro: "examen",
                    unidad: "Unidad 1",
                    peso: "20%",
                    nota: "4.65",
                    notaNumero: 4.65,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "Ayer",
                    ordenFecha: 4,
                    accion: "Ver detalles"
                },
                {
                    nombre: "Tarea de derivadas",
                    tipo: "Tarea",
                    tipoFiltro: "tarea",
                    unidad: "Unidad 1",
                    peso: "20%",
                    nota: "8.3",
                    notaNumero: 8.3,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "26 de oct",
                    ordenFecha: 3,
                    accion: "Ver detalles"
                },
                {
                    nombre: "Práctica de funciones",
                    tipo: "Práctica",
                    tipoFiltro: "practica",
                    unidad: "Unidad 2",
                    peso: "20%",
                    nota: "5.7",
                    notaNumero: 5.7,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "20 de oct",
                    ordenFecha: 2,
                    accion: "Ver detalles"
                },
                {
                    nombre: "Parcial 02",
                    tipo: "Examen",
                    tipoFiltro: "examen",
                    unidad: "Unidad 3",
                    peso: "20%",
                    nota: null,
                    notaNumero: null,
                    estado: "Próxima",
                    estadoFiltro: "proxima",
                    fecha: "15 de nov",
                    ordenFecha: 5,
                    accion: "Ver temario"
                }
            ]
        },

        programacion: {
            notaMedia: "7,4",
            notaMediaExamenes: "6,8",
            notaMediaTareas: "8,6",
            notaMediaPracticas: "8,1",
            historial: [
                {
                    nombre: "Práctica arrays",
                    tipo: "Práctica",
                    tipoFiltro: "practica",
                    unidad: "Unidad 1",
                    peso: "10%",
                    nota: "8.2",
                    notaNumero: 8.2,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "10 de oct",
                    ordenFecha: 1,
                    accion: "Ver detalles"
                },
                {
                    nombre: "Ejercicio recursividad",
                    tipo: "Tarea",
                    tipoFiltro: "tarea",
                    unidad: "Unidad 3",
                    peso: "15%",
                    nota: "8.7",
                    notaNumero: 8.7,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "20 de oct",
                    ordenFecha: 3,
                    accion: "Ver feedback"
                },
                {
                    nombre: "Examen parcial 1",
                    tipo: "Examen",
                    tipoFiltro: "examen",
                    unidad: "Unidad 1 y 2",
                    peso: "25%",
                    nota: "7.1",
                    notaNumero: 7.1,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "02 de nov",
                    ordenFecha: 4,
                    accion: "Ver detalles"
                },
                {
                    nombre: "Práctica grafos",
                    tipo: "Práctica",
                    tipoFiltro: "practica",
                    unidad: "Unidad 4",
                    peso: "15%",
                    nota: null,
                    notaNumero: null,
                    estado: "Próxima",
                    estadoFiltro: "proxima",
                    fecha: "18 de nov",
                    ordenFecha: 5,
                    accion: "Ver tarea"
                }
            ]
        },

        fisica: {
            notaMedia: "6,8",
            notaMediaExamenes: "6,2",
            notaMediaTareas: "7,4",
            notaMediaPracticas: "8,1",
            historial: [
                {
                    nombre: "Cuestionario fuerzas",
                    tipo: "Examen corto",
                    tipoFiltro: "examen",
                    unidad: "Unidad 1",
                    peso: "5%",
                    nota: "7.2",
                    notaNumero: 7.2,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "11 de oct",
                    ordenFecha: 1,
                    accion: "Ver detalles"
                },
                {
                    nombre: "Informe laboratorio",
                    tipo: "Práctica",
                    tipoFiltro: "practica",
                    unidad: "Unidad 2",
                    peso: "15%",
                    nota: "8.1",
                    notaNumero: 8.1,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "25 de oct",
                    ordenFecha: 2,
                    accion: "Ver feedback"
                },
                {
                    nombre: "Control cinemática",
                    tipo: "Examen",
                    tipoFiltro: "examen",
                    unidad: "Unidad 3",
                    peso: "20%",
                    nota: null,
                    notaNumero: null,
                    estado: "Próxima",
                    estadoFiltro: "proxima",
                    fecha: "19 de nov",
                    ordenFecha: 3,
                    accion: "Ver temario"
                }
            ]
        }
    };

    return datos[idAsignatura] || datos.matematicas;
}