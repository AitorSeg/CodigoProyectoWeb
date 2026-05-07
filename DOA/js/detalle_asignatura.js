document.addEventListener("DOMContentLoaded", function () {
    const idAsignatura = window.obtenerAsignaturaSeleccionada() || "programacion";
    const datosAsignatura = window.DOA_ASIGNATURAS[idAsignatura] || window.DOA_ASIGNATURAS.programacion;

    renderizarDetalleAsignatura(datosAsignatura, idAsignatura);
});

function renderizarDetalleAsignatura(datos, idAsignatura) {
    document.getElementById("tituloAsignatura").textContent = datos.nombre;
    document.getElementById("profesorAsignatura").textContent = datos.profesor;

    const unidadTexto = document.getElementById("unidadActualTextoAsignatura");
    if (unidadTexto) unidadTexto.innerHTML = datos.unidadActualTexto;

    document.getElementById("tituloUnidadActual").textContent = datos.unidadActualTitulo;
    document.getElementById("descripcionUnidadActual").textContent = datos.descripcion;

    const rutaProgreso = document.getElementById("rutaProgresoAsignatura");
    if(rutaProgreso) {
        rutaProgreso.style.setProperty("--progreso-escritorio", datos.progresoEscritorio);
        rutaProgreso.style.setProperty("--progreso-movil", datos.progresoMovil);
    }

    document.getElementById("tituloEvaluacionAsignatura").textContent = datos.evaluacion.titulo;
    document.getElementById("fechaEvaluacionAsignatura").textContent = datos.evaluacion.fecha;
    document.getElementById("horaEvaluacionAsignatura").textContent = datos.evaluacion.hora;
    document.getElementById("lugarEvaluacionAsignatura").textContent = datos.evaluacion.lugar;
    document.getElementById("tituloTareaAsignatura").textContent = datos.tarea.titulo;
    document.getElementById("vencimientoTareaAsignatura").textContent = datos.tarea.vencimiento;

    // ==========================================
    // REDIRECCIÓN INTELIGENTE (EVITA EL CRUCE)
    // ==========================================
    // Miramos el nombre del usuario en el header para saber si es profe
    const nombreUsuario = document.getElementById('nombreUsuarioHeader').textContent.toLowerCase();
    const esProfesor = nombreUsuario.includes('kevan') ||
        nombreUsuario.includes('pepito') ||
        nombreUsuario.includes('eolande');

    // Si es profe, va a recursosdoa.html, si es alumno a Recursosdoaalumno.html
    const paginaDestino = esProfesor ? 'recursosdoa.html' : 'Recursosdoaalumno.html';
    const urlDestino = `${paginaDestino}?materia=${idAsignatura}`;

    const linkPestanaRecursos = document.getElementById('linkPestanaRecursos');
    const linkBotonRecursos = document.getElementById('linkBotonRecursos');

    if (linkPestanaRecursos) linkPestanaRecursos.href = urlDestino;
    if (linkBotonRecursos) linkBotonRecursos.href = urlDestino;
}