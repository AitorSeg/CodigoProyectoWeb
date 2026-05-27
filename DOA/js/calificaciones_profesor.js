const datos_calificaciones_profesor = {
  programacion: {
    grupo: "Grupo A",
    totalAlumnos: "32 alumnos",
    notaMediaGrupo: "7,1",
    alumnosAprobados: "24",
    pendientesCorreccion: "14",
    alumnosSuspendidos: "5",
    alumnos: [
      {
        nombre: "Lief Simants Dredge",
        correo: "lief.simants@doa.demo",
        tareas: "8,4",
        examenes: "7,1",
        notaFinal: "7,8",
        notaFinalNumero: 7.8,
        pendientes: 0,
        estado: "Aprobado",
        estadoFiltro: "aprobado"
      },
      {
        nombre: "Ana Ferrer Torres",
        correo: "ana.ferrer@doa.demo",
        tareas: "9,1",
        examenes: "8,3",
        notaFinal: "8,7",
        notaFinalNumero: 8.7,
        pendientes: 1,
        estado: "Aprobado",
        estadoFiltro: "aprobado"
      },
      {
        nombre: "Marc Vidal Soler",
        correo: "marc.vidal@doa.demo",
        tareas: "6,2",
        examenes: "4,8",
        notaFinal: "4,3",
        notaFinalNumero: 4.3,
        pendientes: 2,
        estado: "Suspendido",
        estadoFiltro: "suspendido"
      },
      {
        nombre: "Nuria Campos Gil",
        correo: "nuria.campos@doa.demo",
        tareas: "Pendiente",
        examenes: "6,4",
        notaFinal: null,
        notaFinalNumero: null,
        pendientes: 3,
        estado: "Pendiente",
        estadoFiltro: "pendiente"
      }
    ]
  },

  matematicas: {
    grupo: "Grupo B",
    totalAlumnos: "28 alumnos",
    notaMediaGrupo: "5,9",
    alumnosAprobados: "17",
    pendientesCorreccion: "9",
    alumnosSuspendidos: "7",
    alumnos: [
      {
        nombre: "Merline Kirdsch Kampshell",
        correo: "merline.kirdsch@doa.demo",
        tareas: "8,3",
        examenes: "3,7",
        notaFinal: "5,9",
        notaFinalNumero: 5.9,
        pendientes: 1,
        estado: "Aprobado",
        estadoFiltro: "aprobado"
      },
      {
        nombre: "Pablo Riera Moll",
        correo: "pablo.riera@doa.demo",
        tareas: "7,2",
        examenes: "4,1",
        notaFinal: "4,2",
        notaFinalNumero: 4.2,
        pendientes: 0,
        estado: "Suspendido",
        estadoFiltro: "suspendido"
      },
      {
        nombre: "Nuria Fuster Grau",
        correo: "nuria.fuster@doa.demo",
        tareas: "9,0",
        examenes: "6,6",
        notaFinal: "7,5",
        notaFinalNumero: 7.5,
        pendientes: 0,
        estado: "Aprobado",
        estadoFiltro: "aprobado"
      },
      {
        nombre: "Pedro Sanchis Roig",
        correo: "pedro.sanchis@doa.demo",
        tareas: "Pendiente",
        examenes: "Pendiente",
        notaFinal: null,
        notaFinalNumero: null,
        pendientes: 4,
        estado: "Pendiente",
        estadoFiltro: "pendiente"
      }
    ]
  },

  fisica: {
    grupo: "Grupo A",
    totalAlumnos: "26 alumnos",
    notaMediaGrupo: "6,8",
    alumnosAprobados: "19",
    pendientesCorreccion: "6",
    alumnosSuspendidos: "4",
    alumnos: [
      {
        nombre: "Pedro Montoro Gil",
        correo: "pedro.montoro@doa.demo",
        tareas: "7,4",
        examenes: "6,2",
        notaFinal: "6,8",
        notaFinalNumero: 6.8,
        pendientes: 1,
        estado: "Aprobado",
        estadoFiltro: "aprobado"
      },
      {
        nombre: "Laura Segarra Ruiz",
        correo: "laura.segarra@doa.demo",
        tareas: "8,1",
        examenes: "7,0",
        notaFinal: "7,5",
        notaFinalNumero: 7.5,
        pendientes: 0,
        estado: "Aprobado",
        estadoFiltro: "aprobado"
      },
      {
        nombre: "Hugo Martí Serra",
        correo: "hugo.marti@doa.demo",
        tareas: "5,3",
        examenes: "4,2",
        notaFinal: "4,8",
        notaFinalNumero: 4.8,
        pendientes: 2,
        estado: "Suspendido",
        estadoFiltro: "suspendido"
      },
      {
        nombre: "Clara Ferrando Valls",
        correo: "clara.ferrando@doa.demo",
        tareas: "Pendiente",
        examenes: "5,9",
        notaFinal: null,
        notaFinalNumero: null,
        pendientes: 3,
        estado: "Pendiente",
        estadoFiltro: "pendiente"
      }
    ]
  }
};

const parametros = new URLSearchParams(window.location.search);
const id_asignatura = parametros.get("materia") || window.obtenerAsignaturaSeleccionada();
const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const calificaciones = datos_calificaciones_profesor[id_asignatura];

window.guardarAsignaturaSeleccionada(id_asignatura);

cargar_cabecera_calificaciones(asignatura, calificaciones);
cargar_resumen_calificaciones(calificaciones);
actualizar_enlaces_calificaciones(id_asignatura);
preparar_filtros_calificaciones();
aplicar_filtros_calificaciones();

function cargar_cabecera_calificaciones(asignatura, calificaciones) {
  document.title = "Calificaciones · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloAsignatura").textContent = asignatura.nombre;
  document.getElementById("grupoAsignatura").textContent = calificaciones.grupo;
  document.getElementById("totalAlumnosAsignatura").textContent = calificaciones.totalAlumnos;
  document.getElementById("unidadActualTextoAsignatura").textContent = asignatura.unidadActualTexto;
}

function cargar_resumen_calificaciones(calificaciones) {
  document.getElementById("notaMediaGrupo").textContent = calificaciones.notaMediaGrupo;
  document.getElementById("alumnosAprobados").textContent = calificaciones.alumnosAprobados;
  document.getElementById("pendientesCorreccion").textContent = calificaciones.pendientesCorreccion;
  document.getElementById("alumnosSuspendidos").textContent = calificaciones.alumnosSuspendidos;
}

function actualizar_enlaces_calificaciones(id_asignatura) {
  const parametro_materia = "?materia=" + id_asignatura;

  document.getElementById("linkPestanaRecursos").href = "recursos_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaTareas").href = "listado_tareas_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaExamenes").href = "examenes_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaCalificaciones").href = "calificaciones_profesor.php" + parametro_materia;
}

function preparar_filtros_calificaciones() {
  document.getElementById("filtroEstadoCalificacion").addEventListener("change", aplicar_filtros_calificaciones);
  document.getElementById("ordenCalificacion").addEventListener("change", aplicar_filtros_calificaciones);
}

function aplicar_filtros_calificaciones() {
  const estado_seleccionado = document.getElementById("filtroEstadoCalificacion").value;
  const orden_seleccionado = document.getElementById("ordenCalificacion").value;

  let alumnos_filtrados = calificaciones.alumnos.filter(function (alumno) {
    return estado_seleccionado === "todos" || alumno.estadoFiltro === estado_seleccionado;
  });

  alumnos_filtrados = ordenar_alumnos(alumnos_filtrados, orden_seleccionado);

  renderizar_tabla_calificaciones(alumnos_filtrados);
}

function ordenar_alumnos(alumnos, orden_seleccionado) {
  const copia_alumnos = alumnos.slice();

  if (orden_seleccionado === "nombre") {
    copia_alumnos.sort(function (a, b) {
      return a.nombre.localeCompare(b.nombre);
    });
  }

  if (orden_seleccionado === "nota") {
    copia_alumnos.sort(function (a, b) {
      const nota_a = a.notaFinalNumero === null ? -1 : a.notaFinalNumero;
      const nota_b = b.notaFinalNumero === null ? -1 : b.notaFinalNumero;

      return nota_b - nota_a;
    });
  }

  if (orden_seleccionado === "pendientes") {
    copia_alumnos.sort(function (a, b) {
      return b.pendientes - a.pendientes;
    });
  }

  return copia_alumnos;
}

function renderizar_tabla_calificaciones(alumnos) {
  const tabla = document.getElementById("tablaCalificacionesProfesor");

  tabla.innerHTML = "";

  if (alumnos.length === 0) {
    const fila_vacia = document.createElement("tr");

    fila_vacia.className = "fila-sin-resultados";
    fila_vacia.innerHTML = '<td colspan="6">No hay alumnos con este filtro.</td>';

    tabla.appendChild(fila_vacia);
    return;
  }

  alumnos.forEach(function (alumno) {
    const fila = document.createElement("tr");
    const clase_nota = alumno.notaFinalNumero !== null && alumno.notaFinalNumero < 5 ? "nota-negativa" : "nota-positiva";

    const nota_final = alumno.notaFinal === null
      ? '<span class="barra-nota-pendiente"></span>'
      : '<span class="' + clase_nota + '">' + alumno.notaFinal + '</span>';

    fila.innerHTML =
      '<td>' +
        '<div class="alumno-calificacion">' +
          '<strong>' + alumno.nombre + '</strong>' +
          '<small>' + alumno.correo + '</small>' +
        '</div>' +
      '</td>' +
      '<td>' + alumno.tareas + '</td>' +
      '<td>' + alumno.examenes + '</td>' +
      '<td>' + nota_final + '</td>' +
      '<td>' + alumno.pendientes + '</td>' +
      '<td>' +
        '<span class="estado-calificacion estado-calificacion--' + alumno.estadoFiltro + '">' +
          alumno.estado +
        '</span>' +
      '</td>';

    tabla.appendChild(fila);
  });
}