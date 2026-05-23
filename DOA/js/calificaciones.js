/*
    Pantalla: Calificaciones
    Carga el resumen y el historial de calificaciones de la asignatura seleccionada.
*/

let historial_calificaciones_actual = [];

const id_asignatura = window.obtenerAsignaturaSeleccionada();
const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const calificaciones = obtener_calificaciones_asignatura(id_asignatura);

historial_calificaciones_actual = calificaciones.historial;

cargar_cabecera_asignatura(asignatura);
cargar_resumen_calificaciones(calificaciones);
preparar_filtros_calificaciones();
aplicar_filtros_calificaciones();

function cargar_cabecera_asignatura(asignatura) {
  document.title = "Calificaciones · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloCalificaciones").textContent = asignatura.nombre;
  document.getElementById("profesorAsignatura").textContent = asignatura.profesor;
  document.getElementById("unidadActualAsignatura").textContent = asignatura.unidadActualTexto;
}

function cargar_resumen_calificaciones(calificaciones) {
  document.getElementById("notaMedia").textContent = calificaciones.notaMedia;
  document.getElementById("notaMediaExamenes").textContent = calificaciones.notaMediaExamenes;
  document.getElementById("notaMediaTareas").textContent = calificaciones.notaMediaTareas;
  document.getElementById("notaMediaPracticas").textContent = calificaciones.notaMediaPracticas;
}

function preparar_filtros_calificaciones() {
  document.getElementById("filtroTipoCalificacion").addEventListener("change", aplicar_filtros_calificaciones);
  document.getElementById("filtroEstadoCalificacion").addEventListener("change", aplicar_filtros_calificaciones);
  document.getElementById("ordenCalificacion").addEventListener("change", aplicar_filtros_calificaciones);
}

function aplicar_filtros_calificaciones() {
  const tipo_seleccionado = document.getElementById("filtroTipoCalificacion").value;
  const estado_seleccionado = document.getElementById("filtroEstadoCalificacion").value;
  const orden_seleccionado = document.getElementById("ordenCalificacion").value;

  let historial_filtrado = historial_calificaciones_actual.filter(function (actividad) {
    const coincide_tipo = tipo_seleccionado === "todas" || actividad.tipoFiltro === tipo_seleccionado;
    const coincide_estado = estado_seleccionado === "todos" || actividad.estadoFiltro === estado_seleccionado;

    return coincide_tipo && coincide_estado;
  });

  historial_filtrado = ordenar_calificaciones(historial_filtrado, orden_seleccionado);

  cargar_tabla_calificaciones(historial_filtrado);
}

function ordenar_calificaciones(historial, orden_seleccionado) {
  const copia_historial = historial.slice();

  if (orden_seleccionado === "nombre") {
    copia_historial.sort(function (a, b) {
      return a.nombre.localeCompare(b.nombre);
    });
  }

  if (orden_seleccionado === "nota") {
    copia_historial.sort(function (a, b) {
      const nota_a = a.notaNumero === null ? -1 : a.notaNumero;
      const nota_b = b.notaNumero === null ? -1 : b.notaNumero;

      return nota_b - nota_a;
    });
  }

  if (orden_seleccionado === "fecha") {
    copia_historial.sort(function (a, b) {
      return b.ordenFecha - a.ordenFecha;
    });
  }

  return copia_historial;
}

function cargar_tabla_calificaciones(historial) {
  const tabla = document.getElementById("tablaCalificaciones");

  tabla.innerHTML = "";

  if (historial.length === 0) {
    const fila_vacia = document.createElement("tr");

    fila_vacia.className = "fila-sin-resultados";
    fila_vacia.innerHTML = '<td colspan="8">No hay calificaciones con estos filtros.</td>';

    tabla.appendChild(fila_vacia);
    return;
  }

  historial.forEach(function (actividad) {
    const fila = document.createElement("tr");
    const clase_nota = actividad.notaNumero !== null && actividad.notaNumero < 5 ? "nota-negativa" : "nota-positiva";

    const nota = actividad.nota === null
      ? '<span class="barra-nota-pendiente"></span>'
      : '<span class="' + clase_nota + '">' + actividad.nota + '</span>';

    const estado_clase = actividad.estadoFiltro === "proxima"
      ? "estado-calificacion estado-calificacion--pendiente"
      : "estado-calificacion";

    fila.innerHTML =
      '<td>' + actividad.nombre + '</td>' +
      '<td>' + actividad.tipo + '</td>' +
      '<td>' + actividad.unidad + '</td>' +
      '<td>' + actividad.peso + '</td>' +
      '<td>' + nota + '</td>' +
      '<td><span class="' + estado_clase + '">' + actividad.estado + '</span></td>' +
      '<td>' + actividad.fecha + '</td>' +
      '<td><button type="button" class="enlace-tabla">' + actividad.accion + '</button></td>';

    tabla.appendChild(fila);
  });
}

function obtener_calificaciones_asignatura(id_asignatura) {
    const datos = {
        matematicas: {
            notaMedia: "5,9",
            notaMediaExamenes: "3,7",
            notaMediaTareas: "8,3",
            notaMediaPracticas: "5,7",
            historial: [
                {
                    nombre: "Parcial 01",
                    tipo: "Examen",
                    tipoFiltro: "examen",
                    unidad: "Unidad 1",
                    peso: "20%",
                    nota: "4.65",
                    notaNumero: 4.65,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "Ayer",
                    ordenFecha: 4,
                    accion: "Ver detalles"
                },
                {
                    nombre: "Tarea de derivadas",
                    tipo: "Tarea",
                    tipoFiltro: "tarea",
                    unidad: "Unidad 1",
                    peso: "20%",
                    nota: "8.3",
                    notaNumero: 8.3,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "26 de oct",
                    ordenFecha: 3,
                    accion: "Ver detalles"
                },
                {
                    nombre: "Práctica de funciones",
                    tipo: "Práctica",
                    tipoFiltro: "practica",
                    unidad: "Unidad 2",
                    peso: "20%",
                    nota: "5.7",
                    notaNumero: 5.7,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "20 de oct",
                    ordenFecha: 2,
                    accion: "Ver detalles"
                },
                {
                    nombre: "Parcial 02",
                    tipo: "Examen",
                    tipoFiltro: "examen",
                    unidad: "Unidad 3",
                    peso: "20%",
                    nota: null,
                    notaNumero: null,
                    estado: "Próxima",
                    estadoFiltro: "proxima",
                    fecha: "15 de nov",
                    ordenFecha: 5,
                    accion: "Ver temario"
                }
            ]
        },

        programacion: {
            notaMedia: "7,4",
            notaMediaExamenes: "6,8",
            notaMediaTareas: "8,6",
            notaMediaPracticas: "8,1",
            historial: [
                {
                    nombre: "Práctica arrays",
                    tipo: "Práctica",
                    tipoFiltro: "practica",
                    unidad: "Unidad 1",
                    peso: "10%",
                    nota: "8.2",
                    notaNumero: 8.2,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "10 de oct",
                    ordenFecha: 1,
                    accion: "Ver detalles"
                },
                {
                    nombre: "Ejercicio recursividad",
                    tipo: "Tarea",
                    tipoFiltro: "tarea",
                    unidad: "Unidad 3",
                    peso: "15%",
                    nota: "8.7",
                    notaNumero: 8.7,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "20 de oct",
                    ordenFecha: 3,
                    accion: "Ver feedback"
                },
                {
                    nombre: "Examen parcial 1",
                    tipo: "Examen",
                    tipoFiltro: "examen",
                    unidad: "Unidad 1 y 2",
                    peso: "25%",
                    nota: "7.1",
                    notaNumero: 7.1,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "02 de nov",
                    ordenFecha: 4,
                    accion: "Ver detalles"
                },
                {
                    nombre: "Práctica grafos",
                    tipo: "Práctica",
                    tipoFiltro: "practica",
                    unidad: "Unidad 4",
                    peso: "15%",
                    nota: null,
                    notaNumero: null,
                    estado: "Próxima",
                    estadoFiltro: "proxima",
                    fecha: "18 de nov",
                    ordenFecha: 5,
                    accion: "Ver tarea"
                }
            ]
        },

        fisica: {
            notaMedia: "6,8",
            notaMediaExamenes: "6,2",
            notaMediaTareas: "7,4",
            notaMediaPracticas: "8,1",
            historial: [
                {
                    nombre: "Cuestionario fuerzas",
                    tipo: "Examen corto",
                    tipoFiltro: "examen",
                    unidad: "Unidad 1",
                    peso: "5%",
                    nota: "7.2",
                    notaNumero: 7.2,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "11 de oct",
                    ordenFecha: 1,
                    accion: "Ver detalles"
                },
                {
                    nombre: "Informe laboratorio",
                    tipo: "Práctica",
                    tipoFiltro: "practica",
                    unidad: "Unidad 2",
                    peso: "15%",
                    nota: "8.1",
                    notaNumero: 8.1,
                    estado: "Corregido",
                    estadoFiltro: "corregido",
                    fecha: "25 de oct",
                    ordenFecha: 2,
                    accion: "Ver feedback"
                },
                {
                    nombre: "Control cinemática",
                    tipo: "Examen",
                    tipoFiltro: "examen",
                    unidad: "Unidad 3",
                    peso: "20%",
                    nota: null,
                    notaNumero: null,
                    estado: "Próxima",
                    estadoFiltro: "proxima",
                    fecha: "19 de nov",
                    ordenFecha: 3,
                    accion: "Ver temario"
                }
            ]
        }
    };

    return datos[id_asignatura] || datos.matematicas;
}