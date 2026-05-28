const datos_tareas_profesor = {
  programacion: {
    grupo: "Grupo A",
    totalAlumnos: "32 alumnos",
    resumen: {
      activas: "3",
      entregas: "46",
      pendientes: "14",
      cerradas: "1"
    },
    tareas: [
      {
        id: "programacion_tarea_api",
        titulo: "Tarea 1: Desarrollo de APIs",
        descripcion: "Implementación de una pequeña API siguiendo los criterios explicados en clase.",
        tipo: "tarea",
        fechaEntrega: "19 Abr, 2026",
        fechaOrden: "2026-04-19",
        entregas: "32/32",
        pendientes: 0,
        estado: "Cerrada",
        estadoFiltro: "cerrada"
      },
      {
        id: "programacion_practica_web",
        titulo: "Práctica: Cooperación de webs",
        descripcion: "Práctica breve sobre integración entre páginas y flujo de navegación.",
        tipo: "practica",
        fechaEntrega: "24 Abr, 2026",
        fechaOrden: "2026-04-24",
        entregas: "14/32",
        pendientes: 18,
        estado: "Publicada",
        estadoFiltro: "publicada"
      },
      {
        id: "programacion_tarea_mapas",
        titulo: "Tarea 2: Seguimiento de mapas",
        descripcion: "Documentación del flujo de navegación mediante un pequeño esquema.",
        tipo: "tarea",
        fechaEntrega: "02 May, 2026",
        fechaOrden: "2026-05-02",
        entregas: "0/32",
        pendientes: 32,
        estado: "Borrador",
        estadoFiltro: "borrador"
      }
    ]
  },

  matematicas: {
    grupo: "Grupo B",
    totalAlumnos: "28 alumnos",
    resumen: {
      activas: "2",
      entregas: "21",
      pendientes: "7",
      cerradas: "1"
    },
    tareas: [
      {
        id: "matematicas_limites",
        titulo: "Ejercicio de límites",
        descripcion: "Ejercicios básicos de límites con justificación.",
        tipo: "tarea",
        fechaEntrega: "15 Nov, 2026",
        fechaOrden: "2026-11-15",
        entregas: "21/28",
        pendientes: 7,
        estado: "Publicada",
        estadoFiltro: "publicada"
      },
      {
        id: "matematicas_derivadas",
        titulo: "Práctica de derivadas",
        descripcion: "Hoja de ejercicios sobre derivadas directas.",
        tipo: "practica",
        fechaEntrega: "09 Nov, 2026",
        fechaOrden: "2026-11-09",
        entregas: "28/28",
        pendientes: 0,
        estado: "Cerrada",
        estadoFiltro: "cerrada"
      }
    ]
  },

  fisica: {
    grupo: "Grupo A",
    totalAlumnos: "26 alumnos",
    resumen: {
      activas: "2",
      entregas: "19",
      pendientes: "7",
      cerradas: "1"
    },
    tareas: [
      {
        id: "fisica_cinematica",
        titulo: "Ejercicio de cinemática",
        descripcion: "Problemas sencillos de movimiento rectilíneo.",
        tipo: "tarea",
        fechaEntrega: "22 Nov, 2026",
        fechaOrden: "2026-11-22",
        entregas: "19/26",
        pendientes: 7,
        estado: "Publicada",
        estadoFiltro: "publicada"
      },
      {
        id: "fisica_laboratorio",
        titulo: "Informe de laboratorio",
        descripcion: "Informe de la práctica realizada en laboratorio.",
        tipo: "practica",
        fechaEntrega: "08 Nov, 2026",
        fechaOrden: "2026-11-08",
        entregas: "26/26",
        pendientes: 0,
        estado: "Cerrada",
        estadoFiltro: "cerrada"
      }
    ]
  }
};

const parametros = new URLSearchParams(window.location.search);
const id_asignatura = parametros.get("materia") || window.obtenerAsignaturaSeleccionada();
const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const datos_tareas = datos_tareas_profesor[id_asignatura];

window.guardarAsignaturaSeleccionada(id_asignatura);

cargar_cabecera_tareas_profesor();
cargar_resumen_tareas_profesor();
actualizar_enlaces_tareas_profesor();
preparar_filtros_tareas_profesor();
renderizar_tareas_profesor();

function cargar_cabecera_tareas_profesor() {
  document.title = "Tareas · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloAsignatura").textContent = asignatura.nombre;
  document.getElementById("grupoAsignatura").textContent = datos_tareas.grupo;
  document.getElementById("totalAlumnosAsignatura").textContent = datos_tareas.totalAlumnos;
  document.getElementById("unidadActualTextoAsignatura").textContent = asignatura.unidadActualTexto;
}

function cargar_resumen_tareas_profesor() {
  document.getElementById("totalTareasActivas").textContent = datos_tareas.resumen.activas;
  document.getElementById("totalEntregasRecibidas").textContent = datos_tareas.resumen.entregas;
  document.getElementById("totalPendientesRevision").textContent = datos_tareas.resumen.pendientes;
  document.getElementById("totalTareasCerradas").textContent = datos_tareas.resumen.cerradas;
}

function actualizar_enlaces_tareas_profesor() {
  const parametro_materia = "?materia=" + id_asignatura;

  document.getElementById("linkVolverDetalle").href = "detalle_asignatura_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaRecursos").href = "recursos_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaTareas").href = "listado_tareas_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaExamenes").href = "examenes_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaCalificaciones").href = "calificaciones_profesor.php" + parametro_materia;
  document.getElementById("linkCrearTarea").href = "crear_tarea.php" + parametro_materia;
}

function preparar_filtros_tareas_profesor() {
  document.getElementById("filtroTipo").addEventListener("change", renderizar_tareas_profesor);
  document.getElementById("filtroEstado").addEventListener("change", renderizar_tareas_profesor);
  document.getElementById("ordenTareas").addEventListener("change", renderizar_tareas_profesor);
}

function renderizar_tareas_profesor() {
  const tipo_seleccionado = document.getElementById("filtroTipo").value;
  const estado_seleccionado = document.getElementById("filtroEstado").value;
  const orden_seleccionado = document.getElementById("ordenTareas").value;

  let tareas_filtradas = datos_tareas.tareas.filter(function (tarea) {
    const coincide_tipo = tipo_seleccionado === "todas" || tarea.tipo === tipo_seleccionado;
    const coincide_estado = estado_seleccionado === "todos" || tarea.estadoFiltro === estado_seleccionado;

    return coincide_tipo && coincide_estado;
  });

  tareas_filtradas = ordenar_tareas_profesor(tareas_filtradas, orden_seleccionado);

  cargar_tabla_tareas_profesor(tareas_filtradas);
}

function ordenar_tareas_profesor(tareas, orden_seleccionado) {
  const copia_tareas = tareas.slice();

  if (orden_seleccionado === "fecha_entrega") {
    copia_tareas.sort(function (a, b) {
      return new Date(a.fechaOrden) - new Date(b.fechaOrden);
    });
  }

  if (orden_seleccionado === "nombre") {
    copia_tareas.sort(function (a, b) {
      return a.titulo.localeCompare(b.titulo);
    });
  }

  if (orden_seleccionado === "pendientes") {
    copia_tareas.sort(function (a, b) {
      return b.pendientes - a.pendientes;
    });
  }

  return copia_tareas;
}

function cargar_tabla_tareas_profesor(tareas) {
  const cuerpo_tabla = document.getElementById("cuerpoTablaTareasProfesor");

  cuerpo_tabla.innerHTML = "";

  if (tareas.length === 0) {
    cuerpo_tabla.innerHTML = '<p class="mensaje-tabla-vacia">No hay tareas que coincidan con los filtros seleccionados.</p>';
    return;
  }

  tareas.forEach(function (tarea) {
    cuerpo_tabla.insertAdjacentHTML(
      "beforeend",
      `
        <article class="fila-tarea fila-tarea-profesor">
          <a class="fila-tarea__nombre" href="${crear_url_detalle_tarea_profesor(tarea.id)}">
            ${tarea.titulo}
          </a>

          <p><strong>${tarea.fechaEntrega}</strong></p>
          <p><strong>${tarea.entregas}</strong></p>
          <p><strong>${tarea.pendientes}</strong></p>

          <p>
            <span class="etiqueta-estado etiqueta-estado--${tarea.estadoFiltro}">
              ${tarea.estado}
            </span>
          </p>

          <a class="boton-editar-tarea" href="${crear_url_editar_tarea(tarea.id)}">
            Editar
          </a>
        </article>
      `
    );
  });
}

function crear_url_detalle_tarea_profesor(id_tarea) {
  return "detalle_tarea_profesor.php?materia=" + id_asignatura + "&tarea=" + id_tarea;
}

function crear_url_editar_tarea(id_tarea) {
  return "crear_tarea.php?materia=" + id_asignatura + "&tarea=" + id_tarea;
}