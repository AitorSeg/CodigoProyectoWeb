/*
    Pantalla: Mis asignaturas
    Controla el acordeón superior y guarda la asignatura seleccionada.
*/

const botones_resumen = document.querySelectorAll(".dato-asignaturas__boton");
const enlaces_detalle_asignatura = document.querySelectorAll(".enlace-detalle-asignatura");
const botones_entrar_detalle = document.querySelectorAll(".boton-asignatura--principal");
const enlaces_recurso_asignatura = document.querySelectorAll(".enlace-recurso-asignatura");

botones_resumen.forEach(function (boton) {
    boton.addEventListener("click", function () {
        cambiar_estado_tarjeta_resumen(boton);
    });
});

enlaces_detalle_asignatura.forEach(function (enlace) {
    enlace.addEventListener("click", function () {
        window.guardarAsignaturaSeleccionada(enlace.dataset.asignatura);
    });
});

botones_entrar_detalle.forEach(function (boton) {
    boton.addEventListener("click", function () {
        const tarjeta = boton.closest(".tarjeta-asignatura");

        window.guardarAsignaturaSeleccionada(tarjeta.dataset.asignatura);
    });
});

enlaces_recurso_asignatura.forEach(function (enlace) {
    enlace.addEventListener("click", function () {
        const tarjeta = enlace.closest(".tarjeta-asignatura");

        window.guardarAsignaturaSeleccionada(tarjeta.dataset.asignatura);
    });
});

function cambiar_estado_tarjeta_resumen(boton) {
    const tarjeta = boton.closest(".dato-asignaturas");
    const id_detalle = boton.getAttribute("aria-controls");
    const detalle = document.getElementById(id_detalle);
    const esta_abierta = boton.getAttribute("aria-expanded") === "true";

    boton.setAttribute("aria-expanded", String(!esta_abierta));
    tarjeta.classList.toggle("dato-asignaturas--abierta", !esta_abierta);
    detalle.hidden = esta_abierta;
}