/*
    DOA - Datos comunes de exámenes
*/

window.DOA_EXAMENES = {
  matematicas: [
    {
      id: "matematicas_parcial_01",
      nombre: "Parcial 01",
      asignatura: "Matemáticas",
      unidad: "Unidad 03: Límites",
      descripcion:
        "Examen tipo test sobre límites, derivadas e introducción a integrales.",
      descripcionCorta: "Límites y derivadas",
      fechaCompleta: "15 Nov, 2026",
      fechaCorta: "15 Nov",
      fechaApertura: "10 Nov, 2026",
      fechaCierre: "15 Nov, 2026",
      duracion: "45 min",
      preguntas: "10 preguntas",
      intentos: "1 intento",
      estado: "Abierto",
      estadoFiltro: "abierto",
      temas: [
        "Límites laterales",
        "Derivadas básicas",
        "Interpretación de gráficas",
        "Problemas sencillos de aplicación",
      ],
    },
    {
      id: "matematicas_quiz_derivadas",
      nombre: "Quiz de derivadas",
      asignatura: "Matemáticas",
      unidad: "Unidad 02: Derivadas",
      descripcion: "Cuestionario breve de repaso sobre derivadas básicas.",
      descripcionCorta: "Cuestionario corregido",
      fechaCompleta: "12 Oct, 2026",
      fechaCorta: "12 Oct",
      fechaApertura: "10 Oct, 2026",
      fechaCierre: "12 Oct, 2026",
      duracion: "20 min",
      preguntas: "6 preguntas",
      intentos: "1 intento",
      estado: "Cerrado",
      estadoFiltro: "cerrado",
      temas: ["Derivadas inmediatas", "Regla de la suma", "Regla del producto"],
    },
    {
      id: "matematicas_parcial_02",
      nombre: "Parcial 02",
      asignatura: "Matemáticas",
      unidad: "Unidad 04: Integrales",
      descripcion: "Segundo examen parcial de la asignatura.",
      descripcionCorta: "Integrales y aplicaciones",
      fechaCompleta: "28 Nov, 2026",
      fechaCorta: "28 Nov",
      fechaApertura: "28 Nov, 2026",
      fechaCierre: "28 Nov, 2026",
      duracion: "50 min",
      preguntas: "12 preguntas",
      intentos: "1 intento",
      estado: "Próximo",
      estadoFiltro: "proximo",
      temas: [
        "Integrales básicas",
        "Área bajo la curva",
        "Problemas de aplicación",
      ],
    },
  ],

  programacion: [
    {
      id: "programacion_recursividad",
      nombre: "Examen unidad 03",
      asignatura: "Programación II",
      unidad: "Unidad 03: Recursividad",
      descripcion:
        "Examen tipo test sobre recursividad, caso base y llamadas recursivas.",
      descripcionCorta: "Recursividad",
      fechaCompleta: "15 Nov, 2026",
      fechaCorta: "15 Nov",
      fechaApertura: "10 Nov, 2026",
      fechaCierre: "15 Nov, 2026",
      duracion: "35 min",
      preguntas: "10 preguntas",
      intentos: "1 intento",
      estado: "Abierto",
      estadoFiltro: "abierto",
      temas: [
        "Caso base",
        "Llamada recursiva",
        "Recursividad directa",
        "Errores comunes con recursividad",
      ],
    },
    {
      id: "programacion_arrays",
      nombre: "Quiz arrays",
      asignatura: "Programación II",
      unidad: "Unidad 01: Arrays",
      descripcion: "Cuestionario de repaso de arrays y estructuras básicas.",
      descripcionCorta: "Arrays y bucles",
      fechaCompleta: "10 Oct, 2026",
      fechaCorta: "10 Oct",
      fechaApertura: "08 Oct, 2026",
      fechaCierre: "10 Oct, 2026",
      duracion: "20 min",
      preguntas: "8 preguntas",
      intentos: "1 intento",
      estado: "Cerrado",
      estadoFiltro: "cerrado",
      temas: ["Arrays", "Bucles", "Recorrido de listas"],
    },
    {
      id: "programacion_grafos",
      nombre: "Examen grafos",
      asignatura: "Programación II",
      unidad: "Unidad 04: Grafos",
      descripcion: "Examen próximo sobre grafos y recorridos básicos.",
      descripcionCorta: "Grafos",
      fechaCompleta: "22 Nov, 2026",
      fechaCorta: "22 Nov",
      fechaApertura: "22 Nov, 2026",
      fechaCierre: "22 Nov, 2026",
      duracion: "45 min",
      preguntas: "12 preguntas",
      intentos: "1 intento",
      estado: "Próximo",
      estadoFiltro: "proximo",
      temas: [
        "Nodos y aristas",
        "Recorridos básicos",
        "Búsqueda en anchura",
        "Búsqueda en profundidad",
      ],
    },
  ],

  fisica: [
    {
      id: "fisica_cinematica",
      nombre: "Control de cinemática",
      asignatura: "Física",
      unidad: "Unidad 03: Movimiento y fuerzas",
      descripcion:
        "Examen tipo test sobre movimiento, velocidad y aceleración.",
      descripcionCorta: "Movimiento y fuerzas",
      fechaCompleta: "19 Nov, 2026",
      fechaCorta: "19 Nov",
      fechaApertura: "15 Nov, 2026",
      fechaCierre: "19 Nov, 2026",
      duracion: "40 min",
      preguntas: "10 preguntas",
      intentos: "1 intento",
      estado: "Abierto",
      estadoFiltro: "abierto",
      temas: [
        "Velocidad",
        "Aceleración",
        "Fuerzas",
        "Interpretación de problemas",
      ],
    },
    {
      id: "fisica_fuerzas",
      nombre: "Cuestionario de fuerzas",
      asignatura: "Física",
      unidad: "Unidad 02: Fuerzas",
      descripcion: "Cuestionario corregido sobre fuerzas y leyes básicas.",
      descripcionCorta: "Fuerzas",
      fechaCompleta: "11 Oct, 2026",
      fechaCorta: "11 Oct",
      fechaApertura: "09 Oct, 2026",
      fechaCierre: "11 Oct, 2026",
      duracion: "25 min",
      preguntas: "8 preguntas",
      intentos: "1 intento",
      estado: "Cerrado",
      estadoFiltro: "cerrado",
      temas: ["Fuerzas básicas", "Leyes de Newton", "Resolución de problemas"],
    },
  ],
};

window.obtenerExamenesAsignatura = function (id_asignatura) {
  return window.DOA_EXAMENES[id_asignatura];
};

window.guardarExamenSeleccionado = function (id_examen) {
  localStorage.setItem("doaExamenSeleccionado", id_examen);
};

window.obtenerExamenSeleccionado = function () {
  return localStorage.getItem("doaExamenSeleccionado");
};

window.buscarExamenPorId = function (id_examen) {
  const asignaturas = Object.keys(window.DOA_EXAMENES);

  for (let i = 0; i < asignaturas.length; i++) {
    const examenes = window.DOA_EXAMENES[asignaturas[i]];

    for (let j = 0; j < examenes.length; j++) {
      if (examenes[j].id === id_examen) {
        return examenes[j];
      }
    }
  }

  return null;
};

window.obtenerExamenActual = function () {
  const id_examen = window.obtenerExamenSeleccionado();
  const examen_guardado = window.buscarExamenPorId(id_examen);

  if (examen_guardado !== null) {
    return examen_guardado;
  }

  const id_asignatura = window.obtenerAsignaturaSeleccionada();
  const examenes_asignatura = window.obtenerExamenesAsignatura(id_asignatura);

  return examenes_asignatura[0];
};
