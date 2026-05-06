/*
    Panel principal DOA
*/

document.addEventListener("DOMContentLoaded", function () {
    const tarjetasResumen = document.querySelectorAll(".tarjeta-asignatura-resumen");
    const tarjetasProgreso = document.querySelectorAll(".tarjeta-progreso-asignatura");
    const listaProgresos = document.getElementById("listaProgresosAsignaturas");

    tarjetasResumen.forEach(function (tarjeta) {
        tarjeta.addEventListener("click", function () {
            seleccionarAsignatura(tarjeta.dataset.asignatura);
        });
    });

    seleccionarAsignatura("matematicas");

    function seleccionarAsignatura(asignaturaSeleccionada) {
        actualizarTarjetasResumen(asignaturaSeleccionada);
        actualizarTarjetasProgreso(asignaturaSeleccionada);
    }

    function actualizarTarjetasResumen(asignaturaSeleccionada) {
        tarjetasResumen.forEach(function (tarjeta) {
            const esActiva = tarjeta.dataset.asignatura === asignaturaSeleccionada;

            tarjeta.classList.toggle("tarjeta-asignatura-resumen--activa", esActiva);
        });
    }

    function actualizarTarjetasProgreso(asignaturaSeleccionada) {
        tarjetasProgreso.forEach(function (tarjeta) {
            const esActiva = tarjeta.dataset.asignatura === asignaturaSeleccionada;

            tarjeta.classList.toggle("tarjeta-progreso-asignatura--secundaria", !esActiva);

            if (esActiva) {
                cambiarIconosProgreso(tarjeta, "blue");
                listaProgresos.prepend(tarjeta);
            } else {
                cambiarIconosProgreso(tarjeta, "grey");
            }
        });
    }

    function cambiarIconosProgreso(tarjeta, color) {
        const checks = tarjeta.querySelectorAll(".progreso-asignatura__unidad--completada img");
        const plays = tarjeta.querySelectorAll(".progreso-asignatura__unidad--actual img");

        checks.forEach(function (icono) {
            icono.src = "../img/iconos/" + color + "-check.svg";
        });

        plays.forEach(function (icono) {
            icono.src = "../img/iconos/" + color + "-play.svg";
        });
    }
});