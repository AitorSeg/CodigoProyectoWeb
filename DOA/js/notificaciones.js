let notificaciones = cargar_notificaciones_demo();
let filtro_activo = "todas";
let notificacion_seleccionada = null;

preparar_filtros_notificaciones();
preparar_acciones_notificaciones();
seleccionar_notificacion(notificaciones[0].id);

function cargar_notificaciones_demo() {
  const estado_lectura = obtener_estado_lectura_guardado();

  return [
    {
      id: "notificacion_tarea_derivadas",
      tipo: "tarea",
      tipo_texto: "Tarea",
      titulo: "Nueva tarea disponible",
      resumen: "Se ha publicado la tarea de derivadas en Matemáticas.",
      contenido: "Ya está disponible la tarea de derivadas de la Unidad 03. Revisa el enunciado, entrega la actividad antes de la fecha indicada y consulta los recursos del tema si necesitas repasar.",
      remitente: "Don Pepito",
      fecha: "Hoy",
      asignatura: "matematicas",
      accion_texto: "Ver tareas",
      accion_href: "listado_tareas.php",
      leida: estado_lectura.notificacion_tarea_derivadas === true
    },

    {
      id: "notificacion_examen_programacion",
      tipo: "aviso",
      tipo_texto: "Aviso",
      titulo: "Recordatorio de examen",
      resumen: "El examen de Programación II estará disponible próximamente.",
      contenido: "Recuerda que el examen de la Unidad 03 de Programación II estará disponible durante el periodo indicado en la sección de exámenes. Comprueba la duración antes de empezar.",
      remitente: "Profesorado de Programación",
      fecha: "Ayer",
      asignatura: "programacion",
      accion_texto: "Ver exámenes",
      accion_href: "examenes.php",
      leida: estado_lectura.notificacion_examen_programacion === true
    },

    {
      id: "notificacion_centro_mantenimiento",
      tipo: "aviso",
      tipo_texto: "Centro",
      titulo: "Mantenimiento programado",
      resumen: "El centro realizará tareas de mantenimiento fuera del horario lectivo.",
      contenido: "Se informa al alumnado de que se realizarán tareas de mantenimiento en los sistemas del centro. Durante ese periodo podrían producirse interrupciones puntuales en algunos servicios.",
      remitente: "Secretaría del centro",
      fecha: "Lun",
      accion_texto: "",
      accion_href: "",
      leida: estado_lectura.notificacion_centro_mantenimiento === true
    },

    {
      id: "notificacion_recurso_fisica",
      tipo: "recurso",
      tipo_texto: "Recurso",
      titulo: "Nuevo recurso de Física",
      resumen: "Se ha añadido una guía de repaso a la biblioteca de Física.",
      contenido: "La asignatura de Física tiene disponible un nuevo recurso de repaso relacionado con la unidad actual. Puedes consultarlo desde la sección de recursos de la asignatura.",
      remitente: "Eolande Merriton Mizzi",
      fecha: "Vie",
      asignatura: "fisica",
      accion_texto: "Ver recursos",
      accion_href: "recursos_alumno.php",
      leida: estado_lectura.notificacion_recurso_fisica === true
    },

    {
      id: "notificacion_calificacion",
      tipo: "aviso",
      tipo_texto: "Calificación",
      titulo: "Nueva calificación publicada",
      resumen: "Se ha publicado una nueva nota en Matemáticas.",
      contenido: "Tu profesor ha publicado una nueva calificación asociada a una actividad de Matemáticas. Puedes revisarla desde la sección de calificaciones de la asignatura.",
      remitente: "Don Pepito",
      fecha: "Jue",
      asignatura: "matematicas",
      accion_texto: "Ver calificaciones",
      accion_href: "calificaciones.php",
      leida: estado_lectura.notificacion_calificacion === true
    }
  ];
}

function obtener_estado_lectura_guardado() {
  const datos = localStorage.getItem("doaEstadoLecturaNotificaciones");

  return datos === null ? {} : JSON.parse(datos);
}

function guardar_estado_lectura() {
  const estado_lectura = {};

  notificaciones.forEach(function (notificacion) {
    estado_lectura[notificacion.id] = notificacion.leida;
  });

  localStorage.setItem("doaEstadoLecturaNotificaciones", JSON.stringify(estado_lectura));
}

function preparar_filtros_notificaciones() {
  const filtros = document.querySelectorAll(".filtro-notificacion");

  filtros.forEach(function (filtro) {
    filtro.addEventListener("click", function () {
      filtro_activo = filtro.dataset.filtro;

      filtros.forEach(function (boton) {
        boton.classList.toggle("filtro-notificacion--activo", boton === filtro);
      });

      renderizar_listado_notificaciones();
    });
  });
}

function preparar_acciones_notificaciones() {
  document.getElementById("botonLecturaNotificacion").addEventListener("click", cambiar_estado_lectura_notificacion_seleccionada);
  document.getElementById("botonMarcarTodas").addEventListener("click", marcar_todas_como_leidas);

  document.getElementById("botonAccionNotificacion").addEventListener("click", function () {
    guardar_asignatura_de_notificacion(notificacion_seleccionada);
  });
}

function renderizar_resumen_notificaciones() {
  const no_leidas = notificaciones.filter(function (notificacion) {
    return !notificacion.leida;
  }).length;

  const tareas = notificaciones.filter(function (notificacion) {
    return notificacion.tipo === "tarea";
  }).length;

  const avisos = notificaciones.filter(function (notificacion) {
    return notificacion.tipo === "aviso";
  }).length;

  document.getElementById("totalNoLeidas").textContent = no_leidas;
  document.getElementById("totalTareas").textContent = tareas;
  document.getElementById("totalAvisos").textContent = avisos;
}

function renderizar_listado_notificaciones() {
  const contenedor = document.getElementById("listaNotificaciones");
  const notificaciones_filtradas = obtener_notificaciones_filtradas();

  contenedor.innerHTML = "";

  if (notificaciones_filtradas.length === 0) {
    contenedor.innerHTML = '<p class="mensaje-sin-notificaciones">No hay notificaciones con este filtro.</p>';
    return;
  }

  notificaciones_filtradas.forEach(function (notificacion) {
    contenedor.appendChild(crear_bloque_notificacion(notificacion));
  });
}

function crear_bloque_notificacion(notificacion) {
  const bloque = document.createElement("article");
  const item = document.createElement("button");

  bloque.className = "bloque-notificacion";
  item.type = "button";
  item.className = "notificacion-item";

  if (!notificacion.leida) {
    item.classList.add("notificacion-item--no-leida");
  }

  if (notificacion_seleccionada !== null && notificacion_seleccionada.id === notificacion.id) {
    item.classList.add("notificacion-item--activa");
    bloque.classList.add("bloque-notificacion--activa");
  }

  item.innerHTML = `
    <div>
      <p class="notificacion-item__titulo">${notificacion.titulo}</p>
    </div>

    <span class="notificacion-item__fecha">${notificacion.fecha}</span>
    <p class="notificacion-item__resumen">${notificacion.resumen}</p>
    <span class="notificacion-item__tipo">${notificacion.tipo_texto}</span>
  `;

  item.addEventListener("click", function () {
    seleccionar_notificacion(notificacion.id);
  });

  bloque.appendChild(item);

  if (notificacion_seleccionada !== null && notificacion_seleccionada.id === notificacion.id) {
    bloque.appendChild(crear_detalle_movil_notificacion(notificacion));
  }

  return bloque;
}

function crear_detalle_movil_notificacion(notificacion) {
  const detalle = document.createElement("div");
  const boton_accion = notificacion.accion_href === ""
    ? ""
    : '<a href="' + crear_url_accion_notificacion(notificacion) + '" class="boton-accion-notificacion boton-accion-notificacion-movil">' + notificacion.accion_texto + '</a>';

  detalle.className = "detalle-notificacion-movil";
  detalle.innerHTML = `
    <div class="detalle-notificacion-movil__cabecera">
      <span class="etiqueta-notificacion">${notificacion.tipo_texto}</span>
      <p>${notificacion.remitente} · ${notificacion.fecha}</p>
    </div>

    <p class="detalle-notificacion-movil__texto">${notificacion.contenido}</p>

    <div class="detalle-notificacion-movil__acciones">
      ${boton_accion}
    </div>
  `;

  const enlace_movil = detalle.querySelector(".boton-accion-notificacion-movil");

  if (enlace_movil !== null) {
    enlace_movil.addEventListener("click", function () {
      guardar_asignatura_de_notificacion(notificacion);
    });
  }

  return detalle;
}

function obtener_notificaciones_filtradas() {
  return notificaciones.filter(function (notificacion) {
    if (filtro_activo === "todas") {
      return true;
    }

    if (filtro_activo === "no-leidas") {
      return !notificacion.leida;
    }

    return notificacion.tipo === filtro_activo;
  });
}

function seleccionar_notificacion(id_notificacion) {
  const notificacion = notificaciones.find(function (item) {
    return item.id === id_notificacion;
  });

  notificacion_seleccionada = notificacion;

  if (!notificacion.leida) {
    notificacion.leida = true;
    guardar_estado_lectura();
  }

  cargar_detalle_notificacion(notificacion);
  renderizar_resumen_notificaciones();
  renderizar_listado_notificaciones();
}

function cargar_detalle_notificacion(notificacion) {
  const boton_accion = document.getElementById("botonAccionNotificacion");

  document.getElementById("detalleTipoNotificacion").textContent = notificacion.tipo_texto;
  document.getElementById("detalleTituloNotificacion").textContent = notificacion.titulo;
  document.getElementById("detalleMetaNotificacion").textContent = notificacion.remitente + " · " + notificacion.fecha;
  document.getElementById("detalleTextoNotificacion").textContent = notificacion.contenido;
  document.getElementById("botonLecturaNotificacion").textContent = notificacion.leida ? "Marcar como no leída" : "Marcar como leída";

  if (notificacion.accion_href !== "") {
    boton_accion.classList.remove("hidden");
    boton_accion.href = crear_url_accion_notificacion(notificacion);
    boton_accion.textContent = notificacion.accion_texto;
    return;
  }

  boton_accion.classList.add("hidden");
  boton_accion.removeAttribute("href");
}

function cambiar_estado_lectura_notificacion_seleccionada() {
  notificacion_seleccionada.leida = !notificacion_seleccionada.leida;

  guardar_estado_lectura();
  renderizar_resumen_notificaciones();
  renderizar_listado_notificaciones();
  cargar_detalle_notificacion(notificacion_seleccionada);
}

function marcar_todas_como_leidas() {
  notificaciones.forEach(function (notificacion) {
    notificacion.leida = true;
  });

  guardar_estado_lectura();
  renderizar_resumen_notificaciones();
  renderizar_listado_notificaciones();
  cargar_detalle_notificacion(notificacion_seleccionada);
}

function guardar_asignatura_de_notificacion(notificacion) {
  if (notificacion.asignatura !== undefined) {
    window.guardarAsignaturaSeleccionada(notificacion.asignatura);
  }
}

function crear_url_accion_notificacion(notificacion) {
  if (notificacion.asignatura === undefined) {
    return notificacion.accion_href;
  }

  return notificacion.accion_href + "?materia=" + notificacion.asignatura;
}