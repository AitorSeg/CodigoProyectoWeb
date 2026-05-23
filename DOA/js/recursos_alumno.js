/*
    Pantalla: Recursos del alumno
    Carga los recursos de la asignatura seleccionada y permite filtrar la biblioteca.
*/

const recursos_asignaturas = {
  programacion: {
    archivos: {
      "UNIDAD 01": [
        {
          nombre: "Intro",
          tipo: "PDF",
          tamano: "1.2 MB",
          etiqueta: "Teoría",
          clase_etiqueta: "etiqueta-neutral",
          fecha: "10/01/2026"
        }
      ],
      "UNIDAD 03": [
        {
          nombre: "Recursividad",
          tipo: "PDF",
          tamano: "12.5 MB",
          etiqueta: "Unidad actual",
          clase_etiqueta: "etiqueta-actual",
          fecha: "19/03/2026"
        },
        {
          nombre: "Ejercicios 1",
          tipo: "PDF",
          tamano: "2.6 MB",
          etiqueta: "Práctica",
          clase_etiqueta: "etiqueta-importante",
          fecha: "26/08/2025"
        }
      ],
      "PRÁCTICAS": [
        {
          nombre: "Práctica 1",
          tipo: "ZIP",
          tamano: "5.1 MB",
          etiqueta: "Práctica",
          clase_etiqueta: "etiqueta-importante",
          fecha: "05/02/2026"
        }
      ],
      "EXÁMENES": []
    }
  },

  matematicas: {
    archivos: {
      "UNIDAD 01": [
        {
          nombre: "Álgebra",
          tipo: "PDF",
          tamano: "3.2 MB",
          etiqueta: "Teoría",
          clase_etiqueta: "etiqueta-neutral",
          fecha: "12/01/2026"
        }
      ],
      "UNIDAD 03": [
        {
          nombre: "Guía numérica",
          tipo: "PDF",
          tamano: "4.5 MB",
          etiqueta: "Unidad actual",
          clase_etiqueta: "etiqueta-actual",
          fecha: "20/03/2026"
        }
      ],
      "PRÁCTICAS": [],
      "EXÁMENES": []
    }
  },

  fisica: {
    archivos: {
      "UNIDAD 03": [
        {
          nombre: "Laboratorio",
          tipo: "PDF",
          tamano: "8.1 MB",
          etiqueta: "Unidad actual",
          clase_etiqueta: "etiqueta-actual",
          fecha: "25/03/2026"
        }
      ],
      "PRÁCTICAS": [],
      "EXÁMENES": []
    }
  }
};

const parametros_url = new URLSearchParams(window.location.search);
const id_asignatura = parametros_url.get("materia") || window.obtenerAsignaturaSeleccionada();
const datos_asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const recursos_asignatura = recursos_asignaturas[id_asignatura];

const lista_carpetas = document.querySelectorAll(".carpeta-click");
const cuerpo_tabla_archivos = document.getElementById("cuerpoTablaArchivos");
const filtro_tipo = document.getElementById("filtroTipo");
const filtro_etiqueta = document.getElementById("filtroEtiqueta");
const btn_navegacion_movil = document.getElementById("btnNavegacionMovil");
const sidebar_biblioteca = document.getElementById("menuCarpetasGeneral");
const btn_filtrar_movil = document.getElementById("btnFiltrarMovil");
const contenedor_filtros = document.getElementById("contenedorFiltros");

let unidad_actual = "UNIDAD 03";

window.guardarAsignaturaSeleccionada(id_asignatura);

renderizar_cabecera_recursos();
renderizar_tabla_recursos();

lista_carpetas.forEach(function (enlace) {
  enlace.addEventListener("click", function (evento) {
    evento.preventDefault();
    cambiar_carpeta_recursos(enlace);
  });
});

filtro_tipo.addEventListener("change", renderizar_tabla_recursos);
filtro_etiqueta.addEventListener("change", renderizar_tabla_recursos);

btn_navegacion_movil.addEventListener("click", function () {
  sidebar_biblioteca.classList.toggle("mostrar-movil");
  contenedor_filtros.classList.remove("mostrar-movil");
});

btn_filtrar_movil.addEventListener("click", function () {
  contenedor_filtros.classList.toggle("mostrar-movil");
  sidebar_biblioteca.classList.remove("mostrar-movil");
});

function renderizar_cabecera_recursos() {
  document.getElementById("tituloAsignatura").textContent = datos_asignatura.nombre;
  document.getElementById("profesorAsignatura").textContent = datos_asignatura.profesor;
  document.getElementById("unidadActualTextoAsignatura").textContent = datos_asignatura.unidadActualTexto;
}

function cambiar_carpeta_recursos(enlace) {
  lista_carpetas.forEach(function (item) {
    item.classList.remove("activo");
  });

  enlace.classList.add("activo");
  unidad_actual = enlace.dataset.unidad;

  actualizar_breadcrumb_recursos();
  renderizar_tabla_recursos();

  sidebar_biblioteca.classList.remove("mostrar-movil");
}

function actualizar_breadcrumb_recursos() {
  const texto_carpeta_padre = document.getElementById("textoCarpetaPadre");
  const flecha_breadcrumb = document.getElementById("flechaBreadcrumb");
  const texto_breadcrumb = document.getElementById("textoBreadcrumb");

  if (unidad_actual.includes("UNIDAD")) {
    texto_carpeta_padre.style.display = "inline";
    flecha_breadcrumb.style.display = "inline";
  } else {
    texto_carpeta_padre.style.display = "none";
    flecha_breadcrumb.style.display = "none";
  }

  texto_breadcrumb.textContent = unidad_actual;
}

function renderizar_tabla_recursos() {
  cuerpo_tabla_archivos.innerHTML = "";

  const archivos = recursos_asignatura.archivos[unidad_actual] || [];
  const tipo_seleccionado = filtro_tipo.value;
  const etiqueta_seleccionada = filtro_etiqueta.value;

  const archivos_filtrados = archivos.filter(function (archivo) {
    const coincide_tipo = tipo_seleccionado === "TODOS" || archivo.tipo === tipo_seleccionado;
    const coincide_etiqueta = etiqueta_seleccionada === "TODAS" || archivo.etiqueta === etiqueta_seleccionada;

  return coincide_tipo && coincide_etiqueta;
});

  if (archivos_filtrados.length === 0) {
    cuerpo_tabla_archivos.innerHTML = '<p class="mensaje-tabla-vacia">No hay recursos que coincidan con los filtros seleccionados.</p>';
    return;
  }

  archivos_filtrados.forEach(function (archivo) {
    cuerpo_tabla_archivos.insertAdjacentHTML(
      "beforeend",
      `
        <div class="biblioteca-tabla-fila archivo-fila">
          <a href="#" class="nombre-archivo">${archivo.nombre}</a>
          <span class="col-tipo">${archivo.tipo}</span>
          <span class="col-movil-oculta">${archivo.tamano}</span>
          <span class="col-movil-oculta">
            <span class="badge-etiqueta ${archivo.clase_etiqueta}">${archivo.etiqueta}</span>
          </span>
          <span class="col-movil-oculta">${archivo.fecha}</span>
        </div>
      `
    );
  });
}