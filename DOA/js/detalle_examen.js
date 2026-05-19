/*
    Pantalla: Detalle de examen
    Carga el examen seleccionado y adapta la acción principal según su estado.
*/

const examen_actual = window.obtenerExamenActual();

cargar_detalle_examen(examen_actual);

function cargar_detalle_examen(examen) {
  document.title = examen.nombre + " | DOA";

  document.getElementById("tituloExamen").textContent = examen.nombre;
  document.getElementById("asignaturaExamen").textContent = examen.asignatura;
  document.getElementById("fechaExamen").textContent = examen.fechaCompleta;

  document.getElementById("descripcionExamen").textContent = examen.descripcion;
  document.getElementById("estadoExamen").textContent = examen.estado;

  document.getElementById("aperturaExamen").textContent = examen.fechaApertura;
  document.getElementById("cierreExamen").textContent = examen.fechaCierre;
  document.getElementById("duracionExamen").textContent = examen.duracion;
  document.getElementById("preguntasExamen").textContent = examen.preguntas;
  document.getElementById("intentosExamen").textContent = examen.intentos;

  actualizar_estado_examen(examen);
  renderizar_temas_examen(examen.temas);
  preparar_boton_realizar_examen(examen);
}

function actualizar_estado_examen(examen) {
  const estado = document.getElementById("estadoExamen");
  const tarjeta = document.getElementById("tarjetaDetalleExamen");

  estado.classList.remove(
    "estado-detalle-examen--cerrado",
    "estado-detalle-examen--proximo"
  );

  tarjeta.dataset.estado = examen.estadoFiltro;

  if (examen.estadoFiltro === "cerrado") {
    estado.classList.add("estado-detalle-examen--cerrado");
  }

  if (examen.estadoFiltro === "proximo") {
    estado.classList.add("estado-detalle-examen--proximo");
  }
}

function renderizar_temas_examen(temas) {
  const lista = document.getElementById("temasExamen");

  lista.innerHTML = "";

  temas.forEach(function (tema) {
    const elemento = document.createElement("li");

    elemento.textContent = tema;
    lista.appendChild(elemento);
  });
}

function preparar_boton_realizar_examen(examen) {
  const boton = document.getElementById("botonRealizarExamen");
  const mensaje = document.getElementById("mensajeAccesoExamen");

  if (examen.estadoFiltro === "abierto") {
    boton.textContent = "Realizar examen";
    boton.href = "realizar_examen.html";
    boton.classList.remove("boton-realizar-examen--desactivado");
    mensaje.textContent = "El examen está disponible. Puedes empezar cuando quieras.";

    boton.addEventListener("click", function () {
      window.guardarExamenSeleccionado(examen.id);
    });

    return;
  }

  boton.removeAttribute("href");
  boton.classList.add("boton-realizar-examen--desactivado");

  if (examen.estadoFiltro === "cerrado") {
    boton.textContent = "Examen cerrado";
    mensaje.textContent = "Este examen ya no está disponible para realizarse.";
  }

  if (examen.estadoFiltro === "proximo") {
    boton.textContent = "No disponible";
    mensaje.textContent = "Este examen todavía no está disponible.";
  }
}