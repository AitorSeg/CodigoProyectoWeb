const datos_asignaciones_secretaria = {
  programacion: {
    nombre: "Programación II",
    codigo: "GTI-203",
    profesor: "kevan",
    alumnos: ["lief", "ana", "marc"]
  },

  matematicas: {
    nombre: "Matemáticas",
    codigo: "GTI-104",
    profesor: "pepito",
    alumnos: ["nuria", "pablo"]
  },

  fisica: {
    nombre: "Física",
    codigo: "GTI-112",
    profesor: "eolande",
    alumnos: ["pedro"]
  },

  interfaces: {
    nombre: "Diseño de Interfaces",
    codigo: "GTI-221",
    profesor: "",
    alumnos: []
  }
};

const nombres_profesores_secretaria = {
  kevan: "Kevan Pounds Mainston",
  pepito: "Don Pepito",
  eolande: "Eolande Merriton Mizzi",
  luelle: "Luelle Pridmore Starsmeare"
};

const select_asignatura_secretaria = document.getElementById("selectAsignaturaSecretaria");
const select_profesor_secretaria = document.getElementById("selectProfesorSecretaria");
const formulario_asignaciones_secretaria = document.getElementById("formAsignacionesSecretaria");
const checks_alumnos_secretaria = document.querySelectorAll(".item-alumno-secretaria input");

cargar_asignacion_seleccionada();
preparar_eventos_asignaciones();

function preparar_eventos_asignaciones() {
  select_asignatura_secretaria.addEventListener("change", function () {
    cargar_asignacion_seleccionada();
    ocultar_mensaje_asignaciones();
  });

  select_profesor_secretaria.addEventListener("change", function () {
    actualizar_resumen_asignacion();
    ocultar_mensaje_asignaciones();
  });

  checks_alumnos_secretaria.forEach(function (check) {
    check.addEventListener("change", function () {
      actualizar_resumen_asignacion();
      ocultar_mensaje_asignaciones();
    });
  });

  formulario_asignaciones_secretaria.addEventListener("submit", function (evento) {
    evento.preventDefault();

    guardar_asignacion_secretaria();
    mostrar_mensaje_asignaciones();
  });
}

function cargar_asignacion_seleccionada() {
  const id_asignatura = select_asignatura_secretaria.value;
  const datos = obtener_asignacion_guardada(id_asignatura);

  select_profesor_secretaria.value = datos.profesor;

  checks_alumnos_secretaria.forEach(function (check) {
    check.checked = datos.alumnos.includes(check.value);
  });

  actualizar_resumen_asignacion();
}

function obtener_asignacion_guardada(id_asignatura) {
  const asignaciones_guardadas = obtener_asignaciones_guardadas();

  return asignaciones_guardadas[id_asignatura] || datos_asignaciones_secretaria[id_asignatura];
}

function obtener_asignaciones_guardadas() {
  const guardado = localStorage.getItem("doaAsignacionesSecretaria");

  return guardado === null ? {} : JSON.parse(guardado);
}

function actualizar_resumen_asignacion() {
  const id_asignatura = select_asignatura_secretaria.value;
  const datos_base = datos_asignaciones_secretaria[id_asignatura];
  const profesor_seleccionado = select_profesor_secretaria.value;
  const alumnos_seleccionados = obtener_alumnos_seleccionados();
  const total_alumnos = alumnos_seleccionados.length;

  document.getElementById("resumenNombreAsignatura").textContent = datos_base.nombre;
  document.getElementById("resumenCodigoAsignatura").textContent = datos_base.codigo;
  document.getElementById("resumenAlumnosAsignatura").textContent = total_alumnos + " asignados";
  document.getElementById("contadorAlumnosSecretaria").textContent = total_alumnos + " seleccionados";

  if (profesor_seleccionado === "") {
    document.getElementById("resumenProfesorAsignatura").textContent = "Pendiente";
  } else {
    document.getElementById("resumenProfesorAsignatura").textContent = nombres_profesores_secretaria[profesor_seleccionado];
  }

  actualizar_estado_resumen(profesor_seleccionado, total_alumnos);
}

function actualizar_estado_resumen(profesor_seleccionado, total_alumnos) {
  const estado = document.getElementById("resumenEstadoAsignatura");

  estado.classList.remove("estado-secretaria--completa", "estado-secretaria--pendiente");

  if (profesor_seleccionado !== "" && total_alumnos > 0) {
    estado.textContent = "Completa";
    estado.classList.add("estado-secretaria--completa");
    return;
  }

  estado.textContent = "Pendiente";
  estado.classList.add("estado-secretaria--pendiente");
}

function guardar_asignacion_secretaria() {
  const id_asignatura = select_asignatura_secretaria.value;
  const datos_base = datos_asignaciones_secretaria[id_asignatura];
  const asignaciones_guardadas = obtener_asignaciones_guardadas();

  asignaciones_guardadas[id_asignatura] = {
    nombre: datos_base.nombre,
    codigo: datos_base.codigo,
    profesor: select_profesor_secretaria.value,
    alumnos: obtener_alumnos_seleccionados()
  };

  localStorage.setItem("doaAsignacionesSecretaria", JSON.stringify(asignaciones_guardadas));
}

function obtener_alumnos_seleccionados() {
  const checks_seleccionados = document.querySelectorAll(".item-alumno-secretaria input:checked");
  const alumnos = [];

  checks_seleccionados.forEach(function (check) {
    alumnos.push(check.value);
  });

  return alumnos;
}

function mostrar_mensaje_asignaciones() {
  const mensaje = document.getElementById("mensajeAsignacionesSecretaria");

  mensaje.classList.remove("mensaje-formulario-secretaria--oculto");

  setTimeout(function () {
    mensaje.classList.add("mensaje-formulario-secretaria--oculto");
  }, 3500);
}

function ocultar_mensaje_asignaciones() {
  document.getElementById("mensajeAsignacionesSecretaria").classList.add("mensaje-formulario-secretaria--oculto");
}