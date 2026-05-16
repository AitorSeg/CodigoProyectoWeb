/*
    Header y datos comunes de DOA.
*/

document.addEventListener("DOMContentLoaded", function () {
    controlarAccesoPorRol();
    cargarUsuarioHeader();
    cargarCabeceraAsignaturaComun();
});

function controlarAccesoPorRol() {
    const usuario = obtenerUsuarioDemoLayout();

    if (usuario === null) {
        return;
    }

    const rol = normalizarRolLayout(usuario.rol);
    const paginaActual = obtenerNombrePaginaActual();

    const paginasSecretaria = [
        "panel_secretaria.html",
        "asignaturas_secretaria.html",
        "crear_asignatura.html",
        "asignaciones_secretaria.html"
    ];

    const paginasProfesor = [
        "panel_profesor.html",
        "asignaturas_profesor.html",
        "listado_tareas_profe.html",
        "detalle_tarea_profe.html",
        "detalle_tarea_entregada.html",
        "crear_tarea.html",
        "crearexamen.html",
        "examenes_profesor.html",
        "recursosdoa.html"
    ];

    const paginasAlumno = [
        "panel_principal.html",
        "asignaturas.html",
        "detalle_asignatura.html",
        "listado_tareas.html",
        "detalle_tarea.html",
        "calificaciones.html",
        "notificaciones.html",
        "examenes.html",
        "detalle_examen.html",
        "realizar_examen.html",
        "Recursosdoaalumno.html"
    ];

    if (rol === "profesor" && paginasSecretaria.includes(paginaActual)) {
        window.location.href = "panel_profesor.html";
        return;
    }

    if (rol === "secretaria" && (paginasProfesor.includes(paginaActual) || paginasAlumno.includes(paginaActual))) {
        window.location.href = "panel_secretaria.html";
        return;
    }

    if (rol === "alumno" && (paginasProfesor.includes(paginaActual) || paginasSecretaria.includes(paginaActual) || paginaActual === "enviarnotificaciones.html")) {
        window.location.href = "panel_principal.html";
        return;
    }
}

function obtenerUsuarioDemoLayout() {
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

function normalizarRolLayout(rol) {
    const rolNormalizado = String(rol || "")
        .trim()
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");

    if (rolNormalizado === "secretaria" || rolNormalizado === "pas") {
        return "secretaria";
    }

    if (rolNormalizado === "profesor") {
        return "profesor";
    }

    if (rolNormalizado === "alumno") {
        return "alumno";
    }

    return "";
}

function obtenerNombrePaginaActual() {
    return window.location.pathname.split("/").pop();
}

function cargarUsuarioHeader() {
    const nombreUsuarioHeader = document.getElementById("nombreUsuarioHeader");
    const rolUsuarioHeader = document.getElementById("rolUsuarioHeader");

    const usuarioGuardado = sessionStorage.getItem("usuarioDemoDOA");

    if (nombreUsuarioHeader === null || rolUsuarioHeader === null) {
        return;
    }

    if (usuarioGuardado === null) {
        return;
    }

    const usuario = JSON.parse(usuarioGuardado);

    nombreUsuarioHeader.textContent = usuario.nombre;
    rolUsuarioHeader.textContent = usuario.rol;
}

function cargarCabeceraAsignaturaComun() {
    if (!window.DOA_ASIGNATURAS || !window.obtenerAsignaturaSeleccionada) {
        return;
    }

    const parametrosURL = new URLSearchParams(window.location.search);
    const asignaturaURL = parametrosURL.get("materia") || parametrosURL.get("asignatura");

    if (asignaturaURL && window.DOA_ASIGNATURAS[asignaturaURL] && window.guardarAsignaturaSeleccionada) {
        window.guardarAsignaturaSeleccionada(asignaturaURL);
    }

    const idAsignatura = asignaturaURL && window.DOA_ASIGNATURAS[asignaturaURL]
        ? asignaturaURL
        : window.obtenerAsignaturaSeleccionada();

    const asignatura = window.DOA_ASIGNATURAS[idAsignatura];

    if (!asignatura) {
        return;
    }

    ponerTextoSiExiste("tituloAsignatura", asignatura.nombre);
    ponerTextoSiExiste("tituloCalificaciones", asignatura.nombre);
    ponerTextoSiExiste("profesorAsignatura", asignatura.profesor);
    ponerTextoSiExiste("unidadActualTextoAsignatura", asignatura.unidadActualTexto);
    ponerTextoSiExiste("unidadActualAsignatura", asignatura.unidadActualTexto);
}

function ponerTextoSiExiste(idElemento, texto) {
    const elemento = document.getElementById(idElemento);

    if (elemento !== null) {
        elemento.textContent = texto;
    }
}
