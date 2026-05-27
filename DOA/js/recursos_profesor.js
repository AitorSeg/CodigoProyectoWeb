const datos_recursos_docente = {
  programacion: {
    grupo: "Grupo A",
    alumnos: "32 alumnos",
    archivos: {
      "UNIDAD 01": [
        {
          nombre: "Introducción a la asignatura",
          tipo: "PDF",
          tamano: "1.2 MB",
          etiqueta: "Importante",
          claseEtiqueta: "etiqueta-importante",
          fecha: "10/01/2026"
        }
      ],

      "UNIDAD 02": [
        {
          nombre: "Ejercicios de estructuras",
          tipo: "PDF",
          tamano: "2.1 MB",
          etiqueta: "Práctica",
          claseEtiqueta: "etiqueta-importante",
          fecha: "02/02/2026"
        }
      ],

      "UNIDAD 03": [
        {
          nombre: "Recursividad",
          tipo: "PDF",
          tamano: "12.5 MB",
          etiqueta: "Unidad actual",
          claseEtiqueta: "etiqueta-actual",
          fecha: "19/03/2026"
        },
        {
          nombre: "Ejercicios 1",
          tipo: "PDF",
          tamano: "2.6 MB",
          etiqueta: "Práctica",
          claseEtiqueta: "etiqueta-importante",
          fecha: "26/08/2025"
        }
      ],

      "PRÁCTICAS": [
        {
          nombre: "Práctica 1",
          tipo: "ZIP",
          tamano: "5.1 MB",
          etiqueta: "Práctica",
          claseEtiqueta: "etiqueta-importante",
          fecha: "05/02/2026"
        }
      ],

      "EXÁMENES": [
        {
          nombre: "Guía del parcial",
          tipo: "PDF",
          tamano: "1.8 MB",
          etiqueta: "Importante",
          claseEtiqueta: "etiqueta-importante",
          fecha: "12/03/2026"
        }
      ]
    }
  },

  matematicas: {
    grupo: "Grupo B",
    alumnos: "28 alumnos",
    archivos: {
      "UNIDAD 01": [
        {
          nombre: "Álgebra básica",
          tipo: "PDF",
          tamano: "3.2 MB",
          etiqueta: "Importante",
          claseEtiqueta: "etiqueta-importante",
          fecha: "12/01/2026"
        }
      ],

      "UNIDAD 02": [],

      "UNIDAD 03": [
        {
          nombre: "Guía numérica",
          tipo: "PDF",
          tamano: "4.5 MB",
          etiqueta: "Unidad actual",
          claseEtiqueta: "etiqueta-actual",
          fecha: "20/03/2026"
        }
      ],

      "PRÁCTICAS": [],

      "EXÁMENES": [
        {
          nombre: "Temario Parcial 01",
          tipo: "PDF",
          tamano: "2.0 MB",
          etiqueta: "Importante",
          claseEtiqueta: "etiqueta-importante",
          fecha: "08/03/2026"
        }
      ]
    }
  },

  fisica: {
    grupo: "Grupo A",
    alumnos: "26 alumnos",
    archivos: {
      "UNIDAD 01": [],

      "UNIDAD 02": [],

      "UNIDAD 03": [
        {
          nombre: "Laboratorio",
          tipo: "PDF",
          tamano: "8.1 MB",
          etiqueta: "Unidad actual",
          claseEtiqueta: "etiqueta-actual",
          fecha: "25/03/2026"
        }
      ],

      "PRÁCTICAS": [
        {
          nombre: "Informe de laboratorio",
          tipo: "PDF",
          tamano: "1.9 MB",
          etiqueta: "Práctica",
          claseEtiqueta: "etiqueta-importante",
          fecha: "18/03/2026"
        }
      ],

      "EXÁMENES": []
    }
  }
};

let unidad_actual = "UNIDAD 03";

const parametros = new URLSearchParams(window.location.search);
const id_asignatura = parametros.get("materia") || window.obtenerAsignaturaSeleccionada();
const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const datos_recursos = datos_recursos_docente[id_asignatura];

window.guardarAsignaturaSeleccionada(id_asignatura);

cargar_cabecera_recursos(asignatura, datos_recursos);
actualizar_enlaces_recursos(id_asignatura);
preparar_navegacion_biblioteca();
preparar_filtros_recursos();
preparar_subida_recurso();
preparar_controles_movil();
renderizar_tabla_recursos();

function cargar_cabecera_recursos(asignatura, datos_recursos) {
  document.title = "Recursos · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloAsignatura").textContent = asignatura.nombre;
  document.getElementById("grupoAsignatura").textContent = datos_recursos.grupo;
  document.getElementById("totalAlumnosAsignatura").textContent = datos_recursos.alumnos;
  document.getElementById("unidadActualTextoAsignatura").textContent = asignatura.unidadActualTexto;
}

function actualizar_enlaces_recursos(id_asignatura) {
  const parametro_materia = "?materia=" + id_asignatura;
  const parametro_asignatura = "?asignatura=" + id_asignatura;

  document.getElementById("linkVolverDetalle").href = "detalle_asignatura_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaRecursos").href = "recursos_profesor.php" + parametro_materia;
  document.getElementById("linkPestanaTareas").href = "listado_tareas_profe.html" + parametro_asignatura;
  document.getElementById("linkPestanaExamenes").href = "examenes_profesor.php" + parametro_asignatura;
  document.getElementById("linkPestanaCalificaciones").href = "calificaciones_profesor.php" + parametro_materia;
}

function preparar_navegacion_biblioteca() {
  const enlaces_carpetas = document.querySelectorAll(".carpeta-click");

  enlaces_carpetas.forEach(function (enlace) {
    enlace.addEventListener("click", function (evento) {
      evento.preventDefault();

      enlaces_carpetas.forEach(function (item) {
        item.classList.remove("activo");
      });

      enlace.classList.add("activo");
      unidad_actual = enlace.dataset.unidad;

      actualizar_breadcrumb();
      cerrar_paneles_movil();
      renderizar_tabla_recursos();
    });
  });
}

function actualizar_breadcrumb() {
  const es_unidad = unidad_actual.includes("UNIDAD");

  document.getElementById("textoCarpetaPadre").hidden = !es_unidad;
  document.getElementById("flechaBreadcrumb").hidden = !es_unidad;
  document.getElementById("textoBreadcrumb").textContent = unidad_actual;
}

function preparar_filtros_recursos() {
  document.getElementById("filtroTipo").addEventListener("change", renderizar_tabla_recursos);
  document.getElementById("filtroEtiqueta").addEventListener("change", renderizar_tabla_recursos);
}

function preparar_controles_movil() {
  document.getElementById("btnNavegacionMovil").addEventListener("click", function () {
    document.getElementById("menuCarpetasGeneral").classList.toggle("mostrar-movil");
    document.getElementById("contenedorFiltros").classList.remove("mostrar-movil");
  });

  document.getElementById("btnFiltrarMovil").addEventListener("click", function () {
    document.getElementById("contenedorFiltros").classList.toggle("mostrar-movil");
    document.getElementById("menuCarpetasGeneral").classList.remove("mostrar-movil");
  });
}

function cerrar_paneles_movil() {
  document.getElementById("menuCarpetasGeneral").classList.remove("mostrar-movil");
  document.getElementById("contenedorFiltros").classList.remove("mostrar-movil");
}

function renderizar_tabla_recursos() {
  const cuerpo_tabla = document.getElementById("cuerpoTablaArchivos");
  const tipo_seleccionado = document.getElementById("filtroTipo").value;
  const etiqueta_seleccionada = document.getElementById("filtroEtiqueta").value;
  const archivos = datos_recursos.archivos[unidad_actual] || [];

  const archivos_filtrados = archivos.filter(function (archivo) {
    const coincide_tipo = tipo_seleccionado === "TODOS" || archivo.tipo === tipo_seleccionado;
    const coincide_etiqueta = etiqueta_seleccionada === "TODAS" || archivo.etiqueta === etiqueta_seleccionada;

    return coincide_tipo && coincide_etiqueta;
  });

  cuerpo_tabla.innerHTML = "";

  if (archivos_filtrados.length === 0) {
    cuerpo_tabla.innerHTML = '<p class="mensaje-tabla-vacia">No hay recursos que coincidan con los filtros seleccionados.</p>';
    return;
  }

  archivos_filtrados.forEach(function (archivo) {
    const etiqueta = archivo.etiqueta
      ? '<span class="badge-etiqueta ' + archivo.claseEtiqueta + '">' + archivo.etiqueta + '</span>'
      : "";

    cuerpo_tabla.insertAdjacentHTML(
      "beforeend",
      `
        <div class="biblioteca-tabla-fila archivo-fila">
          <a href="#" class="nombre-archivo">${archivo.nombre}</a>
          <span class="col-tipo">${archivo.tipo}</span>
          <span class="col-movil-oculta">${archivo.tamano}</span>
          <span class="col-movil-oculta">${etiqueta}</span>
          <span class="col-movil-oculta">${archivo.fecha}</span>
        </div>
      `
    );
  });
}

function preparar_subida_recurso() {
  document.getElementById("formSubirRecurso").addEventListener("submit", function (evento) {
    evento.preventDefault();

    const titulo = document.getElementById("tituloRecurso").value.trim();
    const archivo = document.getElementById("archivoRecurso").files[0];
    const extension = archivo.name.split(".").pop().toUpperCase();

    if (datos_recursos.archivos[unidad_actual] === undefined) {
      datos_recursos.archivos[unidad_actual] = [];
    }

    datos_recursos.archivos[unidad_actual].unshift({
      nombre: titulo,
      tipo: extension,
      tamano: (archivo.size / 1048576).toFixed(1) + " MB",
      etiqueta: "Nuevo",
      claseEtiqueta: "etiqueta-success",
      fecha: new Date().toLocaleDateString("es-ES")
    });

    document.getElementById("formSubirRecurso").reset();

    renderizar_tabla_recursos();
    mostrar_mensaje_recurso_subido();
  });
}

function mostrar_mensaje_recurso_subido() {
  const mensaje = document.getElementById("mensajeRecursoSubido");

  mensaje.classList.remove("mensaje-recurso-subido--oculto");

  setTimeout(function () {
    mensaje.classList.add("mensaje-recurso-subido--oculto");
  }, 3000);
}