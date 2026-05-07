/*
    Pantalla: Panel principal del profesor
*/

const DATOS_PANEL_PROFESOR = {
    matematicas: {
        estudiantes: 28,
        tareasActivas: 2,
        entregasPendientes: 11,
        recursosPublicados: 6,
        pendiente: "Revisar entregas de límites"
    },

    programacion: {
        estudiantes: 32,
        tareasActivas: 3,
        entregasPendientes: 14,
        recursosPublicados: 8,
        pendiente: "Preparar tarea de recursividad"
    },

    fisica: {
        estudiantes: 24,
        tareasActivas: 1,
        entregasPendientes: 7,
        recursosPublicados: 5,
        pendiente: "Publicar recurso de movimiento"
    }
};

const ASIGNATURAS_POR_PROFESOR = {
    "Kevan Pounds Mainston": ["programacion"],
    "Luelle Pridmore Starsmeare": ["matematicas"],
    "Eolande Merriton Mizzi": ["fisica"]
};

let asignaturasProfesor = [];
let asignaturaActivaProfesor = "programacion";

document.addEventListener("DOMContentLoaded", function () {
    asignaturasProfesor = obtenerAsignaturasDelProfesor();
    asignaturaActivaProfesor = obtenerAsignaturaInicial();

    cargarSaludoProfesor();
    renderizarResumenProfesor();
    renderizarAsignaturasProfesor();
    renderizarPanelAsignaturaActiva();
    renderizarPendientesProfesor();
    prepararAccionesRapidasProfesor();
});

function obtenerUsuarioDemo() {
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

function obtenerAsignaturasDelProfesor() {
    const usuario = obtenerUsuarioDemo();

    if (usuario !== null && ASIGNATURAS_POR_PROFESOR[usuario.nombre]) {
        return ASIGNATURAS_POR_PROFESOR[usuario.nombre];
    }

    return ["programacion"];
}

function obtenerAsignaturaInicial() {
    const asignaturaGuardada = window.obtenerAsignaturaSeleccionada
        ? window.obtenerAsignaturaSeleccionada()
        : localStorage.getItem("doaAsignaturaSeleccionada");

    if (asignaturasProfesor.includes(asignaturaGuardada)) {
        return asignaturaGuardada;
    }

    return asignaturasProfesor[0];
}

function cargarSaludoProfesor() {
    const usuario = obtenerUsuarioDemo();
    const nombre = usuario !== null ? usuario.nombre.split(" ")[0] : "profesor";

    ponerTexto("saludoProfesor", "Buenos días, " + nombre);
}

function renderizarResumenProfesor() {
    let totalTareas = 0;
    let totalEntregas = 0;
    let totalRecursos = 0;

    asignaturasProfesor.forEach(function (idAsignatura) {
        const datos = DATOS_PANEL_PROFESOR[idAsignatura];

        if (datos) {
            totalTareas += datos.tareasActivas;
            totalEntregas += datos.entregasPendientes;
            totalRecursos += datos.recursosPublicados;
        }
    });

    ponerTexto("totalAsignaturasProfesor", asignaturasProfesor.length);
    ponerTexto("totalTareasActivasProfesor", totalTareas);
    ponerTexto("totalEntregasPendientesProfesor", totalEntregas);
    ponerTexto("totalRecursosProfesor", totalRecursos);
}

function renderizarAsignaturasProfesor() {
    const contenedor = document.getElementById("listaAsignaturasProfesor");

    if (contenedor === null) {
        return;
    }

    contenedor.innerHTML = "";

    asignaturasProfesor.forEach(function (idAsignatura) {
        const asignatura = window.DOA_ASIGNATURAS[idAsignatura];
        const datosPanel = DATOS_PANEL_PROFESOR[idAsignatura];

        if (!asignatura || !datosPanel) {
            return;
        }

        const tarjeta = document.createElement("article");

        tarjeta.className = "tarjeta-asignatura-profesor";

        if (idAsignatura === asignaturaActivaProfesor) {
            tarjeta.classList.add("tarjeta-asignatura-profesor--activa");
        }

        tarjeta.innerHTML =
            '<div class="tarjeta-asignatura-profesor__cabecera">' +
                '<div>' +
                    '<h3>' + asignatura.nombre + '</h3>' +
                    '<p>' + asignatura.unidadActualTexto + '</p>' +
                '</div>' +
                '<span class="etiqueta-asignatura-profesor">' +
                    (idAsignatura === asignaturaActivaProfesor ? "Activa" : "Asignatura") +
                '</span>' +
            '</div>' +

            '<ul class="datos-asignatura-profesor">' +
                '<li>' +
                    '<span>Alumnos</span>' +
                    '<strong>' + datosPanel.estudiantes + '</strong>' +
                '</li>' +
                '<li>' +
                    '<span>Tareas</span>' +
                    '<strong>' + datosPanel.tareasActivas + '</strong>' +
                '</li>' +
                '<li>' +
                    '<span>Entregas</span>' +
                    '<strong>' + datosPanel.entregasPendientes + '</strong>' +
                '</li>' +
            '</ul>' +

            '<div class="acciones-asignatura-profesor">' +
                '<a href="recursosdoa.html" class="boton-asignatura-profesor" data-accion="recursos" data-asignatura="' + idAsignatura + '">' +
                    'Recursos' +
                '</a>' +
                '<a href="crear_tarea.html" class="boton-asignatura-profesor" data-accion="tarea" data-asignatura="' + idAsignatura + '">' +
                    'Crear tarea' +
                '</a>' +
            '</div>';

        tarjeta.addEventListener("click", function () {
            seleccionarAsignaturaProfesor(idAsignatura);
        });

        const enlaces = tarjeta.querySelectorAll("a");

        enlaces.forEach(function (enlace) {
            enlace.addEventListener("click", function (evento) {
                evento.stopPropagation();
                guardarAsignaturaProfesor(idAsignatura);
            });
        });

        contenedor.appendChild(tarjeta);
    });
}

function seleccionarAsignaturaProfesor(idAsignatura) {
    asignaturaActivaProfesor = idAsignatura;

    guardarAsignaturaProfesor(idAsignatura);
    renderizarAsignaturasProfesor();
    renderizarPanelAsignaturaActiva();
}

function renderizarPanelAsignaturaActiva() {
    const asignatura = window.DOA_ASIGNATURAS[asignaturaActivaProfesor];

    if (!asignatura) {
        return;
    }

    ponerTexto("asignaturaActivaProfesor", asignatura.nombre);
    ponerTexto("unidadActivaProfesor", asignatura.unidadActualTexto);

    prepararEnlaceConAsignatura("botonRecursosAsignaturaActiva", asignaturaActivaProfesor);
    prepararEnlaceConAsignatura("botonTareaAsignaturaActiva", asignaturaActivaProfesor);
}

function renderizarPendientesProfesor() {
    const contenedor = document.getElementById("listaPendientesProfesor");

    if (contenedor === null) {
        return;
    }

    contenedor.innerHTML = "";

    asignaturasProfesor.forEach(function (idAsignatura) {
        const asignatura = window.DOA_ASIGNATURAS[idAsignatura];
        const datosPanel = DATOS_PANEL_PROFESOR[idAsignatura];

        if (!asignatura || !datosPanel) {
            return;
        }

        const pendiente = document.createElement("div");

        pendiente.className = "pendiente-profesor";
        pendiente.innerHTML =
            '<strong>' + datosPanel.pendiente + '</strong>' +
            '<span>' + asignatura.nombre + ' · ' + datosPanel.entregasPendientes + ' entregas pendientes</span>';

        contenedor.appendChild(pendiente);
    });
}

function prepararAccionesRapidasProfesor() {
    prepararEnlaceConAsignatura("accionCrearTarea", asignaturaActivaProfesor);
    prepararEnlaceConAsignatura("accionSubirRecurso", asignaturaActivaProfesor);
}

function prepararEnlaceConAsignatura(idEnlace, idAsignatura) {
    const enlace = document.getElementById(idEnlace);

    if (enlace === null) {
        return;
    }

    enlace.addEventListener("click", function () {
        guardarAsignaturaProfesor(idAsignatura);
    });
}

function guardarAsignaturaProfesor(idAsignatura) {
    if (typeof window.guardarAsignaturaSeleccionada === "function") {
        window.guardarAsignaturaSeleccionada(idAsignatura);
        return;
    }

    localStorage.setItem("doaAsignaturaSeleccionada", idAsignatura);
}

function ponerTexto(idElemento, texto) {
    const elemento = document.getElementById(idElemento);

    if (elemento !== null) {
        elemento.textContent = texto;
    }
}