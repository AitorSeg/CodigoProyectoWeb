/*
    Header y datos comunes de DOA.
*/

document.addEventListener("DOMContentLoaded", function () {
    cargarUsuarioHeader();
    cargarCabeceraAsignaturaComun();
});

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
