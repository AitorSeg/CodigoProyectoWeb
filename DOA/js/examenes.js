/*
    Pantalla: Exámenes del alumno
    Carga los exámenes de la asignatura seleccionada y permite filtrarlos.
*/

let filtro_activo = "todos";

const id_asignatura = window.obtenerAsignaturaSeleccionada();
const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
const examenes_actuales = window.obtenerExamenesAsignatura(id_asignatura);

cargar_cabecera_asignatura();
cargar_resumen_examenes();
cargar_examen_destacado();
preparar_filtros_examenes();
renderizar_examenes();

function cargar_cabecera_asignatura() {
  document.title = "Exámenes · " + asignatura.nombre + " | DOA";

  document.getElementById("tituloAsignatura").textContent = asignatura.nombre;
  document.getElementById("profesorAsignatura").textContent =
    asignatura.profesor;
  document.getElementById("unidadActualTextoAsignatura").textContent =
    asignatura.unidadActualTexto;
}

function cargar_resumen_examenes() {
  const examenes_abiertos = examenes_actuales.filter(function (examen) {
    return examen.estadoFiltro === "abierto";
  }).length;

  const examenes_completados = examenes_actuales.filter(function (examen) {
    return examen.estadoFiltro === "cerrado";
  }).length;

  const proximo_examen = examenes_actuales.find(function (examen) {
    return (
      examen.estadoFiltro === "proximo" || examen.estadoFiltro === "abierto"
    );
  });

  document.getElementById("totalExamenesAbiertos").textContent =
    examenes_abiertos;
  document.getElementById("totalExamenesRealizados").textContent =
    examenes_completados;
  document.getElementById("proximoExamenTexto").textContent =
    proximo_examen.fechaCorta;
}

function cargar_examen_destacado() {
  const examen_abierto = examenes_actuales.find(function (examen) {
    return examen.estadoFiltro === "abierto";
  });

  const examen_destacado = examen_abierto || examenes_actuales[0];
  const etiqueta_estado = document.getElementById("estadoExamenDestacado");
  const boton_examen = document.getElementById("botonExamenDestacado");

  document.getElementById("tituloExamenDestacado").textContent =
    examen_destacado.nombre;
  document.getElementById("descripcionExamenDestacado").textContent =
    examen_destacado.descripcion;
  document.getElementById("fechaLimiteExamenDestacado").textContent =
    examen_destacado.fechaCierre;

  etiqueta_estado.textContent = examen_destacado.estado;
  etiqueta_estado.className =
    "etiqueta-examen etiqueta-examen--" + examen_destacado.estadoFiltro;

  document.getElementById("examenDestacado").dataset.estado =
    examen_destacado.estadoFiltro;

  boton_examen.dataset.examen = examen_destacado.id;
  boton_examen.textContent =
    examen_destacado.estadoFiltro === "abierto" ? "Entrar" : "Ver detalles";

  boton_examen.addEventListener("click", function () {
    window.guardarExamenSeleccionado(examen_destacado.id);
  });
}

function preparar_filtros_examenes() {
  const filtros = document.querySelectorAll(".filtro-examen");

  filtros.forEach(function (filtro) {
    filtro.addEventListener("click", function () {
      filtro_activo = filtro.dataset.filtro;

      filtros.forEach(function (boton) {
        boton.classList.toggle("filtro-examen--activo", boton === filtro);
      });

      renderizar_examenes();
    });
  });
}

function renderizar_examenes() {
  const contenedor = document.getElementById("listadoExamenes");

  contenedor.innerHTML = "";

  const examenes_filtrados = examenes_actuales.filter(function (examen) {
    return filtro_activo === "todos" || examen.estadoFiltro === filtro_activo;
  });

  if (examenes_filtrados.length === 0) {
    contenedor.innerHTML =
      '<p class="mensaje-sin-examenes">No hay exámenes con este filtro.</p>';
    return;
  }

  examenes_filtrados.forEach(function (examen) {
    const es_abierto = examen.estadoFiltro === "abierto";
    const texto_accion = es_abierto ? "Entrar" : "Ver detalles";
    const clase_accion = es_abierto
      ? "fila-examen__accion fila-examen__accion--principal"
      : "fila-examen__accion";

    contenedor.insertAdjacentHTML(
      "beforeend",
      `
        <article class="fila-examen">
          <div class="fila-examen__nombre">
            <strong>${examen.nombre}</strong>
            <span>${examen.descripcionCorta}</span>
          </div>

          <p class="fila-examen__fecha" data-duracion="${examen.duracion}">
            ${examen.fechaCompleta}
          </p>

          <p class="fila-examen__duracion">
            ${examen.duracion}
          </p>

          <p class="fila-examen__estado">
            <span class="etiqueta-examen etiqueta-examen--${examen.estadoFiltro}">
              ${examen.estado}
            </span>
          </p>

          <a href="detalle_examen.html" class="${clase_accion}" data-examen="${examen.id}">
            ${texto_accion}
          </a>
        </article>
      `,
    );
  });

  preparar_enlaces_examenes();
}

function preparar_enlaces_examenes() {
  const enlaces_examenes = document.querySelectorAll(".fila-examen__accion");

  enlaces_examenes.forEach(function (enlace) {
    enlace.addEventListener("click", function () {
      window.guardarExamenSeleccionado(enlace.dataset.examen);
    });
  });
}
