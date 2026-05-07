/*
    Pantalla: Mis asignaturas
    Desplegar tarjetas de resumen.
*/

document.addEventListener("DOMContentLoaded", function () {
    const botonesResumen = document.querySelectorAll(".dato-asignaturas__boton");

    botonesResumen.forEach(function (boton) {
        boton.addEventListener("click", function () {
            cambiarEstadoTarjetaResumen(boton);
        });
    });

    function cambiarEstadoTarjetaResumen(boton) {
        const tarjeta = boton.closest(".dato-asignaturas");
        const idDetalle = boton.getAttribute("aria-controls");
        const detalle = document.getElementById(idDetalle);
        const estaAbierta = boton.getAttribute("aria-expanded") === "true";

        if (tarjeta === null || detalle === null) {
            return;
        }

        boton.setAttribute("aria-expanded", String(!estaAbierta));
        tarjeta.classList.toggle("dato-asignaturas--abierta", !estaAbierta);
        detalle.hidden = estaAbierta;
    }
});