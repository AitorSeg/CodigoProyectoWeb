const datos_docentes_asignatura = {
  programacion: {
    grupo: "Grupo A",
    alumnos: "32 alumnos",
    aula: "Aula 2.4",
    horario: "Lunes y miércoles",
    tareasActivas: "3",
    entregasPendientes: "14",
    recursosPublicados: "8",
    proximoExamen: "18 Nov",
    tarea: {
      titulo: "Ejercicio de recursividad",
      entregas: "14",
    },
    examen: {
      id: "programacion_parcial_01",
      titulo: "Parcial 01",
      fecha: "18 Nov, 2026",
      hora: "10:00",
      lugar: "Aula 2.4",
    },
    actividad: [
      "14 entregas en la tarea de recursividad.",
      "Se ha publicado el recurso “Ejercicios 1”.",
      "El próximo examen está programado para el 18 de noviembre.",
    ],
  },

  matematicas: {
    grupo: "Grupo B",
    alumnos: "28 alumnos",
    aula: "Aula 1.3",
    horario: "Martes y jueves",
    tareasActivas: "2",
    entregasPendientes: "9",
    recursosPublicados: "6",
    proximoExamen: "15 Nov",
    tarea: {
      titulo: "Hoja de límites",
      entregas: "9",
    },
    examen: {
      id: "matematicas_parcial_01",
      titulo: "Parcial 01",
      fecha: "15 Nov, 2026",
      hora: "09:00",
      lugar: "Aula 1.3",
    },
    actividad: [
      "9 entregas en la hoja de límites.",
      "Se recomienda publicar una guía de ejercicios resueltos.",
      "El parcial está programado para el 15 de noviembre.",
    ],
  },

  fisica: {
    grupo: "Grupo A",
    alumnos: "26 alumnos",
    aula: "Laboratorio 2",
    horario: "Viernes",
    tareasActivas: "1",
    entregasPendientes: "6",
    recursosPublicados: "5",
    proximoExamen: "22 Nov",
    tarea: {
      titulo: "Ejercicios de MRU",
      entregas: "6",
    },
    examen: {
      id: "fisica_control_unidad",
      titulo: "Control de unidad",
      fecha: "22 Nov, 2026",
      hora: "09:00",
      lugar: "Laboratorio 2",
    },
    actividad: [
      "6 entregas en ejercicios de MRU.",
      "El último recurso publicado fue “Laboratorio”.",
      "El control de unidad será el 22 de noviembre.",
    ],
  },
};

const parametros = new URLSearchParams(window.location.search);
const id_asignatura =
  parametros.get("materia") || window.obtenerAsignaturaSeleccionada();
const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const datos_docentes = datos_docentes_asignatura[id_asignatura];

window.guardarAsignaturaSeleccionada(id_asignatura);

cargar_cabecera_asignatura(asignatura, datos_docentes);
cargar_resumen_docente(datos_docentes);
cargar_unidad_actual(asignatura);
cargar_panel_lateral(datos_docentes);
actualizar_ruta_progreso(asignatura);
actualizar_enlaces_asignatura(id_asignatura);
renderizar_actividad_docente(datos_docentes.actividad);

function cargar_cabecera_asignatura(asignatura, datos_docentes) {
  document.title = "Detalle · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloAsignatura").textContent = asignatura.nombre;
  document.getElementById("grupoAsignatura").textContent = datos_docentes.grupo;
  document.getElementById("totalAlumnosAsignatura").textContent =
    datos_docentes.alumnos;
  document.getElementById("unidadActualTextoAsignatura").textContent =
    asignatura.unidadActualTexto;
}

function cargar_resumen_docente(datos_docentes) {
  document.getElementById("totalTareasActivas").textContent =
    datos_docentes.tareasActivas;
  document.getElementById("totalEntregasPendientes").textContent =
    datos_docentes.entregasPendientes;
  document.getElementById("totalRecursosPublicados").textContent =
    datos_docentes.recursosPublicados;
  document.getElementById("fechaProximoExamen").textContent =
    datos_docentes.proximoExamen;
}

function cargar_unidad_actual(asignatura) {
  document.getElementById("tituloUnidadActual").textContent =
    asignatura.unidadActualTitulo;
  document.getElementById("descripcionUnidadActual").textContent =
    asignatura.descripcion;
}

function cargar_panel_lateral(datos_docentes) {
  document.getElementById("tituloTareaDestacada").textContent =
    datos_docentes.tarea.titulo;
  document.getElementById("entregasTareaDestacada").textContent =
    datos_docentes.tarea.entregas;

  document.getElementById("tituloExamenDestacado").textContent =
    datos_docentes.examen.titulo;
  document.getElementById("fechaExamenDestacado").textContent =
    datos_docentes.examen.fecha;
  document.getElementById("horaExamenDestacado").textContent =
    datos_docentes.examen.hora;
  document.getElementById("lugarExamenDestacado").textContent =
    datos_docentes.examen.lugar;
}

function actualizar_ruta_progreso(asignatura) {
  const ruta_progreso = document.getElementById("rutaProgresoAsignatura");

  ruta_progreso.classList.remove(
    "progreso-asignatura--avance-40-33",
    "progreso-asignatura--avance-40-333",
  );

  ruta_progreso.classList.add(asignatura.progresoClase);
}

function actualizar_enlaces_asignatura(id_asignatura) {
  const parametro_materia = "?materia=" + id_asignatura;
  const parametro_asignatura = "?asignatura=" + id_asignatura;

  document.getElementById("linkPestanaRecursos").href =
    "recursos_profesor.php" + parametro_materia;
  document.getElementById("linkBotonRecursos").href =
    "recursos_profesor.php" + parametro_materia;

  document.getElementById("linkPestanaTareas").href =
    "listado_tareas_profesor.php" + parametro_materia;
  document.getElementById("linkBotonCrearTarea").href =
    "crear_tarea.php" + parametro_materia;
  document.getElementById("linkTareaDestacada").href =
    "listado_tareas_profesor.php" + parametro_materia;

  document.getElementById("linkPestanaExamenes").href =
    "examenes_profesor.php" + parametro_materia;
  document.getElementById("linkBotonCrearExamen").href =
    "crear_examen.php" + parametro_materia;
  document.getElementById("linkExamenDestacado").href =
    "detalle_examen_profesor.php" + parametro_materia + "&examen=" + datos_docentes.examen.id;

  document.getElementById("linkPestanaCalificaciones").href =
    "calificaciones_profesor.php" + parametro_materia;

  document.getElementById("linkVistaAlumno").href = "detalle_asignatura.php";
}

function renderizar_actividad_docente(actividad) {
  const lista = document.getElementById("listaActividadDocente");

  lista.innerHTML = "";

  actividad.forEach(function (mensaje) {
    const item = document.createElement("p");

    item.textContent = mensaje;
    lista.appendChild(item);
  });
}
