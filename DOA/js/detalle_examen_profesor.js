/*
    Pantalla: Detalle de examen del profesor
    Carga el examen seleccionado y muestra información de gestión.
*/

const datos_detalle_examen_profesor = {
  programacion: {
    grupo: "Grupo A",
    totalAlumnos: 32,
    examenes: {
      programacion_parcial_01: {
        nombre: "Parcial 01",
        descripcion: "Examen sobre recursividad y estructuras dinámicas.",
        fecha: "18 Nov, 2026",
        apertura: "15 Nov, 2026",
        cierre: "18 Nov, 2026",
        duracion: "45 min",
        entregas: 26,
        pendientes: 6,
        estado: "Abierto",
        estadoFiltro: "abierto",
        temas: [
          "Casos base",
          "Llamadas recursivas",
          "Pilas de llamadas",
          "Estructuras dinámicas"
        ],
        preguntas: [
          "Identificar el caso base de una función recursiva.",
          "Calcular el resultado de una llamada recursiva.",
          "Detectar un error en una función sin condición de parada.",
          "Relacionar recursividad con pilas de llamadas."
        ]
      },

      programacion_control_estructuras: {
        nombre: "Control de estructuras",
        descripcion: "Control breve sobre arrays, listas y pilas.",
        fecha: "02 Nov, 2026",
        apertura: "02 Nov, 2026",
        cierre: "02 Nov, 2026",
        duracion: "30 min",
        entregas: 32,
        pendientes: 0,
        estado: "Cerrado",
        estadoFiltro: "cerrado",
        temas: [
          "Arrays",
          "Listas",
          "Pilas",
          "Operaciones básicas"
        ],
        preguntas: [
          "Diferenciar array y lista.",
          "Interpretar una operación push.",
          "Interpretar una operación pop.",
          "Elegir la estructura adecuada para un caso."
        ]
      },

      programacion_parcial_02: {
        nombre: "Parcial 02",
        descripcion: "Examen próximo sobre árboles y grafos.",
        fecha: "12 Dic, 2026",
        apertura: "10 Dic, 2026",
        cierre: "12 Dic, 2026",
        duracion: "60 min",
        entregas: 0,
        pendientes: 0,
        estado: "Próximo",
        estadoFiltro: "proximo",
        temas: [
          "Árboles",
          "Grafos",
          "Recorridos",
          "Relaciones entre nodos"
        ],
        preguntas: [
          "Identificar nodos padre e hijo.",
          "Diferenciar árbol y grafo.",
          "Aplicar recorrido en anchura.",
          "Aplicar recorrido en profundidad."
        ]
      }
    }
  },

  matematicas: {
    grupo: "Grupo B",
    totalAlumnos: 28,
    examenes: {
      matematicas_parcial_01: {
        nombre: "Parcial 01",
        descripcion: "Examen sobre límites, derivadas e introducción a integrales.",
        fecha: "15 Nov, 2026",
        apertura: "10 Nov, 2026",
        cierre: "15 Nov, 2026",
        duracion: "45 min",
        entregas: 24,
        pendientes: 4,
        estado: "Abierto",
        estadoFiltro: "abierto",
        temas: [
          "Límites laterales",
          "Derivadas básicas",
          "Interpretación de gráficas",
          "Problemas sencillos de aplicación"
        ],
        preguntas: [
          "Resolver un límite lateral.",
          "Calcular una derivada directa.",
          "Interpretar el crecimiento en una gráfica.",
          "Aplicar derivadas en un problema básico."
        ]
      },

      matematicas_control_01: {
        nombre: "Control de funciones",
        descripcion: "Control sobre representación e interpretación de gráficas.",
        fecha: "28 Oct, 2026",
        apertura: "28 Oct, 2026",
        cierre: "28 Oct, 2026",
        duracion: "30 min",
        entregas: 28,
        pendientes: 0,
        estado: "Cerrado",
        estadoFiltro: "cerrado",
        temas: [
          "Funciones",
          "Gráficas",
          "Crecimiento",
          "Puntos de corte"
        ],
        preguntas: [
          "Identificar puntos de corte.",
          "Interpretar crecimiento y decrecimiento.",
          "Relacionar tabla y gráfica.",
          "Detectar máximos y mínimos visuales."
        ]
      }
    }
  },

  fisica: {
    grupo: "Grupo A",
    totalAlumnos: 26,
    examenes: {
      fisica_control_01: {
        nombre: "Control de cinemática",
        descripcion: "Control sobre movimiento rectilíneo y fuerzas.",
        fecha: "22 Nov, 2026",
        apertura: "20 Nov, 2026",
        cierre: "22 Nov, 2026",
        duracion: "40 min",
        entregas: 0,
        pendientes: 0,
        estado: "Próximo",
        estadoFiltro: "proximo",
        temas: [
          "MRU",
          "MRUA",
          "Fuerzas",
          "Unidades"
        ],
        preguntas: [
          "Calcular velocidad media.",
          "Interpretar aceleración.",
          "Relacionar fuerza y movimiento.",
          "Convertir unidades básicas."
        ]
      },

      fisica_cuestionario_fuerzas: {
        nombre: "Cuestionario de fuerzas",
        descripcion: "Cuestionario corto sobre conceptos básicos de fuerzas.",
        fecha: "11 Oct, 2026",
        apertura: "11 Oct, 2026",
        cierre: "11 Oct, 2026",
        duracion: "20 min",
        entregas: 26,
        pendientes: 0,
        estado: "Cerrado",
        estadoFiltro: "cerrado",
        temas: [
          "Fuerza",
          "Masa",
          "Aceleración",
          "Equilibrio"
        ],
        preguntas: [
          "Definir fuerza.",
          "Relacionar fuerza, masa y aceleración.",
          "Identificar fuerzas en equilibrio.",
          "Resolver una situación sencilla."
        ]
      }
    }
  }
};

const parametros = new URLSearchParams(window.location.search);
const id_asignatura = parametros.get("materia") || window.obtenerAsignaturaSeleccionada();
const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const datos_asignatura = datos_detalle_examen_profesor[id_asignatura];
const id_examen = parametros.get("examen") || Object.keys(datos_asignatura.examenes)[0];
const examen_actual = datos_asignatura.examenes[id_examen];

window.guardarAsignaturaSeleccionada(id_asignatura);

cargar_cabecera_examen(asignatura, datos_asignatura, examen_actual);
cargar_datos_examen(datos_asignatura, examen_actual);
renderizar_temas_examen(examen_actual.temas);
renderizar_preguntas_examen(examen_actual.preguntas);
actualizar_estado_examen(examen_actual);
actualizar_enlaces_examen(id_asignatura, id_examen);

function cargar_cabecera_examen(asignatura, datos_asignatura, examen) {
  document.title = examen.nombre + " · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloExamen").textContent = examen.nombre;
  document.getElementById("asignaturaExamen").textContent = asignatura.nombre;
  document.getElementById("grupoExamen").textContent = datos_asignatura.grupo;
  document.getElementById("fechaExamen").textContent = examen.fecha;
}

function cargar_datos_examen(datos_asignatura, examen) {
  const sin_entregar = datos_asignatura.totalAlumnos - examen.entregas;

  document.getElementById("descripcionExamen").textContent = examen.descripcion;
  document.getElementById("estadoExamen").textContent = examen.estado;

  document.getElementById("aperturaExamen").textContent = examen.apertura;
  document.getElementById("cierreExamen").textContent = examen.cierre;
  document.getElementById("duracionExamen").textContent = examen.duracion;
  document.getElementById("entregasExamen").textContent = examen.entregas + "/" + datos_asignatura.totalAlumnos;
  document.getElementById("pendientesExamen").textContent = examen.pendientes;

  document.getElementById("totalAlumnosGrupo").textContent = datos_asignatura.totalAlumnos;
  document.getElementById("totalEntregadosGrupo").textContent = examen.entregas;
  document.getElementById("totalSinEntregarGrupo").textContent = sin_entregar;
}

function renderizar_temas_examen(temas) {
  const lista = document.getElementById("temasExamen");

  lista.innerHTML = "";

  temas.forEach(function (tema) {
    const item = document.createElement("li");

    item.textContent = tema;
    lista.appendChild(item);
  });
}

function renderizar_preguntas_examen(preguntas) {
  const lista = document.getElementById("listaPreguntasProfesor");

  lista.innerHTML = "";

  preguntas.forEach(function (pregunta, indice) {
    const item = document.createElement("article");

    item.className = "pregunta-profesor";
    item.innerHTML =
      '<span>Pregunta ' + (indice + 1) + '</span>' +
      '<p>' + pregunta + '</p>';

    lista.appendChild(item);
  });
}

function actualizar_estado_examen(examen) {
  const estado = document.getElementById("estadoExamen");
  const tarjeta = document.getElementById("tarjetaDetalleExamen");

  estado.classList.remove(
    "estado-detalle-examen--cerrado",
    "estado-detalle-examen--proximo"
  );

  tarjeta.dataset.estado = examen.estadoFiltro;

  if (examen.estadoFiltro === "cerrado") {
    estado.classList.add("estado-detalle-examen--cerrado");
  }

  if (examen.estadoFiltro === "proximo") {
    estado.classList.add("estado-detalle-examen--proximo");
  }
}

function actualizar_enlaces_examen(id_asignatura, id_examen) {
  const parametro_materia = "?materia=" + id_asignatura;
  const parametro_examen = "?materia=" + id_asignatura + "&examen=" + id_examen;

  document.getElementById("linkVolverExamenes").href = "examenes_profesor.php" + parametro_materia;

  document.getElementById("linkPestanaRecursos").href = "recursos_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaTareas").href = "listado_tareas_profe.html" + parametro_materia;
  document.getElementById("linkPestanaExamenes").href = "examenes_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaCalificaciones").href = "calificaciones_profesor.php" + parametro_materia;

  document.getElementById("linkEditarExamen").href = "crearexamen.html" + parametro_examen;
  document.getElementById("linkCalificacionesExamen").href = "calificaciones_profesor.php" + parametro_materia;
  document.getElementById("linkListadoExamenes").href = "examenes_profesor.php" + parametro_materia;
}