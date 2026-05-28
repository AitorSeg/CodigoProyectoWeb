const datos_panel_profesor = {
  matematicas: {
    estudiantes: 28,
    tareas_activas: 2,
    entregas_pendientes: 11,
    recursos_publicados: 6,
    pendiente: "Revisar entregas de límites"
  },

  programacion: {
    estudiantes: 32,
    tareas_activas: 3,
    entregas_pendientes: 14,
    recursos_publicados: 8,
    pendiente: "Preparar tarea de recursividad"
  },

  fisica: {
    estudiantes: 24,
    tareas_activas: 1,
    entregas_pendientes: 7,
    recursos_publicados: 5,
    pendiente: "Publicar recurso de movimiento"
  }
};

const asignaturas_por_profesor = {
  "Kevan Pounds Mainston": ["programacion"],
  "Luelle Pridmore Starsmeare": ["matematicas"],
  "Eolande Merriton Mizzi": ["fisica"]
};

let asignaturas_profesor = obtener_asignaturas_del_profesor();
let asignatura_activa_profesor = obtener_asignatura_inicial();

cargar_saludo_profesor();
renderizar_resumen_profesor();
renderizar_asignaturas_profesor();
renderizar_panel_asignatura_activa();
renderizar_pendientes_profesor();
actualizar_acciones_rapidas_profesor();

function obtener_usuario_demo() {
  const usuario_guardado = sessionStorage.getItem("usuarioDemoDOA");

  return usuario_guardado === null ? null : JSON.parse(usuario_guardado);
}

function obtener_asignaturas_del_profesor() {
  const usuario = obtener_usuario_demo();

  if (usuario === null) {
    return ["programacion"];
  }

  return asignaturas_por_profesor[usuario.nombre] || ["programacion"];
}

function obtener_asignatura_inicial() {
  const asignatura_guardada = window.obtenerAsignaturaSeleccionada();

  if (asignaturas_profesor.includes(asignatura_guardada)) {
    return asignatura_guardada;
  }

  return asignaturas_profesor[0];
}

function cargar_saludo_profesor() {
  const usuario = obtener_usuario_demo();
  const nombre = usuario === null ? "profesor" : usuario.nombre.split(" ")[0];

  document.getElementById("saludoProfesor").textContent = "Buenos días, " + nombre;
}

function renderizar_resumen_profesor() {
  let total_tareas = 0;
  let total_entregas = 0;
  let total_recursos = 0;

  asignaturas_profesor.forEach(function (id_asignatura) {
    const datos = datos_panel_profesor[id_asignatura];

    total_tareas += datos.tareas_activas;
    total_entregas += datos.entregas_pendientes;
    total_recursos += datos.recursos_publicados;
  });

  document.getElementById("totalAsignaturasProfesor").textContent = asignaturas_profesor.length;
  document.getElementById("totalTareasActivasProfesor").textContent = total_tareas;
  document.getElementById("totalEntregasPendientesProfesor").textContent = total_entregas;
  document.getElementById("totalRecursosProfesor").textContent = total_recursos;
}

function renderizar_asignaturas_profesor() {
  const contenedor = document.getElementById("listaAsignaturasProfesor");

  contenedor.innerHTML = "";

  asignaturas_profesor.forEach(function (id_asignatura) {
    const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
    const datos = datos_panel_profesor[id_asignatura];
    const tarjeta = document.createElement("article");

    tarjeta.className = "tarjeta-asignatura-profesor";

    if (id_asignatura === asignatura_activa_profesor) {
      tarjeta.classList.add("tarjeta-asignatura-profesor--activa");
    }

    tarjeta.innerHTML = `
      <div class="tarjeta-asignatura-profesor__cabecera">
        <div>
          <h3>${asignatura.nombre}</h3>
          <p>${asignatura.unidadActualTexto}</p>
        </div>

        <span class="etiqueta-asignatura-profesor">
          ${id_asignatura === asignatura_activa_profesor ? "Activa" : "Asignatura"}
        </span>
      </div>

      <ul class="datos-asignatura-profesor">
        <li>
          <span>Alumnos</span>
          <strong>${datos.estudiantes}</strong>
        </li>

        <li>
          <span>Tareas</span>
          <strong>${datos.tareas_activas}</strong>
        </li>

        <li>
          <span>Entregas</span>
          <strong>${datos.entregas_pendientes}</strong>
        </li>
      </ul>

      <div class="acciones-asignatura-profesor">
        <a
          href="detalle_asignatura_profesor.php?materia=${id_asignatura}"
          class="boton-asignatura-profesor boton-asignatura-profesor--principal"
          data-asignatura="${id_asignatura}"
        >
          Entrar
        </a>
      </div>
    `;

    tarjeta.addEventListener("click", function () {
      seleccionar_asignatura_profesor(id_asignatura);
    });

    tarjeta.querySelector("a").addEventListener("click", function (evento) {
      evento.stopPropagation();
      guardar_asignatura_profesor(id_asignatura);
    });

    contenedor.appendChild(tarjeta);
  });
}

function seleccionar_asignatura_profesor(id_asignatura) {
  asignatura_activa_profesor = id_asignatura;

  guardar_asignatura_profesor(id_asignatura);
  renderizar_asignaturas_profesor();
  renderizar_panel_asignatura_activa();
  actualizar_acciones_rapidas_profesor();
}

function renderizar_panel_asignatura_activa() {
  const asignatura = window.DOA_ASIGNATURAS[asignatura_activa_profesor];

  document.getElementById("asignaturaActivaProfesor").textContent = asignatura.nombre;
  document.getElementById("unidadActivaProfesor").textContent = asignatura.unidadActualTexto;

  actualizar_enlace_asignatura(
    "botonRecursosAsignaturaActiva",
    "recursos_profesor.php?materia=" + asignatura_activa_profesor
  );

  actualizar_enlace_asignatura(
    "botonTareaAsignaturaActiva",
    "crear_tarea.php?materia=" + asignatura_activa_profesor
  );
}

function renderizar_pendientes_profesor() {
  const contenedor = document.getElementById("listaPendientesProfesor");

  contenedor.innerHTML = "";

  asignaturas_profesor.forEach(function (id_asignatura) {
    const asignatura = window.DOA_ASIGNATURAS[id_asignatura];
    const datos = datos_panel_profesor[id_asignatura];
    const pendiente = document.createElement("div");

    pendiente.className = "pendiente-profesor";
    pendiente.innerHTML = `
      <strong>${datos.pendiente}</strong>
      <span>${asignatura.nombre} · ${datos.entregas_pendientes} entregas pendientes</span>
    `;

    contenedor.appendChild(pendiente);
  });
}

function actualizar_acciones_rapidas_profesor() {
  actualizar_enlace_asignatura(
    "accionCrearTarea",
    "crear_tarea.php?materia=" + asignatura_activa_profesor
  );

  actualizar_enlace_asignatura(
    "accionSubirRecurso",
    "recursos_profesor.php?materia=" + asignatura_activa_profesor
  );
}

function actualizar_enlace_asignatura(id_enlace, url) {
  const enlace = document.getElementById(id_enlace);

  enlace.href = url;

  enlace.addEventListener("click", function () {
    guardar_asignatura_profesor(asignatura_activa_profesor);
  });
}

function guardar_asignatura_profesor(id_asignatura) {
  window.guardarAsignaturaSeleccionada(id_asignatura);
}