/*
    Pantalla: Mis asignaturas
    Desplegar tarjetas de resumen.
*/

document.addEventListener("DOMContentLoaded", function () {
    // 1. Acordeón de tarjetas de resumen superior
    const botonesResumen = document.querySelectorAll(".dato-asignaturas__boton");

    botonesResumen.forEach(function (boton) {
        boton.addEventListener("click", function () {
            cambiarEstadoTarjetaResumen(boton);
        });
    });

    // 2. Enlaces del detalle (en el acordeón superior)
    const enlacesDetalleAsignatura = document.querySelectorAll(".enlace-detalle-asignatura");

    enlacesDetalleAsignatura.forEach(function (enlace) {
        enlace.addEventListener("click", function () {
            const asignatura = enlace.dataset.asignatura;

            if (asignatura) {
                window.guardarAsignaturaSeleccionada(asignatura);
            }
        });
    });

    // 3. Botones principales de ENTRAR en las tarjetas grandes
    const botonesEntrarDetalle = document.querySelectorAll(".boton-asignatura--principal");

    botonesEntrarDetalle.forEach(function (boton) {
        boton.addEventListener("click", function () {
            const tarjeta = boton.closest(".tarjeta-asignatura");
            const asignatura = tarjeta.dataset.asignatura;

            if (asignatura) {
                window.guardarAsignaturaSeleccionada(asignatura);
            }
        });
    });

    // 4. Botones secundarios de RECURSOS en las tarjetas grandes
    const enlacesRecursoAsignatura = document.querySelectorAll(".enlace-recurso-asignatura");

    enlacesRecursoAsignatura.forEach(function (enlace) {
        enlace.addEventListener("click", function () {
            const tarjeta = enlace.closest(".tarjeta-asignatura");

            if (tarjeta !== null && tarjeta.dataset.asignatura) {
                window.guardarAsignaturaSeleccionada(tarjeta.dataset.asignatura);
            }
        });
    });

    // Función para el acordeón
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