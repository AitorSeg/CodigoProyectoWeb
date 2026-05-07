const DATOS_ASIGNACIONES_SECRETARIA = {
    programacion: {
        nombre: "Programación II",
        codigo: "GTI-203",
        profesor: "kevan",
        alumnos: ["lief", "ana", "marc"]
    },

    matematicas: {
        nombre: "Matemáticas",
        codigo: "GTI-104",
        profesor: "pepito",
        alumnos: ["nuria", "pablo"]
    },

    fisica: {
        nombre: "Física",
        codigo: "GTI-112",
        profesor: "eolande",
        alumnos: ["pedro"]
    },

    interfaces: {
        nombre: "Diseño de Interfaces",
        codigo: "GTI-221",
        profesor: "",
        alumnos: []
    }
};

const NOMBRES_PROFESORES_SECRETARIA = {
    kevan: "Kevan Pounds Mainston",
    pepito: "Don Pepito",
    eolande: "Eolande Merriton Mizzi",
    luelle: "Luelle Pridmore Starsmeare"
};

document.addEventListener("DOMContentLoaded", function () {
    const selectAsignatura = document.getElementById("selectAsignaturaSecretaria");
    const selectProfesor = document.getElementById("selectProfesorSecretaria");
    const formulario = document.getElementById("formAsignacionesSecretaria");
    const checksAlumnos = document.querySelectorAll(".item-alumno-secretaria input");

    if (selectAsignatura === null || selectProfesor === null || formulario === null) {
        return;
    }

    cargarAsignacionSeleccionada();

    selectAsignatura.addEventListener("change", function () {
        cargarAsignacionSeleccionada();
    });

    selectProfesor.addEventListener("change", function () {
        actualizarResumenAsignacion();
    });

    checksAlumnos.forEach(function (check) {
        check.addEventListener("change", function () {
            actualizarResumenAsignacion();
        });
    });

    formulario.addEventListener("submit", function (evento) {
        evento.preventDefault();
        guardarAsignacionSecretaria();
        mostrarMensajeAsignaciones();
    });
});

function cargarAsignacionSeleccionada() {
    const selectAsignatura = document.getElementById("selectAsignaturaSecretaria");
    const selectProfesor = document.getElementById("selectProfesorSecretaria");
    const checksAlumnos = document.querySelectorAll(".item-alumno-secretaria input");

    const idAsignatura = selectAsignatura.value;
    const datos = obtenerAsignacionGuardada(idAsignatura);

    selectProfesor.value = datos.profesor;

    checksAlumnos.forEach(function (check) {
        check.checked = datos.alumnos.includes(check.value);
    });

    actualizarResumenAsignacion();
}

function obtenerAsignacionGuardada(idAsignatura) {
    const guardado = localStorage.getItem("doaAsignacionesSecretaria");

    if (guardado !== null) {
        try {
            const asignaciones = JSON.parse(guardado);

            if (asignaciones[idAsignatura]) {
                return asignaciones[idAsignatura];
            }
        } catch (error) {
            return DATOS_ASIGNACIONES_SECRETARIA[idAsignatura];
        }
    }

    return DATOS_ASIGNACIONES_SECRETARIA[idAsignatura];
}

function actualizarResumenAsignacion() {
    const selectAsignatura = document.getElementById("selectAsignaturaSecretaria");
    const selectProfesor = document.getElementById("selectProfesorSecretaria");
    const checksSeleccionados = document.querySelectorAll(".item-alumno-secretaria input:checked");

    const idAsignatura = selectAsignatura.value;
    const datosBase = DATOS_ASIGNACIONES_SECRETARIA[idAsignatura];

    const profesorSeleccionado = selectProfesor.value;
    const totalAlumnos = checksSeleccionados.length;

    ponerTexto("resumenNombreAsignatura", datosBase.nombre);
    ponerTexto("resumenCodigoAsignatura", datosBase.codigo);

    if (profesorSeleccionado === "") {
        ponerTexto("resumenProfesorAsignatura", "Pendiente");
    } else {
        ponerTexto("resumenProfesorAsignatura", NOMBRES_PROFESORES_SECRETARIA[profesorSeleccionado]);
    }

    ponerTexto("resumenAlumnosAsignatura", totalAlumnos + " asignados");
    ponerTexto("contadorAlumnosSecretaria", totalAlumnos + " seleccionados");

    actualizarEstadoResumen(profesorSeleccionado, totalAlumnos);
}

function actualizarEstadoResumen(profesorSeleccionado, totalAlumnos) {
    const estado = document.getElementById("resumenEstadoAsignatura");

    if (estado === null) {
        return;
    }

    estado.classList.remove("estado-secretaria--completa", "estado-secretaria--pendiente");

    if (profesorSeleccionado !== "" && totalAlumnos > 0) {
        estado.textContent = "Completa";
        estado.classList.add("estado-secretaria--completa");
        return;
    }

    estado.textContent = "Pendiente";
    estado.classList.add("estado-secretaria--pendiente");
}

function guardarAsignacionSecretaria() {
    const selectAsignatura = document.getElementById("selectAsignaturaSecretaria");
    const selectProfesor = document.getElementById("selectProfesorSecretaria");
    const checksSeleccionados = document.querySelectorAll(".item-alumno-secretaria input:checked");

    const idAsignatura = selectAsignatura.value;
    const datosBase = DATOS_ASIGNACIONES_SECRETARIA[idAsignatura];

    const alumnos = [];

    checksSeleccionados.forEach(function (check) {
        alumnos.push(check.value);
    });

    const nuevaAsignacion = {
        nombre: datosBase.nombre,
        codigo: datosBase.codigo,
        profesor: selectProfesor.value,
        alumnos: alumnos
    };

    const guardado = localStorage.getItem("doaAsignacionesSecretaria");
    let asignaciones = {};

    if (guardado !== null) {
        try {
            asignaciones = JSON.parse(guardado);
        } catch (error) {
            asignaciones = {};
        }
    }

    asignaciones[idAsignatura] = nuevaAsignacion;

    localStorage.setItem("doaAsignacionesSecretaria", JSON.stringify(asignaciones));
}

function mostrarMensajeAsignaciones() {
    const mensaje = document.getElementById("mensajeAsignacionesSecretaria");

    if (mensaje === null) {
        return;
    }

    mensaje.classList.remove("mensaje-formulario-secretaria--oculto");

    setTimeout(function () {
        mensaje.classList.add("mensaje-formulario-secretaria--oculto");
    }, 3500);
}

function ponerTexto(idElemento, texto) {
    const elemento = document.getElementById(idElemento);

    if (elemento !== null) {
        elemento.textContent = texto;
    }
}