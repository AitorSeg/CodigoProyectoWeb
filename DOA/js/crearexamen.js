/*
    Pantalla: Crear Examen
*/

document.addEventListener("DOMContentLoaded", function () {
    const formulario = document.getElementById("formularioCrearExamen");
    const contenedorPreguntas = document.getElementById("contenedorPreguntas");
    const btnAnadirPregunta = document.getElementById("btnAnadirPregunta");
    let contadorPreguntas = 0;

    // Inyectar al menos una pregunta al iniciar
    agregarBloquePregunta();

    // Evento para añadir más preguntas
    if (btnAnadirPregunta) {
        btnAnadirPregunta.addEventListener("click", agregarBloquePregunta);
    }

    function agregarBloquePregunta() {
        contadorPreguntas++;
        const idPregunta = `preg_${contadorPreguntas}`;

        const htmlPregunta = `
            <div class="bloque-pregunta-crear" id="bloque_${idPregunta}">
                <div class="cabecera-pregunta">
                    <h3>Pregunta ${contadorPreguntas}</h3>
                    ${contadorPreguntas > 1 ? `<button type="button" class="btn-eliminar-pregunta" onclick="eliminarPregunta('bloque_${idPregunta}')">Eliminar</button>` : ''}
                </div>
                
                <div class="grupo-campo-formulario">
                    <label>Enunciado de la pregunta</label>
                    <textarea class="input-enunciado" rows="2" placeholder="Ej: ¿Qué representa el límite de una función en un punto?" required></textarea>
                </div>

                <div class="opciones-contenedor">
                    <label style="font-size:12px; color:var(--color-muted); margin-bottom:8px; display:block;">Opciones (Selecciona la correcta)</label>
                    
                    <div class="fila-opcion-crear">
                        <input type="radio" name="correcta_${idPregunta}" value="a" required>
                        <input type="text" class="input-opcion-a" placeholder="Opción A" required>
                    </div>
                    <div class="fila-opcion-crear">
                        <input type="radio" name="correcta_${idPregunta}" value="b">
                        <input type="text" class="input-opcion-b" placeholder="Opción B" required>
                    </div>
                    <div class="fila-opcion-crear">
                        <input type="radio" name="correcta_${idPregunta}" value="c">
                        <input type="text" class="input-opcion-c" placeholder="Opción C" required>
                    </div>
                </div>

                <div class="grupo-campo-formulario" style="margin-top:16px; margin-bottom:0;">
                    <label>Explicación de la respuesta (Retroalimentación)</label>
                    <input type="text" class="input-explicacion" placeholder="Ej: El límite describe el valor al que se aproxima..." required>
                </div>
            </div>
        `;

        contenedorPreguntas.insertAdjacentHTML("beforeend", htmlPregunta);
    }

    // Hacer global la función eliminar para el botón onclick
    window.eliminarPregunta = function(idBloque) {
        const bloque = document.getElementById(idBloque);
        if (bloque) bloque.remove();
    };

    // AL ENVIAR EL FORMULARIO
    if (formulario) {
        formulario.addEventListener("submit", function (evento) {
            evento.preventDefault();

            // 1. Recoger datos generales
            const nombre = document.getElementById("inputNombre").value;
            const asignaturaSelect = document.getElementById("selectAsignatura");
            const asignaturaClave = asignaturaSelect.value;
            const asignaturaTexto = asignaturaSelect.options[asignaturaSelect.selectedIndex].text;
            const unidad = document.getElementById("inputUnidad").value;
            const descripcion = document.getElementById("inputDescripcion").value;
            const duracion = document.getElementById("inputDuracion").value;
            const intentos = document.getElementById("inputIntentos").value;

            // 2. RECOGER PREGUNTAS (Magia pura)
            const arrayPreguntas = [];
            const bloques = document.querySelectorAll(".bloque-pregunta-crear");

            bloques.forEach((bloque, index) => {
                const enunciado = bloque.querySelector(".input-enunciado").value;
                const explicacion = bloque.querySelector(".input-explicacion").value;

                const textoA = bloque.querySelector(".input-opcion-a").value;
                const textoB = bloque.querySelector(".input-opcion-b").value;
                const textoC = bloque.querySelector(".input-opcion-c").value;

                // Buscar qué radio está chequeado
                const radioSeleccionado = bloque.querySelector(`input[type="radio"]:checked`);
                const respuestaCorrecta = radioSeleccionado ? radioSeleccionado.value : "a";

                // Formato idéntico a doa-examenes-datos.js
                arrayPreguntas.push({
                    id: `pregunta_creada_${index + 1}`,
                    enunciado: enunciado,
                    correcta: respuestaCorrecta,
                    explicacion: explicacion,
                    opciones: [
                        { id: "a", texto: textoA },
                        { id: "b", texto: textoB },
                        { id: "c", texto: textoC }
                    ]
                });
            });

            // 3. Fechas simuladas de hoy
            const hoy = new Date();
            const mesesCotos = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
            const fechaFormateada = `${hoy.getDate()} ${mesesCotos[hoy.getMonth()]}, ${hoy.getFullYear()}`;

            // 4. Armar el Examen Completo
            const idNuevoExamen = asignaturaClave + "_creado_" + Date.now();
            const nuevoExamen = {
                id: idNuevoExamen,
                nombre: nombre,
                asignatura: asignaturaTexto,
                asignaturaClave: asignaturaClave,
                unidad: unidad,
                descripcion: descripcion,
                descripcionCorta: unidad,
                fechaCompleta: fechaFormateada,
                fechaCorta: `${hoy.getDate()} ${mesesCotos[hoy.getMonth()]}`,
                fechaApertura: fechaFormateada,
                fechaCierre: "Sin límite",
                duracion: `${duracion} min`,
                preguntas: `${arrayPreguntas.length} preguntas`,
                intentos: `${intentos} intento(s)`,
                estado: "Abierto",
                estadoFiltro: "abierto",
                temas: ["Tema General"],
                preguntasArray: arrayPreguntas // <-- Guardamos el array de preguntas acá
            };

            // 5. Guardar en LocalStorage
            let examenesCreados = JSON.parse(localStorage.getItem("doaExamenesCreados")) || [];
            examenesCreados.unshift(nuevoExamen); // Meterlo de primero
            localStorage.setItem("doaExamenesCreados", JSON.stringify(examenesCreados));

            // 6. Volver a la lista
            window.location.href = "examenes.html";
        });
    }
});