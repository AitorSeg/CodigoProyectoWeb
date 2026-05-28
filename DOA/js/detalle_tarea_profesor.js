const datos_detalle_tareas_profesor = {
  programacion: {
    grupo: "Grupo A",
    totalAlumnos: "32 alumnos",
    tareas: {
      programacion_tarea_api: {
        titulo: "Tarea 1: Desarrollo de APIs",
        descripcion: "Implementación de una pequeña API siguiendo los criterios explicados en clase.",
        fechaEntrega: "19 Abr, 2026",
        entregas: "32/32",
        pendientes: "0",
        estado: "Cerrada",
        estadoFiltro: "cerrada",
        recursos: ["Guía de APIs", "Rúbrica de la tarea"],
        entregasAlumnos: [
          {
            alumno: "Pedro Fernández",
            correo: "pedro.fernandez@doa.demo",
            estado: "Entregada",
            estadoFiltro: "entregada",
            fecha: "19 Abr, 2026",
            nota: "9,0",
            notaNumero: 9
          },
          {
            alumno: "Pablo Barceló",
            correo: "pablo.barcelo@doa.demo",
            estado: "Entregada",
            estadoFiltro: "entregada",
            fecha: "19 Abr, 2026",
            nota: "8,7",
            notaNumero: 8.7
          },
          {
            alumno: "Juan Diego",
            correo: "juan.diego@doa.demo",
            estado: "Tardía",
            estadoFiltro: "tardia",
            fecha: "20 Abr, 2026",
            nota: "/",
            notaNumero: null
          }
        ]
      },

      programacion_practica_web: {
        titulo: "Práctica: Cooperación de webs",
        descripcion: "Práctica breve sobre integración entre páginas y flujo de navegación.",
        fechaEntrega: "24 Abr, 2026",
        entregas: "14/32",
        pendientes: "18",
        estado: "Publicada",
        estadoFiltro: "publicada",
        recursos: ["Enunciado de la práctica", "Ejemplo de estructura"],
        entregasAlumnos: [
          {
            alumno: "Ana Ferrer Torres",
            correo: "ana.ferrer@doa.demo",
            estado: "Entregada",
            estadoFiltro: "entregada",
            fecha: "23 Abr, 2026",
            nota: "/",
            notaNumero: null
          },
          {
            alumno: "Marc Vidal Soler",
            correo: "marc.vidal@doa.demo",
            estado: "Pendiente",
            estadoFiltro: "pendiente",
            fecha: "-",
            nota: "/",
            notaNumero: null
          },
          {
            alumno: "Lief Simants Dredge",
            correo: "lief.simants@doa.demo",
            estado: "Entregada",
            estadoFiltro: "entregada",
            fecha: "24 Abr, 2026",
            nota: "/",
            notaNumero: null
          }
        ]
      },

      programacion_tarea_mapas: {
        titulo: "Tarea 2: Seguimiento de mapas",
        descripcion: "Documentación del flujo de navegación mediante un pequeño esquema.",
        fechaEntrega: "02 May, 2026",
        entregas: "0/32",
        pendientes: "32",
        estado: "Borrador",
        estadoFiltro: "borrador",
        recursos: ["Plantilla de entrega"],
        entregasAlumnos: [
          {
            alumno: "Ana Ferrer Torres",
            correo: "ana.ferrer@doa.demo",
            estado: "Pendiente",
            estadoFiltro: "pendiente",
            fecha: "-",
            nota: "/",
            notaNumero: null
          }
        ]
      }
    }
  },

  matematicas: {
    grupo: "Grupo B",
    totalAlumnos: "28 alumnos",
    tareas: {
      matematicas_limites: {
        titulo: "Ejercicio de límites",
        descripcion: "Ejercicios básicos de límites con justificación.",
        fechaEntrega: "15 Nov, 2026",
        entregas: "21/28",
        pendientes: "7",
        estado: "Publicada",
        estadoFiltro: "publicada",
        recursos: ["Hoja de ejercicios"],
        entregasAlumnos: [
          {
            alumno: "Pablo Riera Moll",
            correo: "pablo.riera@doa.demo",
            estado: "Entregada",
            estadoFiltro: "entregada",
            fecha: "15 Nov, 2026",
            nota: "/",
            notaNumero: null
          },
          {
            alumno: "Nuria Fuster Grau",
            correo: "nuria.fuster@doa.demo",
            estado: "Pendiente",
            estadoFiltro: "pendiente",
            fecha: "-",
            nota: "/",
            notaNumero: null
          }
        ]
      }
    }
  },

  fisica: {
    grupo: "Grupo A",
    totalAlumnos: "26 alumnos",
    tareas: {
      fisica_cinematica: {
        titulo: "Ejercicio de cinemática",
        descripcion: "Problemas sencillos de movimiento rectilíneo.",
        fechaEntrega: "22 Nov, 2026",
        entregas: "19/26",
        pendientes: "7",
        estado: "Publicada",
        estadoFiltro: "publicada",
        recursos: ["Tabla de fórmulas"],
        entregasAlumnos: [
          {
            alumno: "Pedro Montoro Gil",
            correo: "pedro.montoro@doa.demo",
            estado: "Entregada",
            estadoFiltro: "entregada",
            fecha: "21 Nov, 2026",
            nota: "/",
            notaNumero: null
          },
          {
            alumno: "Clara Ferrando Valls",
            correo: "clara.ferrando@doa.demo",
            estado: "Pendiente",
            estadoFiltro: "pendiente",
            fecha: "-",
            nota: "/",
            notaNumero: null
          }
        ]
      }
    }
  }
};

const parametros = new URLSearchParams(window.location.search);
const id_asignatura = parametros.get("materia") || window.obtenerAsignaturaSeleccionada();
const datos_asignatura = datos_detalle_tareas_profesor[id_asignatura];
const id_tarea = parametros.get("tarea") || Object.keys(datos_asignatura.tareas)[0];

const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const tarea = datos_asignatura.tareas[id_tarea];

window.guardarAsignaturaSeleccionada(id_asignatura);

cargar_cabecera_tarea_profesor();
cargar_detalle_tarea_profesor();
actualizar_enlaces_tarea_profesor();
preparar_filtros_entregas_profesor();
renderizar_recursos_tarea_profesor();
renderizar_entregas_profesor();

function cargar_cabecera_tarea_profesor() {
  document.title = tarea.titulo + " · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloAsignatura").textContent = asignatura.nombre;
  document.getElementById("grupoAsignatura").textContent = datos_asignatura.grupo;
  document.getElementById("totalAlumnosAsignatura").textContent = datos_asignatura.totalAlumnos;
  document.getElementById("unidadActualTextoAsignatura").textContent = asignatura.unidadActualTexto;
}

function cargar_detalle_tarea_profesor() {
  document.getElementById("estadoTareaProfesor").textContent = tarea.estado;
  document.getElementById("estadoTareaProfesor").className =
    "etiqueta-tarea-profesor etiqueta-tarea-profesor--" + tarea.estadoFiltro;

  document.getElementById("tituloTareaProfesor").textContent = tarea.titulo;
  document.getElementById("descripcionTareaProfesor").textContent = tarea.descripcion;
  document.getElementById("entregasTareaProfesor").textContent = tarea.entregas;
  document.getElementById("pendientesTareaProfesor").textContent = tarea.pendientes;
  document.getElementById("fechaEntregaTareaProfesor").textContent = tarea.fechaEntrega;
  document.getElementById("estadoResumenTareaProfesor").textContent = tarea.estado;
}

function actualizar_enlaces_tarea_profesor() {
  const parametro_materia = "?materia=" + id_asignatura;
  const parametro_tarea = "?materia=" + id_asignatura + "&tarea=" + id_tarea;

  document.getElementById("linkVolverTareas").href =
    "listado_tareas_profesor.php" + parametro_materia;

  document.getElementById("linkPestanaRecursos").href =
    "recursos_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaTareas").href =
    "listado_tareas_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaExamenes").href =
    "examenes_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaCalificaciones").href =
    "calificaciones_profesor.php" + parametro_materia;

  document.getElementById("linkEditarTarea").href =
    "crear_tarea.php" + parametro_tarea;
  document.getElementById("linkCalificacionesTarea").href =
    "calificaciones_profesor.php" + parametro_materia;
}

function preparar_filtros_entregas_profesor() {
  document.getElementById("filtroEstadoEntrega").addEventListener("change", renderizar_entregas_profesor);
  document.getElementById("ordenEntregas").addEventListener("change", renderizar_entregas_profesor);
}

function renderizar_recursos_tarea_profesor() {
  const lista = document.getElementById("listaRecursosTareaProfesor");

  lista.innerHTML = "";

  tarea.recursos.forEach(function (recurso) {
    lista.insertAdjacentHTML(
      "beforeend",
      `
        <li>
          <span>
            <img alt="" src="img/iconos/grey-file.svg">
            ${recurso}
          </span>

          <img alt="" src="img/iconos/grey-download.svg">
        </li>
      `
    );
  });
}

function renderizar_entregas_profesor() {
  const estado_seleccionado = document.getElementById("filtroEstadoEntrega").value;
  const orden_seleccionado = document.getElementById("ordenEntregas").value;

  let entregas_filtradas = tarea.entregasAlumnos.filter(function (entrega) {
    return estado_seleccionado === "todos" || entrega.estadoFiltro === estado_seleccionado;
  });

  entregas_filtradas = ordenar_entregas_profesor(entregas_filtradas, orden_seleccionado);

  cargar_tabla_entregas_profesor(entregas_filtradas);
}

function ordenar_entregas_profesor(entregas, orden_seleccionado) {
  const copia_entregas = entregas.slice();

  if (orden_seleccionado === "nombre") {
    copia_entregas.sort(function (a, b) {
      return a.alumno.localeCompare(b.alumno);
    });
  }

  if (orden_seleccionado === "nota") {
    copia_entregas.sort(function (a, b) {
      const nota_a = a.notaNumero === null ? -1 : a.notaNumero;
      const nota_b = b.notaNumero === null ? -1 : b.notaNumero;

      return nota_b - nota_a;
    });
  }

  if (orden_seleccionado === "fecha") {
    copia_entregas.sort(function (a, b) {
      return a.fecha.localeCompare(b.fecha);
    });
  }

  return copia_entregas;
}

function cargar_tabla_entregas_profesor(entregas) {
  const cuerpo = document.getElementById("cuerpoEntregasProfesor");

  cuerpo.innerHTML = "";

  if (entregas.length === 0) {
    cuerpo.innerHTML = '<p class="mensaje-tabla-vacia">No hay entregas con este filtro.</p>';
    return;
  }

  entregas.forEach(function (entrega) {
    const accion = entrega.estadoFiltro === "pendiente"
      ? '<span class="accion-entrega-deshabilitada">Sin entrega</span>'
      : '<a class="boton-ver-entrega" href="' + crear_url_entrega(entrega.alumno) + '">Revisar</a>';

    cuerpo.insertAdjacentHTML(
      "beforeend",
      `
        <article class="fila-entrega-profesor">
          <div class="alumno-entrega-profesor">
            <strong>${entrega.alumno}</strong>
            <small>${entrega.correo}</small>
          </div>

          <p>
            <span class="etiqueta-entrega etiqueta-entrega--${entrega.estadoFiltro}">
              ${entrega.estado}
            </span>
          </p>

          <p>${entrega.fecha}</p>
          <p><strong>${entrega.nota}</strong></p>
          <p>${accion}</p>
        </article>
      `
    );
  });
}

function crear_url_entrega(alumno) {
  const alumno_url = alumno
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "_")
    .replace(/^_|_$/g, "");

  return "detalle_tarea_entregada.php?materia=" + id_asignatura + "&tarea=" + id_tarea + "&alumno=" + alumno_url;
}