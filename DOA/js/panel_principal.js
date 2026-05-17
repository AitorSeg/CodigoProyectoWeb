/*
    Pantalla: Panel principal del alumno
*/

const tarjetas_resumen = document.querySelectorAll(".tarjeta-asignatura-resumen");
const tarjetas_progreso = document.querySelectorAll(".tarjeta-progreso-asignatura");
const lista_progresos = document.getElementById("listaProgresosAsignaturas");
const botones_entrar_asignatura = document.querySelectorAll(".tarjeta-progreso-asignatura .boton-entrar-asignatura");

tarjetas_resumen.forEach(function (tarjeta) {
    tarjeta.addEventListener("click", function () {
        seleccionar_asignatura(tarjeta.dataset.asignatura);
    });
});

botones_entrar_asignatura.forEach(function (boton) {
    boton.addEventListener("click", function () {
        const tarjeta = boton.closest(".tarjeta-progreso-asignatura");
        const asignatura = tarjeta.dataset.asignatura;

        if (asignatura !== "") {
            window.guardarAsignaturaSeleccionada(asignatura);
        }
    });
});

seleccionar_asignatura(window.obtenerAsignaturaSeleccionada() || "matematicas");

function seleccionar_asignatura(asignatura_seleccionada) {
    window.guardarAsignaturaSeleccionada(asignatura_seleccionada);
    actualizar_tarjetas_resumen(asignatura_seleccionada);
    actualizar_tarjetas_progreso(asignatura_seleccionada);
}

function actualizar_tarjetas_resumen(asignatura_seleccionada) {
    tarjetas_resumen.forEach(function (tarjeta) {
        const es_activa = tarjeta.dataset.asignatura === asignatura_seleccionada;

        tarjeta.classList.toggle("tarjeta-asignatura-resumen--activa", es_activa);
    });
}

function actualizar_tarjetas_progreso(asignatura_seleccionada) {
    tarjetas_progreso.forEach(function (tarjeta) {
        const es_activa = tarjeta.dataset.asignatura === asignatura_seleccionada;

        tarjeta.classList.toggle("tarjeta-progreso-asignatura--secundaria", !es_activa);

        if (es_activa) {
            cambiar_iconos_progreso(tarjeta, "blue");
            lista_progresos.prepend(tarjeta);
        } else {
            cambiar_iconos_progreso(tarjeta, "grey");
        }
    });
}

function cambiar_iconos_progreso(tarjeta, color) {
    const checks = tarjeta.querySelectorAll(".progreso-asignatura__unidad--completada img");
    const plays = tarjeta.querySelectorAll(".progreso-asignatura__unidad--actual img");

    checks.forEach(function (icono) {
        icono.src = "img/iconos/" + color + "-check.svg";
    });

    plays.forEach(function (icono) {
        icono.src = "img/iconos/" + color + "-play.svg";
    });
}