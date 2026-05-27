const datos_detalle_tareas_alumno = {
  programacion: {
    programacion_tarea_api: {
      titulo: "Tarea 1: Desarrollo de APIs",
      descripcion: "Implementa una pequeña API siguiendo los criterios explicados en clase. Entrega el archivo principal y una breve explicación del funcionamiento.",
      fechaEmision: "16 Abr, 2026",
      fechaEntrega: "19 Abr, 2026",
      estado: "Entregada",
      estadoFiltro: "entregada",
      claseEstado: "etiqueta-estado--entregada",
      calificacion: "8,3",
      archivos: ["informe_api.pdf", "capturas_funcionamiento.zip"],
      recursos: ["Guía de APIs", "Rúbrica de la tarea"]
    },

    programacion_practica_web: {
      titulo: "Práctica: Cooperación de webs",
      descripcion: "Entrega una práctica breve sobre integración entre páginas, flujo de navegación y uso correcto de enlaces internos.",
      fechaEmision: "22 Abr, 2026",
      fechaEntrega: "24 Abr, 2026",
      estado: "Pendiente",
      estadoFiltro: "pendiente",
      claseEstado: "etiqueta-estado--pendiente",
      calificacion: "/",
      archivos: [],
      recursos: ["Enunciado de la práctica", "Ejemplo de estructura"]
    },

    programacion_tarea_mapas: {
      titulo: "Tarea 2: Seguimiento de mapas",
      descripcion: "Revisa el flujo de navegación y documenta los pasos principales mediante un pequeño esquema.",
      fechaEmision: "30 Abr, 2026",
      fechaEntrega: "02 May, 2026",
      estado: "Tardía",
      estadoFiltro: "tardia",
      claseEstado: "etiqueta-estado--tardia",
      calificacion: "/",
      archivos: [],
      recursos: ["Plantilla de entrega", "Material de apoyo"]
    }
  },

  matematicas: {
    matematicas_limites: {
      titulo: "Ejercicio de límites",
      descripcion: "Resuelve los ejercicios básicos de límites y justifica cada resultado.",
      fechaEmision: "10 Nov, 2026",
      fechaEntrega: "15 Nov, 2026",
      estado: "Pendiente",
      estadoFiltro: "pendiente",
      claseEstado: "etiqueta-estado--pendiente",
      calificacion: "/",
      archivos: [],
      recursos: ["Hoja de ejercicios", "Ejemplo resuelto"]
    },

    matematicas_derivadas: {
      titulo: "Práctica de derivadas",
      descripcion: "Entrega una hoja de ejercicios sobre derivadas directas.",
      fechaEmision: "03 Nov, 2026",
      fechaEntrega: "09 Nov, 2026",
      estado: "Entregada",
      estadoFiltro: "entregada",
      claseEstado: "etiqueta-estado--entregada",
      calificacion: "7,4",
      archivos: ["derivadas_resueltas.pdf"],
      recursos: ["Formulario de derivadas"]
    }
  },

  fisica: {
    fisica_cinematica: {
      titulo: "Ejercicio de cinemática",
      descripcion: "Resuelve problemas sencillos de movimiento rectilíneo.",
      fechaEmision: "14 Nov, 2026",
      fechaEntrega: "22 Nov, 2026",
      estado: "Pendiente",
      estadoFiltro: "pendiente",
      claseEstado: "etiqueta-estado--pendiente",
      calificacion: "/",
      archivos: [],
      recursos: ["Enunciado de cinemática", "Tabla de fórmulas"]
    },

    fisica_laboratorio: {
      titulo: "Informe de laboratorio",
      descripcion: "Entrega el informe de la práctica realizada en laboratorio.",
      fechaEmision: "01 Nov, 2026",
      fechaEntrega: "08 Nov, 2026",
      estado: "Entregada",
      estadoFiltro: "entregada",
      claseEstado: "etiqueta-estado--entregada",
      calificacion: "8,1",
      archivos: ["informe_laboratorio.pdf"],
      recursos: ["Guía de laboratorio"]
    }
  }
};

const parametros = new URLSearchParams(window.location.search);
const id_asignatura = parametros.get("materia") || window.obtenerAsignaturaSeleccionada();
const tareas_asignatura = datos_detalle_tareas_alumno[id_asignatura];
const id_tarea = parametros.get("tarea") || Object.keys(tareas_asignatura)[0];

const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const tarea = tareas_asignatura[id_tarea];

window.guardarAsignaturaSeleccionada(id_asignatura);

cargar_cabecera_tarea();
cargar_detalle_tarea();
actualizar_enlaces_tarea();
preparar_acciones_tarea();

function cargar_cabecera_tarea() {
  document.title = tarea.titulo + " · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloAsignatura").textContent = asignatura.nombre;
  document.getElementById("profesorAsignatura").textContent = asignatura.profesor;
  document.getElementById("unidadActualTextoAsignatura").textContent = asignatura.unidadActualTexto;
}

function cargar_detalle_tarea() {
  document.getElementById("estadoTarea").textContent = tarea.estado;
  document.getElementById("estadoTarea").className = "etiqueta-estado " + tarea.claseEstado;

  document.getElementById("tituloTarea").textContent = tarea.titulo;
  document.getElementById("descripcionTarea").textContent = tarea.descripcion;
  document.getElementById("fechaEmisionTarea").textContent = tarea.fechaEmision;
  document.getElementById("fechaEntregaTarea").textContent = tarea.fechaEntrega;
  document.getElementById("calificacionTarea").textContent = tarea.calificacion;

  renderizar_archivos_entrega(tarea.archivos);
  renderizar_recursos_adjuntos(tarea.recursos);
}

function actualizar_enlaces_tarea() {
  const parametro_materia = "?materia=" + id_asignatura;

  document.getElementById("linkVolverTareas").href = "listado_tareas.php" + parametro_materia;
  document.getElementById("linkPestanaRecursos").href = "recursos_alumno.php" + parametro_materia;
  document.getElementById("linkPestanaTareas").href = "listado_tareas.php" + parametro_materia;
  document.getElementById("linkPestanaExamenes").href = "examenes.php" + parametro_materia;
  document.getElementById("linkPestanaCalificaciones").href = "calificaciones.php" + parametro_materia;
  document.getElementById("linkCancelarTarea").href = "listado_tareas.php" + parametro_materia;
}

function preparar_acciones_tarea() {
  document.getElementById("archivoEntrega").addEventListener("change", cargar_archivos_seleccionados);

  document.getElementById("btnGuardarTarea").addEventListener("click", function () {
    mostrar_mensaje_tarea("Entrega guardada correctamente.");
  });

  document.getElementById("btnEntregarTarea").addEventListener("click", entregar_tarea);
}

function cargar_archivos_seleccionados() {
  const archivos = Array.from(document.getElementById("archivoEntrega").files);
  const nombres_archivos = archivos.map(function (archivo) {
    return archivo.name;
  });

  renderizar_archivos_entrega(nombres_archivos);
}

function renderizar_archivos_entrega(archivos) {
  const lista = document.getElementById("listaArchivosEntrega");

  lista.innerHTML = "";

  if (archivos.length === 0) {
    lista.innerHTML = '<li class="item-sin-archivos">Todavía no has seleccionado ningún archivo.</li>';
    return;
  }

  archivos.forEach(function (archivo) {
    lista.insertAdjacentHTML(
      "beforeend",
      `
        <li>
          <a href="#">
            <img alt="" src="img/iconos/grey-file.svg">
            ${archivo}
          </a>
        </li>
      `
    );
  });
}

function renderizar_recursos_adjuntos(recursos) {
  const lista = document.getElementById("listaRecursosAdjuntos");

  lista.innerHTML = "";

  recursos.forEach(function (recurso) {
    lista.insertAdjacentHTML(
      "beforeend",
      `
        <li>
          <a href="#">
            <span>
              <img alt="" src="img/iconos/grey-file.svg">
              ${recurso}
            </span>

            <img alt="" src="img/iconos/grey-download.svg">
          </a>
        </li>
      `
    );
  });
}

function entregar_tarea() {
  const archivos = document.getElementById("archivoEntrega").files;

  if (archivos.length === 0 && tarea.archivos.length === 0) {
    mostrar_mensaje_tarea("Selecciona al menos un archivo antes de entregar.");
    return;
  }

  document.getElementById("estadoTarea").textContent = "Entregada";
  document.getElementById("estadoTarea").className = "etiqueta-estado etiqueta-estado--entregada";

  mostrar_mensaje_tarea("Tarea entregada correctamente.");
}

function mostrar_mensaje_tarea(texto) {
  const mensaje = document.getElementById("mensajeTarea");

  mensaje.textContent = texto;
  mensaje.classList.remove("mensaje-tarea--oculto");

  setTimeout(function () {
    mensaje.classList.add("mensaje-tarea--oculto");
  }, 3000);
}