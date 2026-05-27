const datos_docentes_examen = {
  programacion: {
    grupo: "Grupo A"
  },

  matematicas: {
    grupo: "Grupo B"
  },

  fisica: {
    grupo: "Grupo A"
  }
};

const examenes_base_edicion = {
  programacion_parcial_01: {
    nombre: "Parcial 01",
    unidad: "Unidad 03",
    descripcion: "Examen sobre recursividad y estructuras dinámicas.",
    duracion: "45",
    intentos: "1"
  },

  programacion_control_estructuras: {
    nombre: "Control de estructuras",
    unidad: "Unidad 02",
    descripcion: "Control breve sobre arrays, listas y pilas.",
    duracion: "30",
    intentos: "1"
  },

  programacion_parcial_02: {
    nombre: "Parcial 02",
    unidad: "Unidad 04",
    descripcion: "Examen próximo sobre árboles y grafos.",
    duracion: "60",
    intentos: "1"
  },

  matematicas_parcial_01: {
    nombre: "Parcial 01",
    unidad: "Unidad 03",
    descripcion: "Examen sobre límites, derivadas e introducción a integrales.",
    duracion: "45",
    intentos: "1"
  },

  matematicas_control_01: {
    nombre: "Control de funciones",
    unidad: "Unidad 02",
    descripcion: "Control sobre representación e interpretación de gráficas.",
    duracion: "30",
    intentos: "1"
  },

  fisica_control_01: {
    nombre: "Control de cinemática",
    unidad: "Unidad 03",
    descripcion: "Control sobre movimiento rectilíneo y fuerzas.",
    duracion: "40",
    intentos: "1"
  },

  fisica_cuestionario_fuerzas: {
    nombre: "Cuestionario de fuerzas",
    unidad: "Unidad 01",
    descripcion: "Cuestionario corto sobre conceptos básicos de fuerzas.",
    duracion: "20",
    intentos: "1"
  }
};

let contador_preguntas = 0;
let id_asignatura_actual = null;

const parametros = new URLSearchParams(window.location.search);
const id_examen_edicion = parametros.get("examen");
const modo_edicion = id_examen_edicion !== null;

id_asignatura_actual = parametros.get("materia") || window.obtenerAsignaturaSeleccionada();

window.guardarAsignaturaSeleccionada(id_asignatura_actual);

cargar_cabecera_examen();
cargar_formulario_inicial();
actualizar_enlaces_examen();
preparar_formulario_examen();
actualizar_resumen_publicacion();

function cargar_cabecera_examen() {
  const asignatura = window.DOA_ASIGNATURAS[id_asignatura_actual];
  const datos_docentes = datos_docentes_examen[id_asignatura_actual];

  document.title = (modo_edicion ? "Editar examen" : "Crear examen") + " · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloPaginaExamen").textContent = modo_edicion ? "Editar examen" : "Crear examen";
  document.getElementById("tituloAsignatura").textContent = asignatura.nombre;
  document.getElementById("grupoAsignatura").textContent = datos_docentes.grupo;
  document.getElementById("unidadActualTextoAsignatura").textContent = asignatura.unidadActualTexto;
}

function cargar_formulario_inicial() {
  document.getElementById("selectAsignatura").value = id_asignatura_actual;

  if (modo_edicion) {
    const examen = examenes_base_edicion[id_examen_edicion];

    document.getElementById("inputNombre").value = examen.nombre;
    document.getElementById("inputUnidad").value = examen.unidad;
    document.getElementById("inputDescripcion").value = examen.descripcion;
    document.getElementById("inputDuracion").value = examen.duracion;
    document.getElementById("inputIntentos").value = examen.intentos;

    agregar_pregunta({
      enunciado: "Pregunta de ejemplo del examen seleccionado.",
      opcionA: "Opción A",
      opcionB: "Opción B",
      opcionC: "Opción C",
      correcta: "a",
      explicacion: "Explicación de ejemplo."
    });

    return;
  }

  document.getElementById("inputUnidad").value = window.DOA_ASIGNATURAS[id_asignatura_actual].unidadActualTexto;
  agregar_pregunta();
}

function preparar_formulario_examen() {
  document.getElementById("selectAsignatura").addEventListener("change", cambiar_asignatura_examen);
  document.getElementById("btnAnadirPregunta").addEventListener("click", agregar_pregunta);
  document.getElementById("formularioCrearExamen").addEventListener("submit", guardar_examen);
  document.getElementById("contenedorPreguntas").addEventListener("click", gestionar_click_preguntas);
}

function cambiar_asignatura_examen() {
  id_asignatura_actual = document.getElementById("selectAsignatura").value;

  window.guardarAsignaturaSeleccionada(id_asignatura_actual);

  cargar_cabecera_examen();
  actualizar_enlaces_examen();
  actualizar_resumen_publicacion();
}

function agregar_pregunta(datos_pregunta) {
  contador_preguntas++;

  const id_pregunta = "pregunta_" + contador_preguntas;
  const contenedor = document.getElementById("contenedorPreguntas");
  const tarjeta = document.createElement("article");

  tarjeta.className = "bloque-pregunta-crear";
  tarjeta.dataset.pregunta = id_pregunta;

  const enunciado = datos_pregunta ? datos_pregunta.enunciado : "";
  const opcion_a = datos_pregunta ? datos_pregunta.opcionA : "";
  const opcion_b = datos_pregunta ? datos_pregunta.opcionB : "";
  const opcion_c = datos_pregunta ? datos_pregunta.opcionC : "";
  const correcta = datos_pregunta ? datos_pregunta.correcta : "";
  const explicacion = datos_pregunta ? datos_pregunta.explicacion : "";

  tarjeta.innerHTML =
    '<div class="cabecera-pregunta-crear">' +
      '<h3>Pregunta <span class="numero-pregunta-crear"></span></h3>' +
      '<button class="btn-eliminar-pregunta" type="button">Eliminar</button>' +
    '</div>' +
    '<div class="grupo-campo-formulario">' +
      '<label>Enunciado de la pregunta</label>' +
      '<textarea class="input-enunciado" rows="2" placeholder="Escribe el enunciado..." required>' + enunciado + '</textarea>' +
    '</div>' +
    '<div class="opciones-contenedor">' +
      '<label class="label-secundario">Opciones</label>' +
      crear_opcion_pregunta(id_pregunta, "a", "Opción A", opcion_a, correcta) +
      crear_opcion_pregunta(id_pregunta, "b", "Opción B", opcion_b, correcta) +
      crear_opcion_pregunta(id_pregunta, "c", "Opción C", opcion_c, correcta) +
    '</div>' +
    '<div class="grupo-campo-formulario grupo-campo-sin-margen">' +
      '<label>Explicación de la respuesta</label>' +
      '<input class="input-explicacion" type="text" placeholder="Explica por qué esta es la respuesta correcta..." value="' + explicacion + '" required>' +
    '</div>';

  contenedor.appendChild(tarjeta);

  renumerar_preguntas();
  actualizar_resumen_publicacion();
}

function crear_opcion_pregunta(id_pregunta, valor, placeholder, texto, correcta) {
  const checked = correcta === valor ? " checked" : "";
  const required = valor === "a" ? " required" : "";

  return (
    '<div class="fila-opcion-crear">' +
      '<input type="radio" name="correcta_' + id_pregunta + '" value="' + valor + '"' + checked + required + '>' +
      '<input type="text" class="input-opcion-' + valor + '" placeholder="' + placeholder + '" value="' + texto + '" required>' +
    '</div>'
  );
}

function gestionar_click_preguntas(evento) {
  if (!evento.target.classList.contains("btn-eliminar-pregunta")) {
    return;
  }

  evento.target.closest(".bloque-pregunta-crear").remove();

  renumerar_preguntas();
  actualizar_resumen_publicacion();
}

function renumerar_preguntas() {
  const preguntas = document.querySelectorAll(".bloque-pregunta-crear");

  preguntas.forEach(function (pregunta, indice) {
    pregunta.querySelector(".numero-pregunta-crear").textContent = indice + 1;
    pregunta.querySelector(".btn-eliminar-pregunta").hidden = preguntas.length === 1;
  });
}

function guardar_examen(evento) {
  evento.preventDefault();

  const preguntas = obtener_preguntas_formulario();
  const examen = crear_datos_examen(preguntas);
  const examenes_guardados = obtener_examenes_guardados();

  const examenes_actualizados = examenes_guardados.filter(function (item) {
    return item.id !== examen.id;
  });

  examenes_actualizados.unshift(examen);

  localStorage.setItem("doaExamenesProfesor", JSON.stringify(examenes_actualizados));

  mostrar_mensaje_guardado();

  setTimeout(function () {
    window.location.href = "examenes_profesor.php?materia=" + id_asignatura_actual;
  }, 900);
}

function obtener_preguntas_formulario() {
  const bloques = document.querySelectorAll(".bloque-pregunta-crear");
  const preguntas = [];

  bloques.forEach(function (bloque, indice) {
    const radio_correcto = bloque.querySelector('input[type="radio"]:checked');

    preguntas.push({
      id: "pregunta_" + (indice + 1),
      enunciado: bloque.querySelector(".input-enunciado").value,
      correcta: radio_correcto.value,
      explicacion: bloque.querySelector(".input-explicacion").value,
      opciones: [
        {
          id: "a",
          texto: bloque.querySelector(".input-opcion-a").value
        },
        {
          id: "b",
          texto: bloque.querySelector(".input-opcion-b").value
        },
        {
          id: "c",
          texto: bloque.querySelector(".input-opcion-c").value
        }
      ]
    });
  });

  return preguntas;
}

function crear_datos_examen(preguntas) {
  const asignatura = window.DOA_ASIGNATURAS[id_asignatura_actual];
  const id_examen = modo_edicion
    ? id_examen_edicion
    : generar_id_examen(document.getElementById("inputNombre").value);

  return {
    id: id_examen,
    nombre: document.getElementById("inputNombre").value,
    descripcion: document.getElementById("inputDescripcion").value,
    fecha: formatear_fecha(document.getElementById("inputFechaCierre").value),
    apertura: formatear_fecha(document.getElementById("inputFechaApertura").value),
    cierre: formatear_fecha(document.getElementById("inputFechaCierre").value),
    duracion: document.getElementById("inputDuracion").value + " min",
    entregas: "0/0",
    pendientes: "0",
    estado: "Abierto",
    estadoFiltro: "abierto",
    asignaturaClave: id_asignatura_actual,
    asignaturaNombre: asignatura.nombre,
    unidad: document.getElementById("inputUnidad").value,
    preguntas: preguntas
  };
}

function generar_id_examen(nombre) {
  const texto_limpio = nombre
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "_")
    .replace(/^_|_$/g, "");

  return id_asignatura_actual + "_" + texto_limpio + "_" + Date.now();
}

function formatear_fecha(valor) {
  const fecha = new Date(valor + "T00:00:00");
  const meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

  return fecha.getDate() + " " + meses[fecha.getMonth()] + ", " + fecha.getFullYear();
}

function obtener_examenes_guardados() {
  return JSON.parse(localStorage.getItem("doaExamenesProfesor") || "[]");
}

function actualizar_enlaces_examen() {
  const parametro_materia = "?materia=" + id_asignatura_actual;

  document.getElementById("linkVolverExamenes").href = "examenes_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaRecursos").href = "recursos_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaTareas").href = "listado_tareas_profe.html" + parametro_materia;
  document.getElementById("linkPestanaExamenes").href = "examenes_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaCalificaciones").href = "calificaciones_profesor.php" + parametro_materia;
  document.getElementById("botonDescartarExamen").href = "examenes_profesor.php" + parametro_materia;
}

function actualizar_resumen_publicacion() {
  const asignatura = window.DOA_ASIGNATURAS[id_asignatura_actual];
  const total_preguntas = document.querySelectorAll(".bloque-pregunta-crear").length;

  document.getElementById("resumenAsignaturaExamen").textContent = asignatura.nombre;
  document.getElementById("resumenPreguntasExamen").textContent = total_preguntas;
}

function mostrar_mensaje_guardado() {
  document.getElementById("mensajeExamenGuardado").classList.remove("mensaje-examen-guardado--oculto");
}