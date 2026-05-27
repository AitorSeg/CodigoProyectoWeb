/*
    Pantalla: Listado de tareas del alumno
    Carga las tareas de la asignatura seleccionada y permite filtrarlas.
*/

const datos_tareas_alumno = {
  programacion: [
    {
      id: "programacion_tarea_api",
      titulo: "Tarea 1: Desarrollo de APIs",
      descripcion: "Implementa una pequeña API siguiendo los criterios explicados en clase.",
      tipo: "tarea",
      tipoTexto: "Tarea",
      fechaEmision: "16 Abr, 2026",
      fechaEntrega: "19 Abr, 2026",
      fechaOrden: "2026-04-19",
      estado: "Entregada",
      estadoFiltro: "entregada",
      claseEstado: "etiqueta-estado--entregada",
      calificacion: "8,3",
      tiempoRestante: "Entregada"
    },
    {
      id: "programacion_practica_web",
      titulo: "Práctica: Cooperación de webs",
      descripcion: "Entrega una práctica breve sobre integración entre páginas y flujo de navegación.",
      tipo: "practica",
      tipoTexto: "Práctica",
      fechaEmision: "22 Abr, 2026",
      fechaEntrega: "24 Abr, 2026",
      fechaOrden: "2026-04-24",
      estado: "Pendiente",
      estadoFiltro: "pendiente",
      claseEstado: "etiqueta-estado--pendiente",
      calificacion: "/",
      tiempoRestante: "3 días"
    },
    {
      id: "programacion_tarea_mapas",
      titulo: "Tarea 2: Seguimiento de mapas",
      descripcion: "Revisa el flujo de navegación y documenta los pasos principales.",
      tipo: "tarea",
      tipoTexto: "Tarea",
      fechaEmision: "30 Abr, 2026",
      fechaEntrega: "02 May, 2026",
      fechaOrden: "2026-05-02",
      estado: "Tardía",
      estadoFiltro: "tardia",
      claseEstado: "etiqueta-estado--tardia",
      calificacion: "/",
      tiempoRestante: "Fuera de plazo"
    }
  ],

  matematicas: [
    {
      id: "matematicas_limites",
      titulo: "Ejercicio de límites",
      descripcion: "Resuelve los ejercicios básicos de límites y justifica cada resultado.",
      tipo: "tarea",
      tipoTexto: "Tarea",
      fechaEmision: "10 Nov, 2026",
      fechaEntrega: "15 Nov, 2026",
      fechaOrden: "2026-11-15",
      estado: "Pendiente",
      estadoFiltro: "pendiente",
      claseEstado: "etiqueta-estado--pendiente",
      calificacion: "/",
      tiempoRestante: "2 días"
    },
    {
      id: "matematicas_derivadas",
      titulo: "Práctica de derivadas",
      descripcion: "Entrega una hoja de ejercicios sobre derivadas directas.",
      tipo: "practica",
      tipoTexto: "Práctica",
      fechaEmision: "03 Nov, 2026",
      fechaEntrega: "09 Nov, 2026",
      fechaOrden: "2026-11-09",
      estado: "Entregada",
      estadoFiltro: "entregada",
      claseEstado: "etiqueta-estado--entregada",
      calificacion: "7,4",
      tiempoRestante: "Entregada"
    }
  ],

  fisica: [
    {
      id: "fisica_cinematica",
      titulo: "Ejercicio de cinemática",
      descripcion: "Resuelve problemas sencillos de movimiento rectilíneo.",
      tipo: "tarea",
      tipoTexto: "Tarea",
      fechaEmision: "14 Nov, 2026",
      fechaEntrega: "22 Nov, 2026",
      fechaOrden: "2026-11-22",
      estado: "Pendiente",
      estadoFiltro: "pendiente",
      claseEstado: "etiqueta-estado--pendiente",
      calificacion: "/",
      tiempoRestante: "5 días"
    },
    {
      id: "fisica_laboratorio",
      titulo: "Informe de laboratorio",
      descripcion: "Entrega el informe de la práctica realizada en laboratorio.",
      tipo: "practica",
      tipoTexto: "Práctica",
      fechaEmision: "01 Nov, 2026",
      fechaEntrega: "08 Nov, 2026",
      fechaOrden: "2026-11-08",
      estado: "Entregada",
      estadoFiltro: "entregada",
      claseEstado: "etiqueta-estado--entregada",
      calificacion: "8,1",
      tiempoRestante: "Entregada"
    }
  ]
};

const parametros = new URLSearchParams(window.location.search);
const id_asignatura = parametros.get("materia") || window.obtenerAsignaturaSeleccionada();
const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const tareas_actuales = datos_tareas_alumno[id_asignatura];

window.guardarAsignaturaSeleccionada(id_asignatura);

cargar_cabecera_tareas();
actualizar_enlaces_tareas();
cargar_proxima_tarea();
preparar_filtros_tareas();
renderizar_tareas();

function cargar_cabecera_tareas() {
  document.title = "Tareas · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloAsignatura").textContent = asignatura.nombre;
  document.getElementById("profesorAsignatura").textContent = asignatura.profesor;
  document.getElementById("unidadActualTextoAsignatura").textContent = asignatura.unidadActualTexto;
}

function actualizar_enlaces_tareas() {
  const parametro_materia = "?materia=" + id_asignatura;

  document.getElementById("linkVolverDetalle").href = "detalle_asignatura.php" + parametro_materia;
  document.getElementById("linkPestanaRecursos").href = "recursos_alumno.php" + parametro_materia;
  document.getElementById("linkPestanaTareas").href = "listado_tareas.php" + parametro_materia;
  document.getElementById("linkPestanaExamenes").href = "examenes.php" + parametro_materia;
  document.getElementById("linkPestanaCalificaciones").href = "calificaciones.php" + parametro_materia;
}

function cargar_proxima_tarea() {
  const proxima_tarea = tareas_actuales.find(function (tarea) {
    return tarea.estadoFiltro === "pendiente";
  }) || tareas_actuales[0];

  document.getElementById("estadoProximaTarea").textContent = proxima_tarea.estado;
  document.getElementById("estadoProximaTarea").className = "etiqueta-estado " + proxima_tarea.claseEstado;
  document.getElementById("tituloProximaTarea").textContent = proxima_tarea.titulo;
  document.getElementById("descripcionProximaTarea").textContent = proxima_tarea.descripcion;
  document.getElementById("tiempoProximaTarea").textContent = proxima_tarea.tiempoRestante;
  document.getElementById("linkProximaTarea").href = crear_url_detalle_tarea(proxima_tarea.id);
}

function preparar_filtros_tareas() {
  document.getElementById("filtroTipo").addEventListener("change", renderizar_tareas);
  document.getElementById("filtroEstado").addEventListener("change", renderizar_tareas);
  document.getElementById("ordenTareas").addEventListener("change", renderizar_tareas);
}

function renderizar_tareas() {
  const tipo_seleccionado = document.getElementById("filtroTipo").value;
  const estado_seleccionado = document.getElementById("filtroEstado").value;
  const orden_seleccionado = document.getElementById("ordenTareas").value;

  let tareas_filtradas = tareas_actuales.filter(function (tarea) {
    const coincide_tipo = tipo_seleccionado === "todas" || tarea.tipo === tipo_seleccionado;
    const coincide_estado = estado_seleccionado === "todos" || tarea.estadoFiltro === estado_seleccionado;

    return coincide_tipo && coincide_estado;
  });

  tareas_filtradas = ordenar_tareas(tareas_filtradas, orden_seleccionado);

  cargar_tabla_tareas(tareas_filtradas);
}

function ordenar_tareas(tareas, orden_seleccionado) {
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

  if (orden_seleccionado === "estado") {
    copia_tareas.sort(function (a, b) {
      return a.estado.localeCompare(b.estado);
    });
  }

  return copia_tareas;
}

function cargar_tabla_tareas(tareas) {
  const cuerpo_tabla = document.getElementById("cuerpoTablaTareas");

  cuerpo_tabla.innerHTML = "";

  if (tareas.length === 0) {
    cuerpo_tabla.innerHTML = '<p class="mensaje-tabla-vacia">No hay tareas que coincidan con los filtros seleccionados.</p>';
    return;
  }

  tareas.forEach(function (tarea) {
    cuerpo_tabla.insertAdjacentHTML(
      "beforeend",
      `
        <article class="fila-tarea" data-tipo="${tarea.tipo}" data-estado="${tarea.estadoFiltro}" data-fecha-entrega="${tarea.fechaOrden}">
          <a class="fila-tarea__nombre" href="${crear_url_detalle_tarea(tarea.id)}">
            ${tarea.titulo}
          </a>

          <p><strong>${tarea.fechaEmision}</strong></p>
          <p><strong>${tarea.fechaEntrega}</strong></p>

          <p>
            <span class="etiqueta-estado ${tarea.claseEstado}">
              ${tarea.estado}
            </span>
          </p>

          <p class="fila-tarea__nota">
            <strong>${tarea.calificacion}</strong>
          </p>
        </article>
      `
    );
  });
}

function crear_url_detalle_tarea(id_tarea) {
  return "detalle_tarea.php?materia=" + id_asignatura + "&tarea=" + id_tarea;
}