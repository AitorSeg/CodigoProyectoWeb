/*
    Pantalla: Detalle de asignatura
    Carga la asignatura seleccionada y actualiza el contenido principal.
*/

const id_asignatura = window.obtenerAsignaturaSeleccionada() || "programacion";
const datos_asignatura = window.DOA_ASIGNATURAS[id_asignatura] || window.DOA_ASIGNATURAS.programacion;

window.guardarAsignaturaSeleccionada(id_asignatura);

renderizar_detalle_asignatura(datos_asignatura, id_asignatura);

function renderizar_detalle_asignatura(datos, id_asignatura) {
  document.getElementById("tituloAsignatura").textContent = datos.nombre;
  document.getElementById("profesorAsignatura").textContent = datos.profesor;
  document.getElementById("unidadActualTextoAsignatura").textContent = datos.unidadActualTexto;

  document.getElementById("tituloUnidadActual").textContent = datos.unidadActualTitulo;
  document.getElementById("descripcionUnidadActual").textContent = datos.descripcion;

  actualizar_ruta_progreso(datos);
  actualizar_panel_lateral(datos);
  actualizar_enlaces_asignatura(id_asignatura);
}

function actualizar_ruta_progreso(datos) {
  const ruta_progreso = document.getElementById("rutaProgresoAsignatura");

  ruta_progreso.classList.remove(
    "progreso-asignatura--avance-40-33",
    "progreso-asignatura--avance-40-333"
  );

  ruta_progreso.classList.add(datos.progresoClase);
}

function actualizar_panel_lateral(datos) {
  document.getElementById("tituloEvaluacionAsignatura").textContent = datos.evaluacion.titulo;
  document.getElementById("fechaEvaluacionAsignatura").textContent = datos.evaluacion.fecha;
  document.getElementById("horaEvaluacionAsignatura").textContent = datos.evaluacion.hora;
  document.getElementById("lugarEvaluacionAsignatura").textContent = datos.evaluacion.lugar;

  document.getElementById("tituloTareaAsignatura").textContent = datos.tarea.titulo;
  document.getElementById("vencimientoTareaAsignatura").textContent = datos.tarea.vencimiento;
}

function actualizar_enlaces_asignatura(id_asignatura) {
  const url_recursos = "recursos_alumno.php?materia=" + id_asignatura;

  document.getElementById("linkPestanaRecursos").href = url_recursos;
  document.getElementById("linkBotonRecursos").href = url_recursos;
}