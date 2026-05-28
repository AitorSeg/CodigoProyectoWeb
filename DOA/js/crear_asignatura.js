const formulario_crear_asignatura = document.getElementById("formCrearAsignatura");

formulario_crear_asignatura.addEventListener("submit", guardar_asignatura_secretaria);

function guardar_asignatura_secretaria(evento) {
  evento.preventDefault();

  limpiar_errores_crear_asignatura();

  const asignatura = obtener_datos_asignatura();

  if (!validar_asignatura(asignatura)) {
    return;
  }

  guardar_asignatura_demo(asignatura);
  mostrar_mensaje_asignatura_creada();
  formulario_crear_asignatura.reset();
}

function obtener_datos_asignatura() {
  const nombre = document.getElementById("nombreAsignatura").value.trim();
  const codigo = document.getElementById("codigoAsignatura").value.trim();
  const curso = document.getElementById("cursoAsignatura").value;
  const grupo = document.getElementById("grupoAsignatura").value;
  const descripcion = document.getElementById("descripcionAsignatura").value.trim();
  const estado = document.getElementById("estadoAsignatura").value;

  return {
    id: crear_id_asignatura(nombre, grupo),
    nombre: nombre,
    codigo: codigo,
    curso: curso,
    grupo: grupo,
    descripcion: descripcion,
    estado: estado,
    profesor: "",
    alumnos: []
  };
}

function validar_asignatura(asignatura) {
  let formulario_valido = true;

  if (asignatura.nombre === "") {
    mostrar_error_crear_asignatura("errorNombreAsignatura", "Introduce el nombre de la asignatura.");
    formulario_valido = false;
  }

  if (asignatura.codigo === "") {
    mostrar_error_crear_asignatura("errorCodigoAsignatura", "Introduce el código de la asignatura.");
    formulario_valido = false;
  }

  if (asignatura.curso === "") {
    mostrar_error_crear_asignatura("errorCursoAsignatura", "Selecciona el curso.");
    formulario_valido = false;
  }

  if (asignatura.grupo === "") {
    mostrar_error_crear_asignatura("errorGrupoAsignatura", "Selecciona el grupo.");
    formulario_valido = false;
  }

  return formulario_valido;
}

function mostrar_error_crear_asignatura(id_elemento, mensaje) {
  document.getElementById(id_elemento).textContent = mensaje;
}

function limpiar_errores_crear_asignatura() {
  const errores = document.querySelectorAll(".mensaje-error-campo");

  errores.forEach(function (error) {
    error.textContent = "";
  });
}

function crear_id_asignatura(nombre, grupo) {
  const nombre_limpio = nombre
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "_")
    .replace(/^_|_$/g, "");

  return nombre_limpio + "_" + grupo.toLowerCase();
}

function guardar_asignatura_demo(asignatura) {
  const asignaturas_guardadas = obtener_asignaturas_guardadas();

  const asignaturas_actualizadas = asignaturas_guardadas.filter(function (item) {
    return item.id !== asignatura.id;
  });

  asignaturas_actualizadas.unshift(asignatura);

  localStorage.setItem("doaAsignaturasSecretaria", JSON.stringify(asignaturas_actualizadas));
}

function obtener_asignaturas_guardadas() {
  return JSON.parse(localStorage.getItem("doaAsignaturasSecretaria") || "[]");
}

function mostrar_mensaje_asignatura_creada() {
  const mensaje = document.getElementById("mensajeFormularioAsignatura");

  mensaje.classList.remove("mensaje-formulario-secretaria--oculto");

  setTimeout(function () {
    mensaje.classList.add("mensaje-formulario-secretaria--oculto");
  }, 3500);
}