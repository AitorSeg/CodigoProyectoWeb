const botones_resumen = document.querySelectorAll(".dato-asignaturas__boton");

botones_resumen.forEach(function (boton) {
    boton.addEventListener("click", function () {
        cambiar_estado_tarjeta_resumen(boton);
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