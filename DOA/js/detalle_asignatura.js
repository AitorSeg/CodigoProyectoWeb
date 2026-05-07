/*
    Pantalla: Detalle de asignatura
    Carga la asignatura seleccionada desde localStorage.
*/

document.addEventListener("DOMContentLoaded", function () {
    const idAsignatura = window.obtenerAsignaturaSeleccionada();
    const datosAsignatura = window.DOA_ASIGNATURAS[idAsignatura] || window.DOA_ASIGNATURAS.programacion;

    renderizarDetalleAsignatura(datosAsignatura);
});

function renderizarDetalleAsignatura(datos) {
    const tituloAsignatura = document.getElementById("tituloAsignatura");
    const profesorAsignatura = document.getElementById("profesorAsignatura");
    const unidadActualTextoAsignatura = document.getElementById("unidadActualTextoAsignatura");
    const tituloUnidadActual = document.getElementById("tituloUnidadActual");
    const descripcionUnidadActual = document.getElementById("descripcionUnidadActual");
    const rutaProgresoAsignatura = document.getElementById("rutaProgresoAsignatura");

    const tituloEvaluacionAsignatura = document.getElementById("tituloEvaluacionAsignatura");
    const fechaEvaluacionAsignatura = document.getElementById("fechaEvaluacionAsignatura");
    const horaEvaluacionAsignatura = document.getElementById("horaEvaluacionAsignatura");
    const lugarEvaluacionAsignatura = document.getElementById("lugarEvaluacionAsignatura");

    const tituloTareaAsignatura = document.getElementById("tituloTareaAsignatura");
    const vencimientoTareaAsignatura = document.getElementById("vencimientoTareaAsignatura");

    document.title = datos.nombre + " | DOA";

    tituloAsignatura.textContent = datos.nombre;
    profesorAsignatura.textContent = datos.profesor;
    unidadActualTextoAsignatura.textContent = datos.unidadActualTexto;
    tituloUnidadActual.textContent = datos.unidadActualTitulo;
    descripcionUnidadActual.textContent = datos.descripcion;

    rutaProgresoAsignatura.style.setProperty("--progreso-escritorio", datos.progresoEscritorio);
    rutaProgresoAsignatura.style.setProperty("--progreso-movil", datos.progresoMovil);
    rutaProgresoAsignatura.setAttribute("aria-label", "Ruta de progreso de " + datos.nombre);

    tituloEvaluacionAsignatura.textContent = datos.evaluacion.titulo;
    fechaEvaluacionAsignatura.textContent = datos.evaluacion.fecha;
    horaEvaluacionAsignatura.textContent = datos.evaluacion.hora;
    lugarEvaluacionAsignatura.textContent = datos.evaluacion.lugar;

    tituloTareaAsignatura.textContent = datos.tarea.titulo;
    vencimientoTareaAsignatura.textContent = datos.tarea.vencimiento;
}