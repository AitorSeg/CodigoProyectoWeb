/*
    Header DOA para mostrar el usuario seleccionado
*/

document.addEventListener("DOMContentLoaded", function () {
    cargarUsuarioHeader();
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