const datos_entregas_profesor = {
  programacion: {
    grupo: "Grupo A",
    totalAlumnos: "32 alumnos",
    tareas: {
      programacion_tarea_api: {
        titulo: "Tarea 1: Desarrollo de APIs",
        descripcion: "Implementación de una pequeña API siguiendo los criterios explicados en clase.",
        entregas: {
          pedro_fernandez: {
            alumno: "Pedro Fernández",
            correo: "pedro.fernandez@doa.demo",
            fecha: "19 Abr, 2026",
            estado: "Entregada",
            estadoFiltro: "entregada",
            nota: "9,0",
            archivos: ["api_pedro.zip", "memoria_api.pdf"],
            comentarioAlumno: "Adjunto la API y una pequeña memoria con capturas.",
            comentarioProfesor: "Buen trabajo. La estructura está clara y la explicación es suficiente."
          },
          pablo_barcelo: {
            alumno: "Pablo Barceló",
            correo: "pablo.barcelo@doa.demo",
            fecha: "19 Abr, 2026",
            estado: "Entregada",
            estadoFiltro: "entregada",
            nota: "8,7",
            archivos: ["api_pablo.zip"],
            comentarioAlumno: "Entrega de la práctica.",
            comentarioProfesor: "Correcto. Faltaría explicar mejor algunos endpoints."
          },
          juan_diego: {
            alumno: "Juan Diego",
            correo: "juan.diego@doa.demo",
            fecha: "20 Abr, 2026",
            estado: "Tardía",
            estadoFiltro: "tardia",
            nota: "",
            archivos: ["api_juan.zip"],
            comentarioAlumno: "Entrega fuera de plazo.",
            comentarioProfesor: ""
          }
        }
      },

      programacion_practica_web: {
        titulo: "Práctica: Cooperación de webs",
        descripcion: "Práctica breve sobre integración entre páginas y flujo de navegación.",
        entregas: {
          ana_ferrer_torres: {
            alumno: "Ana Ferrer Torres",
            correo: "ana.ferrer@doa.demo",
            fecha: "23 Abr, 2026",
            estado: "Entregada",
            estadoFiltro: "entregada",
            nota: "",
            archivos: ["practica_web_ana.pdf"],
            comentarioAlumno: "He incluido el flujo de navegación principal.",
            comentarioProfesor: ""
          },
          lief_simants_dredge: {
            alumno: "Lief Simants Dredge",
            correo: "lief.simants@doa.demo",
            fecha: "24 Abr, 2026",
            estado: "Entregada",
            estadoFiltro: "entregada",
            nota: "",
            archivos: ["cooperacion_web_lief.zip"],
            comentarioAlumno: "Entrega comprimida con los archivos principales.",
            comentarioProfesor: ""
          }
        }
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
        entregas: {
          pablo_riera_moll: {
            alumno: "Pablo Riera Moll",
            correo: "pablo.riera@doa.demo",
            fecha: "15 Nov, 2026",
            estado: "Entregada",
            estadoFiltro: "entregada",
            nota: "",
            archivos: ["limites_pablo.pdf"],
            comentarioAlumno: "Ejercicios resueltos.",
            comentarioProfesor: ""
          }
        }
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
        entregas: {
          pedro_montoro_gil: {
            alumno: "Pedro Montoro Gil",
            correo: "pedro.montoro@doa.demo",
            fecha: "21 Nov, 2026",
            estado: "Entregada",
            estadoFiltro: "entregada",
            nota: "",
            archivos: ["cinematica_pedro.pdf"],
            comentarioAlumno: "Entrega del ejercicio.",
            comentarioProfesor: ""
          }
        }
      }
    }
  }
};

const parametros = new URLSearchParams(window.location.search);
const id_asignatura = parametros.get("materia") || window.obtenerAsignaturaSeleccionada();
const datos_asignatura = datos_entregas_profesor[id_asignatura];
const id_tarea = parametros.get("tarea") || Object.keys(datos_asignatura.tareas)[0];
const tarea = datos_asignatura.tareas[id_tarea];
const id_alumno = parametros.get("alumno") || Object.keys(tarea.entregas)[0];
const entrega = tarea.entregas[id_alumno];
const asignatura = window.DOA_ASIGNATURAS[id_asignatura];

window.guardarAsignaturaSeleccionada(id_asignatura);

cargar_cabecera_entrega();
cargar_detalle_entrega();
actualizar_enlaces_entrega();
preparar_formulario_calificacion();

function cargar_cabecera_entrega() {
  document.title = entrega.alumno + " · " + tarea.titulo + " | DOA";

  document.getElementById("tituloAsignatura").textContent = asignatura.nombre;
  document.getElementById("grupoAsignatura").textContent = datos_asignatura.grupo;
  document.getElementById("totalAlumnosAsignatura").textContent = datos_asignatura.totalAlumnos;
  document.getElementById("unidadActualTextoAsignatura").textContent = asignatura.unidadActualTexto;
}

function cargar_detalle_entrega() {
  document.getElementById("estadoEntregaAlumno").textContent = entrega.estado;
  document.getElementById("estadoEntregaAlumno").className =
    "etiqueta-entrega-profesor etiqueta-entrega-profesor--" + entrega.estadoFiltro;

  document.getElementById("tituloTareaEntrega").textContent = tarea.titulo;
  document.getElementById("descripcionTareaEntrega").textContent = tarea.descripcion;

  document.getElementById("nombreAlumnoEntrega").textContent = entrega.alumno;
  document.getElementById("fechaEntregaAlumno").textContent = entrega.fecha;
  document.getElementById("estadoResumenEntrega").textContent = entrega.estado;
  document.getElementById("notaResumenEntrega").textContent = entrega.nota === "" ? "Pendiente" : entrega.nota;

  document.getElementById("comentarioAlumnoEntrega").textContent = entrega.comentarioAlumno;
  document.getElementById("correoAlumnoEntrega").textContent = entrega.correo;
  document.getElementById("nombreTareaLateral").textContent = tarea.titulo;
  document.getElementById("nombreAsignaturaLateral").textContent = asignatura.nombre;

  document.getElementById("inputNotaEntrega").value = entrega.nota.replace(",", ".");
  document.getElementById("inputComentarioProfesor").value = entrega.comentarioProfesor;

  renderizar_archivos_entrega();
}

function renderizar_archivos_entrega() {
  const lista = document.getElementById("listaArchivosEntregaProfesor");

  lista.innerHTML = "";

  entrega.archivos.forEach(function (archivo) {
    lista.insertAdjacentHTML(
      "beforeend",
      `
        <li>
          <a href="#">
            <span>
              <img alt="" src="img/iconos/grey-file.svg">
              ${archivo}
            </span>

            <img alt="" src="img/iconos/grey-download.svg">
          </a>
        </li>
      `
    );
  });
}

function actualizar_enlaces_entrega() {
  const parametro_materia = "?materia=" + id_asignatura;
  const parametro_tarea = "?materia=" + id_asignatura + "&tarea=" + id_tarea;

  document.getElementById("linkVolverTareaProfesor").href =
    "detalle_tarea_profesor.php" + parametro_tarea;

  document.getElementById("linkPestanaRecursos").href =
    "recursos_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaTareas").href =
    "listado_tareas_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaExamenes").href =
    "examenes_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaCalificaciones").href =
    "calificaciones_profesor.php" + parametro_materia;

  document.getElementById("linkCancelarCalificacion").href =
    "detalle_tarea_profesor.php" + parametro_tarea;
}

function preparar_formulario_calificacion() {
  document.getElementById("formCalificacionEntrega").addEventListener("submit", guardar_calificacion_entrega);
}

function guardar_calificacion_entrega(evento) {
  evento.preventDefault();

  const nota = document.getElementById("inputNotaEntrega").value.replace(".", ",");
  const comentario = document.getElementById("inputComentarioProfesor").value;

  const calificaciones_guardadas = obtener_calificaciones_guardadas();

  calificaciones_guardadas[crear_clave_entrega()] = {
    materia: id_asignatura,
    tarea: id_tarea,
    alumno: id_alumno,
    nota: nota,
    comentario: comentario
  };

  localStorage.setItem("doaCalificacionesEntregas", JSON.stringify(calificaciones_guardadas));

  document.getElementById("notaResumenEntrega").textContent = nota;
  mostrar_mensaje_calificacion();
}

function obtener_calificaciones_guardadas() {
  return JSON.parse(localStorage.getItem("doaCalificacionesEntregas") || "{}");
}

function crear_clave_entrega() {
  return id_asignatura + "_" + id_tarea + "_" + id_alumno;
}

function mostrar_mensaje_calificacion() {
  const mensaje = document.getElementById("mensajeCalificacion");

  mensaje.classList.remove("mensaje-calificacion--oculto");

  setTimeout(function () {
    mensaje.classList.add("mensaje-calificacion--oculto");
  }, 3000);
}