/*
    Pantalla: Realizar examen
    Renderiza las preguntas, controla el progreso y corrige el examen.
*/

let preguntas_examen_actual = [];
let examen_corregido = false;

const examen_actual = window.obtenerExamenActual();

preguntas_examen_actual = obtener_preguntas_examen(examen_actual.id);

cargar_cabecera_examen(examen_actual);
cargar_informacion_examen(examen_actual, preguntas_examen_actual);
renderizar_preguntas(preguntas_examen_actual);
preparar_formulario_examen();
actualizar_progreso_examen();

function cargar_cabecera_examen(examen) {
  document.title = "Realizar · " + examen.nombre + " | DOA";

  document.getElementById("tituloExamen").textContent = examen.nombre;
  document.getElementById("asignaturaExamen").textContent = examen.asignatura;
  document.getElementById("fechaExamen").textContent = examen.fechaCompleta;
  document.getElementById("duracionExamen").textContent = examen.duracion;
}

function cargar_informacion_examen(examen, preguntas) {
  document.getElementById("totalPreguntasExamen").textContent =
    preguntas.length;
  document.getElementById("intentosExamen").textContent = examen.intentos;
  document.getElementById("contadorPreguntasRespondidas").textContent =
    "0 de " + preguntas.length + " respondidas";
}

function renderizar_preguntas(preguntas) {
  const contenedor = document.getElementById("listaPreguntasExamen");

  contenedor.innerHTML = "";

  preguntas.forEach(function (pregunta, indice) {
    const tarjeta = document.createElement("article");

    tarjeta.className = "pregunta-examen";
    tarjeta.dataset.pregunta = pregunta.id;

    let opciones_html = "";

    pregunta.opciones.forEach(function (opcion) {
      opciones_html +=
        '<label class="opcion-pregunta">' +
        '<input type="radio" name="' +
        pregunta.id +
        '" value="' +
        opcion.id +
        '">' +
        "<span>" +
        opcion.texto +
        "</span>" +
        "</label>";
    });

    tarjeta.innerHTML =
      '<div class="pregunta-examen__cabecera">' +
      '<span class="pregunta-examen__numero">' +
      (indice + 1) +
      "</span>" +
      "<h3>" +
      pregunta.enunciado +
      "</h3>" +
      "</div>" +
      '<div class="opciones-pregunta">' +
      opciones_html +
      "</div>" +
      '<p class="retroalimentacion-pregunta"></p>';

    contenedor.appendChild(tarjeta);
  });

  const respuestas = contenedor.querySelectorAll('input[type="radio"]');

  respuestas.forEach(function (respuesta) {
    respuesta.addEventListener("change", actualizar_progreso_examen);
  });
}

function preparar_formulario_examen() {
  const formulario = document.getElementById("formularioExamen");

  formulario.addEventListener("submit", function (evento) {
    evento.preventDefault();

    if (examen_corregido) {
      return;
    }

    if (!comprobar_preguntas_respondidas()) {
      mostrar_error_examen(true);
      return;
    }

    mostrar_error_examen(false);
    corregir_examen();
  });
}

function comprobar_preguntas_respondidas() {
  return preguntas_examen_actual.every(function (pregunta) {
    const respuesta = document.querySelector(
      'input[name="' + pregunta.id + '"]:checked',
    );

    return respuesta !== null;
  });
}

function mostrar_error_examen(mostrar) {
  document
    .getElementById("mensajeErrorExamen")
    .classList.toggle("oculto", !mostrar);
}

function actualizar_progreso_examen() {
  const total_preguntas = preguntas_examen_actual.length;
  let respondidas = 0;

  preguntas_examen_actual.forEach(function (pregunta) {
    const respuesta = document.querySelector(
      'input[name="' + pregunta.id + '"]:checked',
    );

    if (respuesta !== null) {
      respondidas++;
    }
  });

  const porcentaje = (respondidas / total_preguntas) * 100;

  document.getElementById("contadorPreguntasRespondidas").textContent =
    respondidas + " de " + total_preguntas + " respondidas";
  document.getElementById("barraProgresoExamen").style.width = porcentaje + "%";
}

const porcentaje =
  totalPreguntas === 0 ? 0 : (respondidas / totalPreguntas) * 100;

ponerTexto(
  "contadorPreguntasRespondidas",
  respondidas + " de " + totalPreguntas + " respondidas",
);

const barra = document.getElementById("barraProgresoExamen");

if (barra !== null) {
  barra.style.width = porcentaje + "%";
}

function corregir_examen() {
  let correctas = 0;

  preguntas_examen_actual.forEach(function (pregunta) {
    const respuesta_seleccionada = document.querySelector(
      'input[name="' + pregunta.id + '"]:checked',
    );
    const tarjeta_pregunta = document.querySelector(
      '[data-pregunta="' + pregunta.id + '"]',
    );
    const retroalimentacion = tarjeta_pregunta.querySelector(
      ".retroalimentacion-pregunta",
    );

    const respuesta_correcta =
      respuesta_seleccionada.value === pregunta.correcta;
    const opcion_seleccionada =
      respuesta_seleccionada.closest(".opcion-pregunta");
    const input_correcto = tarjeta_pregunta.querySelector(
      'input[value="' + pregunta.correcta + '"]',
    );
    const opcion_correcta = input_correcto.closest(".opcion-pregunta");

    if (respuesta_correcta) {
      correctas++;
      tarjeta_pregunta.classList.add("pregunta-examen--correcta");
      opcion_seleccionada.classList.add("opcion-pregunta--correcta");
    } else {
      tarjeta_pregunta.classList.add("pregunta-examen--incorrecta");
      opcion_seleccionada.classList.add("opcion-pregunta--incorrecta");
      opcion_correcta.classList.add("opcion-pregunta--correcta");
    }

    tarjeta_pregunta.classList.add("pregunta-examen--corregida");

    retroalimentacion.innerHTML =
      "<strong>" +
      (respuesta_correcta ? "Correcto." : "Incorrecto.") +
      "</strong> " +
      pregunta.explicacion;
  });

  bloquear_formulario();
  mostrar_resultado(correctas, preguntas_examen_actual.length);
  guardar_resultado(correctas, preguntas_examen_actual.length);

  examen_corregido = true;
}

function bloquear_formulario() {
  const respuestas = document.querySelectorAll('input[type="radio"]');
  const boton_entregar = document.querySelector(".boton-entregar-examen");

  respuestas.forEach(function (respuesta) {
    respuesta.disabled = true;
  });

  boton_entregar.disabled = true;
  boton_entregar.textContent = "Examen entregado";
}

function mostrar_resultado(correctas, total) {
  const nota = (correctas / total) * 10;
  const nota_formateada = nota.toFixed(1).replace(".", ",");

  document.getElementById("tarjetaResultadoExamen").classList.remove("oculto");
  document.getElementById("notaResultadoExamen").textContent =
    nota_formateada + "/10";
  document.getElementById("detalleResultadoExamen").textContent =
    correctas + " de " + total + " respuestas correctas";
}

function guardar_resultado(correctas, total) {
  const resultado = {
    examen: examen_actual.id,
    correctas: correctas,
    total: total,
    fecha: new Date().toISOString(),
  };

  localStorage.setItem("doaUltimoResultadoExamen", JSON.stringify(resultado));
}

function obtener_preguntas_examen(id_examen) {
  const preguntas = {
    matematicas_parcial_01: [
      {
        id: "pregunta_01",
        enunciado: "¿Qué representa el límite de una función en un punto?",
        correcta: "b",
        explicacion:
          "El límite describe el valor al que se aproxima la función cuando la variable se acerca a un punto.",
        opciones: [
          {
            id: "a",
            texto: "El valor máximo que puede tomar la función.",
          },
          {
            id: "b",
            texto: "El valor al que se aproxima la función cerca de un punto.",
          },
          {
            id: "c",
            texto: "El número de cortes con el eje X.",
          },
        ],
      },
      {
        id: "pregunta_02",
        enunciado:
          "Si una función es derivable en un punto, entonces en ese punto...",
        correcta: "a",
        explicacion:
          "La derivabilidad implica continuidad, aunque una función continua no siempre es derivable.",
        opciones: [
          {
            id: "a",
            texto: "También es continua.",
          },
          {
            id: "b",
            texto: "Siempre tiene una asíntota.",
          },
          {
            id: "c",
            texto: "No puede calcularse su límite.",
          },
        ],
      },
      {
        id: "pregunta_03",
        enunciado: "La derivada de una función puede interpretarse como...",
        correcta: "c",
        explicacion:
          "La derivada mide la tasa de cambio instantánea de una función.",
        opciones: [
          {
            id: "a",
            texto: "El área total bajo la curva.",
          },
          {
            id: "b",
            texto: "La media aritmética de sus valores.",
          },
          {
            id: "c",
            texto: "La tasa de cambio instantánea.",
          },
        ],
      },
    ],

    programacion_recursividad: [
      {
        id: "pregunta_01",
        enunciado:
          "¿Qué es imprescindible en una función recursiva para evitar un bucle infinito?",
        correcta: "b",
        explicacion:
          "El caso base permite detener la cadena de llamadas recursivas.",
        opciones: [
          {
            id: "a",
            texto: "Una variable global.",
          },
          {
            id: "b",
            texto: "Un caso base.",
          },
          {
            id: "c",
            texto: "Un array ordenado.",
          },
        ],
      },
      {
        id: "pregunta_02",
        enunciado: "Una llamada recursiva ocurre cuando...",
        correcta: "a",
        explicacion:
          "Una función es recursiva cuando se llama a sí misma directa o indirectamente.",
        opciones: [
          {
            id: "a",
            texto: "Una función se llama a sí misma.",
          },
          {
            id: "b",
            texto: "Una función no devuelve ningún valor.",
          },
          {
            id: "c",
            texto: "Un bucle se ejecuta una sola vez.",
          },
        ],
      },
      {
        id: "pregunta_03",
        enunciado: "¿Qué suele ocurrir si no se alcanza nunca el caso base?",
        correcta: "c",
        explicacion:
          "Si no se alcanza el caso base, las llamadas se acumulan hasta provocar un error o bloqueo.",
        opciones: [
          {
            id: "a",
            texto: "El programa termina correctamente.",
          },
          {
            id: "b",
            texto: "La función se convierte en iterativa.",
          },
          {
            id: "c",
            texto: "Puede producirse un desbordamiento de pila.",
          },
        ],
      },
    ],

    fisica_cinematica: [
      {
        id: "pregunta_01",
        enunciado:
          "¿Qué magnitud describe el cambio de velocidad con respecto al tiempo?",
        correcta: "b",
        explicacion:
          "La aceleración mide cómo cambia la velocidad a lo largo del tiempo.",
        opciones: [
          {
            id: "a",
            texto: "La posición.",
          },
          {
            id: "b",
            texto: "La aceleración.",
          },
          {
            id: "c",
            texto: "La masa.",
          },
        ],
      },
      {
        id: "pregunta_02",
        enunciado:
          "Si la velocidad de un objeto es constante, su aceleración es...",
        correcta: "a",
        explicacion: "Si no cambia la velocidad, la aceleración es cero.",
        opciones: [
          {
            id: "a",
            texto: "Cero.",
          },
          {
            id: "b",
            texto: "Siempre positiva.",
          },
          {
            id: "c",
            texto: "Siempre negativa.",
          },
        ],
      },
      {
        id: "pregunta_03",
        enunciado: "La fuerza neta sobre un cuerpo se relaciona con...",
        correcta: "c",
        explicacion:
          "Según la segunda ley de Newton, la fuerza neta se relaciona con masa y aceleración.",
        opciones: [
          {
            id: "a",
            texto: "Solo la temperatura.",
          },
          {
            id: "b",
            texto: "Solo la distancia recorrida.",
          },
          {
            id: "c",
            texto: "La masa y la aceleración.",
          },
        ],
      },
    ],
  };

  return preguntas[id_examen] || preguntas.matematicas_parcial_01;
}
