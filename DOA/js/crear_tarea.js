const datos_docentes_tarea = {
  programacion: {
    grupo: "Grupo A",
    totalAlumnos: "32 alumnos"
  },

  matematicas: {
    grupo: "Grupo B",
    totalAlumnos: "28 alumnos"
  },

  fisica: {
    grupo: "Grupo A",
    totalAlumnos: "26 alumnos"
  }
};

const tareas_base_edicion = {
  programacion_tarea_api: {
    titulo: "Tarea 1: Desarrollo de APIs",
    tipo: "tarea",
    unidad: "Unidad 03",
    descripcion: "Implementa una pequeña API siguiendo los criterios explicados en clase.",
    fechaEmision: "2026-04-16",
    fechaEntrega: "2026-04-19",
    estado: "publicada",
    recursos: ["Guía de APIs", "Rúbrica de la tarea"]
  },

  programacion_practica_web: {
    titulo: "Práctica: Cooperación de webs",
    tipo: "practica",
    unidad: "Unidad 03",
    descripcion: "Entrega una práctica breve sobre integración entre páginas, flujo de navegación y uso correcto de enlaces internos.",
    fechaEmision: "2026-04-22",
    fechaEntrega: "2026-04-24",
    estado: "publicada",
    recursos: ["Enunciado de la práctica", "Ejemplo de estructura"]
  },

  programacion_tarea_mapas: {
    titulo: "Tarea 2: Seguimiento de mapas",
    tipo: "tarea",
    unidad: "Unidad 03",
    descripcion: "Revisa el flujo de navegación y documenta los pasos principales mediante un pequeño esquema.",
    fechaEmision: "2026-04-30",
    fechaEntrega: "2026-05-02",
    estado: "borrador",
    recursos: ["Plantilla de entrega"]
  },

  matematicas_limites: {
    titulo: "Ejercicio de límites",
    tipo: "tarea",
    unidad: "Unidad 03",
    descripcion: "Resuelve los ejercicios básicos de límites y justifica cada resultado.",
    fechaEmision: "2026-11-10",
    fechaEntrega: "2026-11-15",
    estado: "publicada",
    recursos: ["Hoja de ejercicios"]
  },

  matematicas_derivadas: {
    titulo: "Práctica de derivadas",
    tipo: "practica",
    unidad: "Unidad 03",
    descripcion: "Entrega una hoja de ejercicios sobre derivadas directas.",
    fechaEmision: "2026-11-03",
    fechaEntrega: "2026-11-09",
    estado: "publicada",
    recursos: ["Formulario de derivadas"]
  },

  fisica_cinematica: {
    titulo: "Ejercicio de cinemática",
    tipo: "tarea",
    unidad: "Unidad 03",
    descripcion: "Resuelve problemas sencillos de movimiento rectilíneo.",
    fechaEmision: "2026-11-14",
    fechaEntrega: "2026-11-22",
    estado: "publicada",
    recursos: ["Tabla de fórmulas"]
  },

  fisica_laboratorio: {
    titulo: "Informe de laboratorio",
    tipo: "practica",
    unidad: "Unidad 03",
    descripcion: "Entrega el informe de la práctica realizada en laboratorio.",
    fechaEmision: "2026-11-01",
    fechaEntrega: "2026-11-08",
    estado: "publicada",
    recursos: ["Guía de laboratorio"]
  }
};

let recursos_tarea = [];

const parametros = new URLSearchParams(window.location.search);
const id_asignatura = parametros.get("materia") || window.obtenerAsignaturaSeleccionada();
const id_tarea_edicion = parametros.get("tarea");
const modo_edicion = id_tarea_edicion !== null;

const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const datos_docentes = datos_docentes_tarea[id_asignatura];

window.guardarAsignaturaSeleccionada(id_asignatura);

cargar_cabecera_crear_tarea();
cargar_formulario_tarea();
actualizar_enlaces_crear_tarea();
preparar_formulario_tarea();
actualizar_resumen_tarea();

function cargar_cabecera_crear_tarea() {
  document.title = (modo_edicion ? "Editar tarea" : "Crear tarea") + " · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloAsignatura").textContent = asignatura.nombre;
  document.getElementById("grupoAsignatura").textContent = datos_docentes.grupo;
  document.getElementById("totalAlumnosAsignatura").textContent = datos_docentes.totalAlumnos;
  document.getElementById("unidadActualTextoAsignatura").textContent = asignatura.unidadActualTexto;
  document.getElementById("tituloPaginaTarea").textContent = modo_edicion ? "Editar tarea" : "Crear tarea";
}

function cargar_formulario_tarea() {
  document.getElementById("inputUnidadTarea").value = asignatura.unidadActualTexto;
  document.getElementById("inputFechaEmision").value = obtener_fecha_hoy();

  if (!modo_edicion) {
    renderizar_recursos_tarea();
    return;
  }

  const tarea = tareas_base_edicion[id_tarea_edicion];

  document.getElementById("inputTituloTarea").value = tarea.titulo;
  document.getElementById("selectTipoTarea").value = tarea.tipo;
  document.getElementById("inputUnidadTarea").value = tarea.unidad;
  document.getElementById("inputDescripcionTarea").value = tarea.descripcion;
  document.getElementById("inputFechaEmision").value = tarea.fechaEmision;
  document.getElementById("inputFechaEntrega").value = tarea.fechaEntrega;
  document.getElementById("selectEstadoTarea").value = tarea.estado;

  recursos_tarea = tarea.recursos.slice();
  renderizar_recursos_tarea();
}

function preparar_formulario_tarea() {
  document.getElementById("selectTipoTarea").addEventListener("change", actualizar_resumen_tarea);
  document.getElementById("selectEstadoTarea").addEventListener("change", actualizar_resumen_tarea);
  document.getElementById("inputRecursosTarea").addEventListener("change", cargar_recursos_seleccionados);
  document.getElementById("listaRecursosTarea").addEventListener("click", eliminar_recurso_tarea);
  document.getElementById("formularioCrearTarea").addEventListener("submit", guardar_tarea);
}

function actualizar_enlaces_crear_tarea() {
  const parametro_materia = "?materia=" + id_asignatura;

  document.getElementById("linkVolverTareas").href = "listado_tareas_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaRecursos").href = "recursos_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaTareas").href = "listado_tareas_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaExamenes").href = "examenes_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaCalificaciones").href = "calificaciones_profesor.php" + parametro_materia;
  document.getElementById("botonDescartarTarea").href = "listado_tareas_profesor.php" + parametro_materia;
}

function cargar_recursos_seleccionados() {
  const archivos = Array.from(document.getElementById("inputRecursosTarea").files);

  archivos.forEach(function (archivo) {
    recursos_tarea.push(archivo.name);
  });

  renderizar_recursos_tarea();
}

function renderizar_recursos_tarea() {
  const lista = document.getElementById("listaRecursosTarea");

  lista.innerHTML = "";

  if (recursos_tarea.length === 0) {
    lista.innerHTML = '<li class="item-sin-recursos">Todavía no hay recursos adjuntos.</li>';
    return;
  }

  recursos_tarea.forEach(function (recurso) {
    lista.insertAdjacentHTML(
      "beforeend",
      `
        <li>
          <span>${recurso}</span>
          <button type="button" data-recurso="${recurso}">Quitar</button>
        </li>
      `
    );
  });
}

function eliminar_recurso_tarea(evento) {
  if (evento.target.tagName !== "BUTTON") {
    return;
  }

  recursos_tarea = recursos_tarea.filter(function (recurso) {
    return recurso !== evento.target.dataset.recurso;
  });

  renderizar_recursos_tarea();
}

function guardar_tarea(evento) {
  evento.preventDefault();

  const tarea = crear_datos_tarea();
  const tareas_guardadas = obtener_tareas_guardadas();

  const tareas_actualizadas = tareas_guardadas.filter(function (item) {
    return item.id !== tarea.id;
  });

  tareas_actualizadas.unshift(tarea);

  localStorage.setItem("doaTareasProfesor", JSON.stringify(tareas_actualizadas));

  mostrar_mensaje_tarea_guardada();

  setTimeout(function () {
    window.location.href = "listado_tareas_profesor.php?materia=" + id_asignatura;
  }, 900);
}

function crear_datos_tarea() {
  const id_tarea = modo_edicion
    ? id_tarea_edicion
    : generar_id_tarea(document.getElementById("inputTituloTarea").value);

  return {
    id: id_tarea,
    titulo: document.getElementById("inputTituloTarea").value,
    tipo: document.getElementById("selectTipoTarea").value,
    unidad: document.getElementById("inputUnidadTarea").value,
    descripcion: document.getElementById("inputDescripcionTarea").value,
    fechaEmision: document.getElementById("inputFechaEmision").value,
    fechaEntrega: document.getElementById("inputFechaEntrega").value,
    estado: document.getElementById("selectEstadoTarea").value,
    recursos: recursos_tarea,
    asignaturaClave: id_asignatura,
    asignaturaNombre: asignatura.nombre
  };
}

function generar_id_tarea(titulo) {
  const texto_limpio = titulo
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "_")
    .replace(/^_|_$/g, "");

  return id_asignatura + "_" + texto_limpio + "_" + Date.now();
}

function obtener_tareas_guardadas() {
  return JSON.parse(localStorage.getItem("doaTareasProfesor") || "[]");
}

function actualizar_resumen_tarea() {
  const tipo = document.getElementById("selectTipoTarea");
  const estado = document.getElementById("selectEstadoTarea");

  document.getElementById("resumenAsignaturaTarea").textContent = asignatura.nombre;
  document.getElementById("resumenTipoTarea").textContent = tipo.options[tipo.selectedIndex].text;
  document.getElementById("resumenEstadoTarea").textContent = estado.options[estado.selectedIndex].text;
}

function mostrar_mensaje_tarea_guardada() {
  document.getElementById("mensajeTareaGuardada").classList.remove("mensaje-tarea-guardada--oculto");
}

function obtener_fecha_hoy() {
  const fecha = new Date();

  return fecha.toISOString().slice(0, 10);
}