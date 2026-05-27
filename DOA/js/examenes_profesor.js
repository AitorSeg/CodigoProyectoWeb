/*
    Pantalla: Exámenes del profesor
    Carga los exámenes de la asignatura seleccionada y permite filtrarlos por estado.
*/

const datos_examenes_profesor = {
  programacion: {
    grupo: "Grupo A",
    totalAlumnos: "32 alumnos",
    resumen: {
      publicados: "3",
      abiertos: "1",
      entregas: "26",
      pendientes: "6"
    },
    examenes: [
      {
        id: "programacion_parcial_01",
        nombre: "Parcial 01",
        descripcion: "Recursividad y estructuras dinámicas",
        fecha: "18 Nov, 2026",
        duracion: "45 min",
        entregas: "26/32",
        pendientes: "6",
        estado: "Abierto",
        estadoFiltro: "abierto"
      },
      {
        id: "programacion_control_estructuras",
        nombre: "Control de estructuras",
        descripcion: "Arrays, listas y pilas",
        fecha: "02 Nov, 2026",
        duracion: "30 min",
        entregas: "32/32",
        pendientes: "0",
        estado: "Cerrado",
        estadoFiltro: "cerrado"
      },
      {
        id: "programacion_parcial_02",
        nombre: "Parcial 02",
        descripcion: "Árboles y grafos",
        fecha: "12 Dic, 2026",
        duracion: "60 min",
        entregas: "0/32",
        pendientes: "0",
        estado: "Próximo",
        estadoFiltro: "proximo"
      }
    ]
  },

  matematicas: {
    grupo: "Grupo B",
    totalAlumnos: "28 alumnos",
    resumen: {
      publicados: "2",
      abiertos: "1",
      entregas: "24",
      pendientes: "4"
    },
    examenes: [
      {
        id: "matematicas_parcial_01",
        nombre: "Parcial 01",
        descripcion: "Límites, derivadas e introducción a integrales",
        fecha: "15 Nov, 2026",
        duracion: "45 min",
        entregas: "24/28",
        pendientes: "4",
        estado: "Abierto",
        estadoFiltro: "abierto"
      },
      {
        id: "matematicas_control_01",
        nombre: "Control de funciones",
        descripcion: "Representación e interpretación de gráficas",
        fecha: "28 Oct, 2026",
        duracion: "30 min",
        entregas: "28/28",
        pendientes: "0",
        estado: "Cerrado",
        estadoFiltro: "cerrado"
      }
    ]
  },

  fisica: {
    grupo: "Grupo A",
    totalAlumnos: "26 alumnos",
    resumen: {
      publicados: "2",
      abiertos: "0",
      entregas: "26",
      pendientes: "0"
    },
    examenes: [
      {
        id: "fisica_control_01",
        nombre: "Control de cinemática",
        descripcion: "Movimiento rectilíneo y fuerzas",
        fecha: "22 Nov, 2026",
        duracion: "40 min",
        entregas: "0/26",
        pendientes: "0",
        estado: "Próximo",
        estadoFiltro: "proximo"
      },
      {
        id: "fisica_cuestionario_fuerzas",
        nombre: "Cuestionario de fuerzas",
        descripcion: "Conceptos básicos de fuerzas",
        fecha: "11 Oct, 2026",
        duracion: "20 min",
        entregas: "26/26",
        pendientes: "0",
        estado: "Cerrado",
        estadoFiltro: "cerrado"
      }
    ]
  }
};

let filtro_activo = "todos";

const parametros = new URLSearchParams(window.location.search);
const id_asignatura = parametros.get("materia") || window.obtenerAsignaturaSeleccionada();
const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const datos_examenes = datos_examenes_profesor[id_asignatura];

window.guardarAsignaturaSeleccionada(id_asignatura);

cargar_cabecera_examenes(asignatura, datos_examenes);
cargar_resumen_examenes(datos_examenes);
actualizar_enlaces_examenes(id_asignatura);
preparar_filtros_examenes();
renderizar_examenes_profesor();

function cargar_cabecera_examenes(asignatura, datos_examenes) {
  document.title = "Exámenes · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloAsignatura").textContent = asignatura.nombre;
  document.getElementById("grupoAsignatura").textContent = datos_examenes.grupo;
  document.getElementById("totalAlumnosAsignatura").textContent = datos_examenes.totalAlumnos;
  document.getElementById("unidadActualTextoAsignatura").textContent = asignatura.unidadActualTexto;
}

function cargar_resumen_examenes(datos_examenes) {
  document.getElementById("totalExamenesPublicados").textContent = datos_examenes.resumen.publicados;
  document.getElementById("totalExamenesAbiertos").textContent = datos_examenes.resumen.abiertos;
  document.getElementById("totalEntregasRecibidas").textContent = datos_examenes.resumen.entregas;
  document.getElementById("totalPendientesRevision").textContent = datos_examenes.resumen.pendientes;
}

function actualizar_enlaces_examenes(id_asignatura) {
  const parametro_materia = "?materia=" + id_asignatura;

  document.getElementById("linkVolverDetalle").href = "detalle_asignatura_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaRecursos").href = "recursos_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaTareas").href = "listado_tareas_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaExamenes").href = "examenes_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaCalificaciones").href = "calificaciones_profesor.php" + parametro_materia;
  document.getElementById("linkCrearExamen").href = "crear_examen.php" + parametro_materia;
}

function preparar_filtros_examenes() {
  const filtros = document.querySelectorAll(".filtro-examen");

  filtros.forEach(function (filtro) {
    filtro.addEventListener("click", function () {
      filtro_activo = filtro.dataset.filtro;

      filtros.forEach(function (boton) {
        boton.classList.toggle("filtro-examen--activo", boton === filtro);
      });

      renderizar_examenes_profesor();
    });
  });
}

function renderizar_examenes_profesor() {
  const contenedor = document.getElementById("listadoExamenesProfesor");

  contenedor.innerHTML = "";

  const examenes_filtrados = datos_examenes.examenes.filter(function (examen) {
    return filtro_activo === "todos" || examen.estadoFiltro === filtro_activo;
  });

  if (examenes_filtrados.length === 0) {
    contenedor.innerHTML = '<p class="mensaje-sin-examenes">No hay exámenes con este filtro.</p>';
    return;
  }

  examenes_filtrados.forEach(function (examen) {
    contenedor.insertAdjacentHTML(
      "beforeend",
      `
        <article class="fila-examen-profesor">
          <div class="fila-examen-profesor__nombre">
            <strong>${examen.nombre}</strong>
            <span>${examen.descripcion}</span>
          </div>

          <span>${examen.fecha}</span>
          <span>${examen.duracion}</span>
          <span>${examen.entregas}</span>
          <span>${examen.pendientes}</span>

          <span>
            <span class="etiqueta-examen etiqueta-examen--${examen.estadoFiltro}">
              ${examen.estado}
            </span>
          </span>

          <a class="fila-examen-profesor__accion" href="detalle_examen_profesor.php?materia=${id_asignatura}&examen=${examen.id}">
            Detalles
          </a>
        </article>
      `
    );
  });
}