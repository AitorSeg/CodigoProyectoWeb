const asignaturas_base_secretaria = {
  programacion: {
    id: "programacion",
    nombre: "Programación II",
    codigo: "GTI-203",
    curso: "2º",
    grupo: "A",
    estado: "activa",
    profesor: "kevan",
    alumnos: ["lief", "ana", "marc"]
  },

  matematicas: {
    id: "matematicas",
    nombre: "Matemáticas",
    codigo: "GTI-104",
    curso: "2º",
    grupo: "B",
    estado: "activa",
    profesor: "pepito",
    alumnos: ["nuria", "pablo"]
  },

  fisica: {
    id: "fisica",
    nombre: "Física",
    codigo: "GTI-112",
    curso: "2º",
    grupo: "A",
    estado: "activa",
    profesor: "eolande",
    alumnos: ["pedro"]
  },

  interfaces: {
    id: "interfaces",
    nombre: "Diseño de Interfaces",
    codigo: "GTI-221",
    curso: "2º",
    grupo: "C",
    estado: "pendiente",
    profesor: "",
    alumnos: []
  }
};

const profesores_secretaria = {
  kevan: {
    nombre: "Kevan Pounds Mainston"
  },

  pepito: {
    nombre: "Don Pepito"
  },

  eolande: {
    nombre: "Eolande Merriton Mizzi"
  },

  luelle: {
    nombre: "Luelle Pridmore Starsmeare"
  }
};

const alumnos_secretaria = {
  lief: {
    nombre: "Lief Simants",
    descripcion: "Alumno GTI · 2ºA"
  },

  ana: {
    nombre: "Ana Torres",
    descripcion: "Alumno GTI · 2ºA"
  },

  marc: {
    nombre: "Marc Vidal",
    descripcion: "Alumno GTI · 2ºA"
  },

  nuria: {
    nombre: "Núria Esteve",
    descripcion: "Alumno GTI · 2ºB"
  },

  pablo: {
    nombre: "Pablo Barceló",
    descripcion: "Alumno GTI · 2ºB"
  },

  pedro: {
    nombre: "Pedro Fernández",
    descripcion: "Alumno GTI · 2ºC"
  }
};

const select_asignatura_secretaria = document.getElementById("selectAsignaturaSecretaria");
const select_profesor_secretaria = document.getElementById("selectProfesorSecretaria");
const lista_alumnos_secretaria = document.getElementById("listaAlumnosSecretaria");
const formulario_asignaciones_secretaria = document.getElementById("formAsignacionesSecretaria");

let asignaturas_secretaria = obtener_asignaturas_secretaria();

renderizar_opciones_asignaturas();
renderizar_opciones_profesores();
renderizar_lista_alumnos();
preparar_eventos_asignaciones();
cargar_asignacion_seleccionada();
actualizar_resumen_global_asignaciones();

function obtener_asignaturas_secretaria() {
  const asignaturas_creadas = JSON.parse(localStorage.getItem("doaAsignaturasSecretaria") || "[]");
  const asignaturas = Object.assign({}, asignaturas_base_secretaria);

  asignaturas_creadas.forEach(function (asignatura) {
    asignaturas[asignatura.id] = {
      id: asignatura.id,
      nombre: asignatura.nombre,
      codigo: asignatura.codigo,
      curso: asignatura.curso,
      grupo: asignatura.grupo,
      estado: asignatura.estado,
      profesor: asignatura.profesor,
      alumnos: asignatura.alumnos
    };
  });

  return asignaturas;
}

function renderizar_opciones_asignaturas() {
  select_asignatura_secretaria.innerHTML = "";

  Object.values(asignaturas_secretaria).forEach(function (asignatura) {
    const opcion = document.createElement("option");

    opcion.value = asignatura.id;
    opcion.textContent = asignatura.nombre + " · Grupo " + asignatura.grupo;

    select_asignatura_secretaria.appendChild(opcion);
  });
}

function renderizar_opciones_profesores() {
  select_profesor_secretaria.innerHTML = '<option value="">Sin profesor asignado</option>';

  Object.keys(profesores_secretaria).forEach(function (id_profesor) {
    const opcion = document.createElement("option");

    opcion.value = id_profesor;
    opcion.textContent = profesores_secretaria[id_profesor].nombre;

    select_profesor_secretaria.appendChild(opcion);
  });
}

function renderizar_lista_alumnos() {
  lista_alumnos_secretaria.innerHTML = "";

  Object.keys(alumnos_secretaria).forEach(function (id_alumno) {
    const alumno = alumnos_secretaria[id_alumno];

    lista_alumnos_secretaria.insertAdjacentHTML(
      "beforeend",
      `
        <label class="item-alumno-secretaria">
          <input type="checkbox" value="${id_alumno}">
          <span>
            <strong>${alumno.nombre}</strong>
            <small>${alumno.descripcion}</small>
          </span>
        </label>
      `
    );
  });
}

function preparar_eventos_asignaciones() {
  select_asignatura_secretaria.addEventListener("change", function () {
    cargar_asignacion_seleccionada();
    ocultar_mensaje_asignaciones();
  });

  select_profesor_secretaria.addEventListener("change", function () {
    actualizar_resumen_asignacion();
    ocultar_mensaje_asignaciones();
  });

  lista_alumnos_secretaria.addEventListener("change", function () {
    actualizar_resumen_asignacion();
    ocultar_mensaje_asignaciones();
  });

  formulario_asignaciones_secretaria.addEventListener("submit", function (evento) {
    evento.preventDefault();

    guardar_asignacion_secretaria();
    mostrar_mensaje_asignaciones();
    actualizar_resumen_global_asignaciones();
  });
}

function cargar_asignacion_seleccionada() {
  const id_asignatura = select_asignatura_secretaria.value;
  const asignacion = obtener_asignacion_guardada(id_asignatura);

  select_profesor_secretaria.value = asignacion.profesor;

  document.querySelectorAll(".item-alumno-secretaria input").forEach(function (check) {
    check.checked = asignacion.alumnos.includes(check.value);
  });

  actualizar_resumen_asignacion();
}

function obtener_asignacion_guardada(id_asignatura) {
  const asignaciones_guardadas = obtener_asignaciones_guardadas();

  return asignaciones_guardadas[id_asignatura] || asignaturas_secretaria[id_asignatura];
}

function obtener_asignaciones_guardadas() {
  return JSON.parse(localStorage.getItem("doaAsignacionesSecretaria") || "{}");
}

function actualizar_resumen_asignacion() {
  const id_asignatura = select_asignatura_secretaria.value;
  const asignatura = asignaturas_secretaria[id_asignatura];
  const profesor_seleccionado = select_profesor_secretaria.value;
  const alumnos_seleccionados = obtener_alumnos_seleccionados();
  const total_alumnos = alumnos_seleccionados.length;

  document.getElementById("resumenNombreAsignatura").textContent = asignatura.nombre;
  document.getElementById("resumenCodigoAsignatura").textContent = asignatura.codigo;
  document.getElementById("resumenAlumnosAsignatura").textContent = total_alumnos + " asignados";
  document.getElementById("contadorAlumnosSecretaria").textContent = total_alumnos + " seleccionados";

  if (profesor_seleccionado === "") {
    document.getElementById("resumenProfesorAsignatura").textContent = "Pendiente";
  } else {
    document.getElementById("resumenProfesorAsignatura").textContent =
      profesores_secretaria[profesor_seleccionado].nombre;
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
  const asignatura = asignaturas_secretaria[id_asignatura];
  const asignaciones_guardadas = obtener_asignaciones_guardadas();

  asignaciones_guardadas[id_asignatura] = {
    id: id_asignatura,
    nombre: asignatura.nombre,
    codigo: asignatura.codigo,
    curso: asignatura.curso,
    grupo: asignatura.grupo,
    estado: obtener_estado_asignacion(),
    profesor: select_profesor_secretaria.value,
    alumnos: obtener_alumnos_seleccionados()
  };

  localStorage.setItem("doaAsignacionesSecretaria", JSON.stringify(asignaciones_guardadas));
}

function obtener_estado_asignacion() {
  const profesor_seleccionado = select_profesor_secretaria.value;
  const total_alumnos = obtener_alumnos_seleccionados().length;

  if (profesor_seleccionado !== "" && total_alumnos > 0) {
    return "activa";
  }

  return "pendiente";
}

function obtener_alumnos_seleccionados() {
  const checks_seleccionados = document.querySelectorAll(".item-alumno-secretaria input:checked");
  const alumnos = [];

  checks_seleccionados.forEach(function (check) {
    alumnos.push(check.value);
  });

  return alumnos;
}

function actualizar_resumen_global_asignaciones() {
  const asignaciones_guardadas = obtener_asignaciones_guardadas();
  const asignaturas = Object.values(asignaturas_secretaria);

  let pendientes = 0;

  asignaturas.forEach(function (asignatura) {
    const asignacion = asignaciones_guardadas[asignatura.id] || asignatura;

    if (asignacion.profesor === "" || asignacion.alumnos.length === 0) {
      pendientes++;
    }
  });

  document.getElementById("totalAsignaturasSecretaria").textContent = asignaturas.length;
  document.getElementById("totalProfesoresSecretaria").textContent = Object.keys(profesores_secretaria).length;
  document.getElementById("totalAlumnosSecretaria").textContent = Object.keys(alumnos_secretaria).length;
  document.getElementById("totalPendientesSecretaria").textContent = pendientes;
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