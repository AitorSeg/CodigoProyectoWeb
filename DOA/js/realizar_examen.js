/*
    Pantalla: Realizar examen
*/

let preguntasExamenActual = [];
let examenActual = null;
let examenCorregido = false;

document.addEventListener("DOMContentLoaded", function () {
    examenActual = window.obtenerExamenActual();

    if (!examenActual) {
        return;
    }

    preguntasExamenActual = obtenerPreguntasExamen(examenActual.id);

    cargarCabeceraExamen(examenActual);
    cargarInformacionExamen(examenActual, preguntasExamenActual);
    renderizarPreguntas(preguntasExamenActual);
    prepararFormularioExamen();
    actualizarProgresoExamen();
});

function ponerTexto(idElemento, texto) {
    const elemento = document.getElementById(idElemento);

    if (elemento !== null) {
        elemento.textContent = texto;
    }
}

function cargarCabeceraExamen(examen) {
    document.title = "Realizar · " + examen.nombre + " | DOA";

    ponerTexto("tituloExamen", examen.nombre);
    ponerTexto("asignaturaExamen", examen.asignatura);
    ponerTexto("fechaExamen", examen.fechaCompleta);
    ponerTexto("duracionExamen", examen.duracion);
}

function cargarInformacionExamen(examen, preguntas) {
    ponerTexto("totalPreguntasExamen", preguntas.length);
    ponerTexto("intentosExamen", examen.intentos);
    ponerTexto("contadorPreguntasRespondidas", "0 de " + preguntas.length + " respondidas");
}

function renderizarPreguntas(preguntas) {
    const contenedor = document.getElementById("listaPreguntasExamen");

    if (contenedor === null) {
        return;
    }

    contenedor.innerHTML = "";

    preguntas.forEach(function (pregunta, indice) {
        const tarjeta = document.createElement("article");

        tarjeta.className = "pregunta-examen";
        tarjeta.dataset.pregunta = pregunta.id;

        let opcionesHtml = "";

        pregunta.opciones.forEach(function (opcion) {
            opcionesHtml +=
                '<label class="opcion-pregunta">' +
                    '<input type="radio" name="' + pregunta.id + '" value="' + opcion.id + '">' +
                    '<span>' + opcion.texto + '</span>' +
                '</label>';
        });

        tarjeta.innerHTML =
            '<div class="pregunta-examen__cabecera">' +
                '<span class="pregunta-examen__numero">' + (indice + 1) + '</span>' +
                '<h3>' + pregunta.enunciado + '</h3>' +
            '</div>' +
            '<div class="opciones-pregunta">' +
                opcionesHtml +
            '</div>' +
            '<p class="retroalimentacion-pregunta"></p>';

        contenedor.appendChild(tarjeta);
    });

    const respuestas = contenedor.querySelectorAll('input[type="radio"]');

    respuestas.forEach(function (respuesta) {
        respuesta.addEventListener("change", actualizarProgresoExamen);
    });
}

function prepararFormularioExamen() {
    const formulario = document.getElementById("formularioExamen");

    if (formulario === null) {
        return;
    }

    formulario.addEventListener("submit", function (evento) {
        evento.preventDefault();

        if (examenCorregido) {
            return;
        }

        const todasRespondidas = comprobarPreguntasRespondidas();

        if (!todasRespondidas) {
            mostrarErrorExamen(true);
            return;
        }

        mostrarErrorExamen(false);
        corregirExamen();
    });
}

function comprobarPreguntasRespondidas() {
    return preguntasExamenActual.every(function (pregunta) {
        const respuesta = document.querySelector('input[name="' + pregunta.id + '"]:checked');

        return respuesta !== null;
    });
}

function mostrarErrorExamen(mostrar) {
    const mensaje = document.getElementById("mensajeErrorExamen");

    if (mensaje !== null) {
        mensaje.classList.toggle("oculto", !mostrar);
    }
}

function actualizarProgresoExamen() {
    const totalPreguntas = preguntasExamenActual.length;
    let respondidas = 0;

    preguntasExamenActual.forEach(function (pregunta) {
        const respuesta = document.querySelector('input[name="' + pregunta.id + '"]:checked');

        if (respuesta !== null) {
            respondidas++;
        }
    });

    const porcentaje = totalPreguntas === 0 ? 0 : (respondidas / totalPreguntas) * 100;

    ponerTexto("contadorPreguntasRespondidas", respondidas + " de " + totalPreguntas + " respondidas");

    const barra = document.getElementById("barraProgresoExamen");

    if (barra !== null) {
        barra.style.width = porcentaje + "%";
    }
}

function corregirExamen() {
    let correctas = 0;

    preguntasExamenActual.forEach(function (pregunta) {
        const respuestaSeleccionada = document.querySelector('input[name="' + pregunta.id + '"]:checked');
        const tarjetaPregunta = document.querySelector('[data-pregunta="' + pregunta.id + '"]');
        const retroalimentacion = tarjetaPregunta.querySelector(".retroalimentacion-pregunta");

        const respuestaCorrecta = respuestaSeleccionada.value === pregunta.correcta;

        if (respuestaCorrecta) {
            correctas++;
            tarjetaPregunta.classList.add("pregunta-examen--correcta");
        } else {
            tarjetaPregunta.classList.add("pregunta-examen--incorrecta");
        }

        tarjetaPregunta.classList.add("pregunta-examen--corregida");

        if (retroalimentacion !== null) {
            retroalimentacion.innerHTML =
                '<strong>' + (respuestaCorrecta ? "Correcto." : "Incorrecto.") + '</strong> ' +
                pregunta.explicacion;
        }
    });

    bloquearFormulario();
    mostrarResultado(correctas, preguntasExamenActual.length);
    guardarResultado(correctas, preguntasExamenActual.length);

    examenCorregido = true;
}

function bloquearFormulario() {
    const respuestas = document.querySelectorAll('input[type="radio"]');
    const botonEntregar = document.querySelector(".boton-entregar-examen");

    respuestas.forEach(function (respuesta) {
        respuesta.disabled = true;
    });

    if (botonEntregar !== null) {
        botonEntregar.disabled = true;
        botonEntregar.textContent = "Examen entregado";
    }
}

function mostrarResultado(correctas, total) {
    const tarjetaResultado = document.getElementById("tarjetaResultadoExamen");
    const notaResultado = document.getElementById("notaResultadoExamen");
    const detalleResultado = document.getElementById("detalleResultadoExamen");

    const nota = total === 0 ? 0 : (correctas / total) * 10;
    const notaFormateada = nota.toFixed(1).replace(".", ",");

    if (tarjetaResultado !== null) {
        tarjetaResultado.classList.remove("oculto");
    }

    if (notaResultado !== null) {
        notaResultado.textContent = notaFormateada + "/10";
    }

    if (detalleResultado !== null) {
        detalleResultado.textContent = correctas + " de " + total + " respuestas correctas";
    }
}

function guardarResultado(correctas, total) {
    const resultado = {
        examen: examenActual.id,
        correctas: correctas,
        total: total,
        fecha: new Date().toISOString()
    };

    localStorage.setItem("doaUltimoResultadoExamen", JSON.stringify(resultado));
}

function obtenerPreguntasExamen(idExamen) {
    const preguntas = {
        matematicas_parcial_01: [
            {
                id: "pregunta_01",
                enunciado: "¿Qué representa el límite de una función en un punto?",
                correcta: "b",
                explicacion: "El límite describe el valor al que se aproxima la función cuando la variable se acerca a un punto.",
                opciones: [
                    {
                        id: "a",
                        texto: "El valor máximo que puede tomar la función."
                    },
                    {
                        id: "b",
                        texto: "El valor al que se aproxima la función cerca de un punto."
                    },
                    {
                        id: "c",
                        texto: "El número de cortes con el eje X."
                    }
                ]
            },
            {
                id: "pregunta_02",
                enunciado: "Si una función es derivable en un punto, entonces en ese punto...",
                correcta: "a",
                explicacion: "La derivabilidad implica continuidad, aunque una función continua no siempre es derivable.",
                opciones: [
                    {
                        id: "a",
                        texto: "También es continua."
                    },
                    {
                        id: "b",
                        texto: "Siempre tiene una asíntota."
                    },
                    {
                        id: "c",
                        texto: "No puede calcularse su límite."
                    }
                ]
            },
            {
                id: "pregunta_03",
                enunciado: "La derivada de una función puede interpretarse como...",
                correcta: "c",
                explicacion: "La derivada mide la tasa de cambio instantánea de una función.",
                opciones: [
                    {
                        id: "a",
                        texto: "El área total bajo la curva."
                    },
                    {
                        id: "b",
                        texto: "La media aritmética de sus valores."
                    },
                    {
                        id: "c",
                        texto: "La tasa de cambio instantánea."
                    }
                ]
            }
        ],

        programacion_recursividad: [
            {
                id: "pregunta_01",
                enunciado: "¿Qué es imprescindible en una función recursiva para evitar un bucle infinito?",
                correcta: "b",
                explicacion: "El caso base permite detener la cadena de llamadas recursivas.",
                opciones: [
                    {
                        id: "a",
                        texto: "Una variable global."
                    },
                    {
                        id: "b",
                        texto: "Un caso base."
                    },
                    {
                        id: "c",
                        texto: "Un array ordenado."
                    }
                ]
            },
            {
                id: "pregunta_02",
                enunciado: "Una llamada recursiva ocurre cuando...",
                correcta: "a",
                explicacion: "Una función es recursiva cuando se llama a sí misma directa o indirectamente.",
                opciones: [
                    {
                        id: "a",
                        texto: "Una función se llama a sí misma."
                    },
                    {
                        id: "b",
                        texto: "Una función no devuelve ningún valor."
                    },
                    {
                        id: "c",
                        texto: "Un bucle se ejecuta una sola vez."
                    }
                ]
            },
            {
                id: "pregunta_03",
                enunciado: "¿Qué suele ocurrir si no se alcanza nunca el caso base?",
                correcta: "c",
                explicacion: "Si no se alcanza el caso base, las llamadas se acumulan hasta provocar un error o bloqueo.",
                opciones: [
                    {
                        id: "a",
                        texto: "El programa termina correctamente."
                    },
                    {
                        id: "b",
                        texto: "La función se convierte en iterativa."
                    },
                    {
                        id: "c",
                        texto: "Puede producirse un desbordamiento de pila."
                    }
                ]
            }
        ],

        fisica_cinematica: [
            {
                id: "pregunta_01",
                enunciado: "¿Qué magnitud describe el cambio de velocidad con respecto al tiempo?",
                correcta: "b",
                explicacion: "La aceleración mide cómo cambia la velocidad a lo largo del tiempo.",
                opciones: [
                    {
                        id: "a",
                        texto: "La posición."
                    },
                    {
                        id: "b",
                        texto: "La aceleración."
                    },
                    {
                        id: "c",
                        texto: "La masa."
                    }
                ]
            },
            {
                id: "pregunta_02",
                enunciado: "Si la velocidad de un objeto es constante, su aceleración es...",
                correcta: "a",
                explicacion: "Si no cambia la velocidad, la aceleración es cero.",
                opciones: [
                    {
                        id: "a",
                        texto: "Cero."
                    },
                    {
                        id: "b",
                        texto: "Siempre positiva."
                    },
                    {
                        id: "c",
                        texto: "Siempre negativa."
                    }
                ]
            },
            {
                id: "pregunta_03",
                enunciado: "La fuerza neta sobre un cuerpo se relaciona con...",
                correcta: "c",
                explicacion: "Según la segunda ley de Newton, la fuerza neta se relaciona con masa y aceleración.",
                opciones: [
                    {
                        id: "a",
                        texto: "Solo la temperatura."
                    },
                    {
                        id: "b",
                        texto: "Solo la distancia recorrida."
                    },
                    {
                        id: "c",
                        texto: "La masa y la aceleración."
                    }
                ]
            }
        ]
    };

    return preguntas[idExamen] || preguntas.matematicas_parcial_01;
}