/*
    Pantalla: Panel principal del alumno
*/

const tarjetas_resumen = document.querySelectorAll(".tarjeta-asignatura-resumen[data-asignatura]");
const tarjetas_progreso = Array.from(document.querySelectorAll(".tarjeta-progreso-asignatura[data-asignatura]"));
const lista_progresos = document.getElementById("listaProgresosAsignaturas");

tarjetas_resumen.forEach(function (tarjeta) {
    tarjeta.addEventListener("click", function () {
        seleccionar_asignatura(tarjeta.dataset.asignatura, true);
    });
});

if (tarjetas_resumen.length > 0) {
    seleccionar_asignatura(tarjetas_resumen[0].dataset.asignatura, false);
}

function seleccionar_asignatura(asignatura_seleccionada, animar_reorden) {
    actualizar_tarjetas_resumen(asignatura_seleccionada);

    if (animar_reorden) {
        animar_reorden_tarjetas_progreso(asignatura_seleccionada);
    } else {
        mover_tarjeta_progreso_activa(asignatura_seleccionada);
        actualizar_tarjetas_progreso(asignatura_seleccionada);
    }
}

function actualizar_tarjetas_resumen(asignatura_seleccionada) {
    tarjetas_resumen.forEach(function (tarjeta) {
        const es_activa = tarjeta.dataset.asignatura === asignatura_seleccionada;

        tarjeta.classList.toggle("tarjeta-asignatura-resumen--activa", es_activa);
    });
}

function mover_tarjeta_progreso_activa(asignatura_seleccionada) {
    const tarjeta_activa = tarjetas_progreso.find(function (tarjeta) {
        return tarjeta.dataset.asignatura === asignatura_seleccionada;
    });

    lista_progresos.prepend(tarjeta_activa);
}

function animar_reorden_tarjetas_progreso(asignatura_seleccionada) {
    const posiciones_iniciales = new Map();

    tarjetas_progreso.forEach(function (tarjeta) {
        posiciones_iniciales.set(tarjeta, tarjeta.getBoundingClientRect());
    });

    mover_tarjeta_progreso_activa(asignatura_seleccionada);
    actualizar_tarjetas_progreso(asignatura_seleccionada);

    tarjetas_progreso.forEach(function (tarjeta) {
        const posicion_inicial = posiciones_iniciales.get(tarjeta);
        const posicion_final = tarjeta.getBoundingClientRect();

        const desplazamiento_x = posicion_inicial.left - posicion_final.left;
        const desplazamiento_y = posicion_inicial.top - posicion_final.top;

        tarjeta.animate(
            [
                {
                    transform: "translate(" + desplazamiento_x + "px, " + desplazamiento_y + "px)"
                },
                {
                    transform: "translate(0, 0)"
                }
            ],
            {
                duration: 280,
                easing: "cubic-bezier(0.22, 1, 0.36, 1)"
            }
        );
    });
}

function actualizar_tarjetas_progreso(asignatura_seleccionada) {
    tarjetas_progreso.forEach(function (tarjeta) {
        const es_activa = tarjeta.dataset.asignatura === asignatura_seleccionada;

        tarjeta.classList.toggle("tarjeta-progreso-asignatura--activa", es_activa);
        tarjeta.classList.toggle("tarjeta-progreso-asignatura--secundaria", !es_activa);

        cambiar_iconos_progreso(tarjeta, es_activa ? "blue" : "grey");
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