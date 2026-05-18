/*
    Pantalla: Crear Examen
    Reglas aplicadas: camelCase (funciones), snake_case (variables), Cero DOMContentLoaded, Cero funciones anónimas
*/

let contador_preguntas_global = 0;

function eliminarPregunta(id_bloque) {
    const bloque_remover = document.getElementById(id_bloque);
    if (bloque_remover) {
        bloque_remover.remove();
    }
}

function agregarBloquePregunta() {
    contador_preguntas_global++;
    const contenedor_preguntas = document.getElementById("contenedorPreguntas");

    if (!contenedor_preguntas) return;

    const id_pregunta = "preg_" + contador_preguntas_global;

    let boton_eliminar_html = "";
    if (contador_preguntas_global > 1) {
        boton_eliminar_html = `<button type="button" class="btn-eliminar-pregunta" onclick="eliminarPregunta('bloque_${id_pregunta}')">Eliminar</button>`;
    }

    const html_pregunta = `
        <div class="bloque-pregunta-crear" id="bloque_${id_pregunta}">
            <div class="cabecera-pregunta">
                <h3>Pregunta ${contador_preguntas_global}</h3>
                ${boton_eliminar_html}
            </div>
            
            <div class="grupo-campo-formulario">
                <label>Enunciado de la pregunta</label>
                <textarea class="input-enunciado" rows="2" placeholder="Ej: ¿Qué representa el límite de una función en un punto?" required></textarea>
            </div>

            <div class="opciones-contenedor">
                <label class="label-secundario">Opciones (Selecciona la correcta)</label>
                
                <div class="fila-opcion-crear">
                    <input type="radio" name="correcta_${id_pregunta}" value="a" required>
                    <input type="text" class="input-opcion-a" placeholder="Opción A" required>
                </div>
                <div class="fila-opcion-crear">
                    <input type="radio" name="correcta_${id_pregunta}" value="b">
                    <input type="text" class="input-opcion-b" placeholder="Opción B" required>
                </div>
                <div class="fila-opcion-crear">
                    <input type="radio" name="correcta_${id_pregunta}" value="c">
                    <input type="text" class="input-opcion-c" placeholder="Opción C" required>
                </div>
            </div>

            <div class="grupo-campo-formulario grupo-campo-sin-margen">
                <label>Explicación de la respuesta</label>
                <input type="text" class="input-explicacion" placeholder="Ej: El límite describe el valor al que se aproxima..." required>
            </div>
        </div>
    `;

    contenedor_preguntas.insertAdjacentHTML("beforeend", html_pregunta);
}

function procesarFormularioExamen(evento) {
    evento.preventDefault();

    const input_nombre = document.getElementById("inputNombre").value;
    const select_asignatura = document.getElementById("selectAsignatura");
    const asignatura_clave = select_asignatura.value;
    const asignatura_texto = select_asignatura.options[select_asignatura.selectedIndex].text;
    const input_unidad = document.getElementById("inputUnidad").value;
    const input_descripcion = document.getElementById("inputDescripcion").value;
    const input_duracion = document.getElementById("inputDuracion").value;
    const input_intentos = document.getElementById("inputIntentos").value;

    const array_preguntas = [];
    const bloques_preguntas = document.querySelectorAll(".bloque-pregunta-crear");

    for (let i = 0; i < bloques_preguntas.length; i++) {
        const bloque_actual = bloques_preguntas[i];

        const enunciado_texto = bloque_actual.querySelector(".input-enunciado").value;
        const explicacion_texto = bloque_actual.querySelector(".input-explicacion").value;

        const texto_a = bloque_actual.querySelector(".input-opcion-a").value;
        const texto_b = bloque_actual.querySelector(".input-opcion-b").value;
        const texto_c = bloque_actual.querySelector(".input-opcion-c").value;

        const radio_seleccionado = bloque_actual.querySelector(`input[type="radio"]:checked`);
        let respuesta_correcta = "a";

        if (radio_seleccionado) {
            respuesta_correcta = radio_seleccionado.value;
        }

        array_preguntas.push({
            id: "pregunta_creada_" + (i + 1),
            enunciado: enunciado_texto,
            correcta: respuesta_correcta,
            explicacion: explicacion_texto,
            opciones: [
                { id: "a", texto: texto_a },
                { id: "b", texto: texto_b },
                { id: "c", texto: texto_c }
            ]
        });
    }

    const fecha_hoy = new Date();
    const MESES_CORTOS = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
    const mes_actual = MESES_CORTOS[fecha_hoy.getMonth()];
    const fecha_formateada = fecha_hoy.getDate() + " " + mes_actual + ", " + fecha_hoy.getFullYear();
    const id_nuevo_examen = asignatura_clave + "_creado_" + Date.now();

    const nuevo_examen = {
        id: id_nuevo_examen,
        nombre: input_nombre,
        asignatura: asignatura_texto,
        asignaturaClave: asignatura_clave,
        unidad: input_unidad,
        descripcion: input_descripcion,
        descripcionCorta: input_unidad,
        fechaCompleta: fecha_formateada,
        fechaCorta: fecha_hoy.getDate() + " " + mes_actual,
        fechaApertura: fecha_formateada,
        fechaCierre: "Sin límite",
        duracion: input_duracion + " min",
        preguntas: array_preguntas.length + " preguntas",
        intentos: input_intentos + " intento(s)",
        estado: "Abierto",
        estadoFiltro: "abierto",
        temas: ["Tema General"],
        preguntasArray: array_preguntas
    };

    let examenes_creados = JSON.parse(localStorage.getItem("doaExamenesCreados"));
    if (!examenes_creados) {
        examenes_creados = [];
    }

    examenes_creados.unshift(nuevo_examen);
    localStorage.setItem("doaExamenesCreados", JSON.stringify(examenes_creados));

    window.location.href = "examenes.html";
}

function inicializarCrearExamen() {
    const formulario_creacion = document.getElementById("formularioCrearExamen");
    const boton_anadir_pregunta = document.getElementById("btnAnadirPregunta");

    if (boton_anadir_pregunta) {
        // Pasamos la función por referencia, no anónima
        boton_anadir_pregunta.addEventListener("click", agregarBloquePregunta);
    }

    if (formulario_creacion) {
        // Pasamos la función por referencia, no anónima
        formulario_creacion.addEventListener("submit", procesarFormularioExamen);
    }

    const contenedor_preguntas = document.getElementById("contenedorPreguntas");
    if (contenedor_preguntas) {
        agregarBloquePregunta();
    }
}

// Llamada directa al cargar el script en el HTML
inicializarCrearExamen();