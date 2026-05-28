/*
    Pantalla: Crear examen
*/

const contenedor_preguntas = document.getElementById("contenedorPreguntas");
const btn_anadir_pregunta = document.getElementById("btnAnadirPregunta");
const resumen_preguntas_examen = document.getElementById("resumenPreguntasExamen");

let contador_preguntas = document.querySelectorAll(".bloque-pregunta-crear").length;

btn_anadir_pregunta.addEventListener("click", function () {
    agregar_pregunta();
});

contenedor_preguntas.addEventListener("click", function (evento) {
    if (!evento.target.classList.contains("btn-eliminar-pregunta")) {
        return;
    }

    evento.target.closest(".bloque-pregunta-crear").remove();

    renumerar_preguntas();
    actualizar_resumen_preguntas();
});

renumerar_preguntas();
actualizar_resumen_preguntas();

function agregar_pregunta() {
    const indice = contador_preguntas;
    contador_preguntas++;

    contenedor_preguntas.insertAdjacentHTML("beforeend", crear_html_pregunta(indice));

    renumerar_preguntas();
    actualizar_resumen_preguntas();
}

function crear_html_pregunta(indice) {
    return `
        <article class="bloque-pregunta-crear">
            <div class="cabecera-pregunta-crear">
                <h3>Pregunta <span class="numero-pregunta-crear"></span></h3>
                <button class="btn-eliminar-pregunta" type="button">Eliminar</button>
            </div>

            <div class="grupo-campo-formulario">
                <label>Enunciado de la pregunta</label>

                <textarea
                    class="input-enunciado"
                    name="preguntas[${indice}][enunciado]"
                    rows="2"
                    placeholder="Escribe el enunciado..."
                    required></textarea>
            </div>

            <div class="opciones-contenedor">
                <label class="label-secundario">Opciones</label>

                ${crear_html_opcion(indice, 0, "Opción 1")}
                ${crear_html_opcion(indice, 1, "Opción 2")}
                ${crear_html_opcion(indice, 2, "Opción 3")}
            </div>

            <div class="grupo-campo-formulario grupo-campo-sin-margen">
                <label>Explicación de la respuesta</label>

                <input
                    class="input-explicacion"
                    name="preguntas[${indice}][explicacion]"
                    type="text"
                    placeholder="Explica por qué esta es la respuesta correcta..."
                    required>
            </div>
        </article>
    `;
}

function crear_html_opcion(indice_pregunta, indice_opcion, placeholder) {
    return `
        <div class="fila-opcion-crear">
            <input
                type="radio"
                name="preguntas[${indice_pregunta}][correcta]"
                value="${indice_opcion}"
                required>

            <input
                type="text"
                name="preguntas[${indice_pregunta}][opciones][]"
                placeholder="${placeholder}"
                required>
        </div>
    `;
}

function renumerar_preguntas() {
    const preguntas = document.querySelectorAll(".bloque-pregunta-crear");

    preguntas.forEach(function (pregunta, indice) {
        pregunta.querySelector(".numero-pregunta-crear").textContent = indice + 1;
        pregunta.querySelector(".btn-eliminar-pregunta").hidden = preguntas.length === 1;
    });
}

function actualizar_resumen_preguntas() {
    resumen_preguntas_examen.textContent = document.querySelectorAll(".bloque-pregunta-crear").length;
}