/*
    Pantalla: Exámenes
*/

let examenesActuales = [];
let filtroActivo = "todos";

document.addEventListener("DOMContentLoaded", function () {
    const idAsignatura = window.obtenerAsignaturaSeleccionada();
    const asignatura = window.DOA_ASIGNATURAS[idAsignatura] || window.DOA_ASIGNATURAS.matematicas;

    examenesActuales = obtenerExamenesAsignatura(idAsignatura);

    cargarCabeceraAsignatura(asignatura);
    cargarResumenExamenes(examenesActuales);
    cargarExamenDestacado(examenesActuales);
    prepararFiltrosExamenes();
    renderizarExamenes();
});

function ponerTexto(idElemento, texto) {
    const elemento = document.getElementById(idElemento);

    if (elemento !== null) {
        elemento.textContent = texto;
    }
}

function cargarCabeceraAsignatura(asignatura) {
    document.title = "Exámenes · " + asignatura.nombre + " | DOA";

    ponerTexto("tituloAsignatura", asignatura.nombre);
    ponerTexto("profesorAsignatura", asignatura.profesor);
    ponerTexto("unidadActualTextoAsignatura", asignatura.unidadActualTexto);
}

function cargarResumenExamenes(examenes) {
    const abiertos = examenes.filter(function (examen) {
        return examen.estadoFiltro === "abierto";
    }).length;

    const realizados = examenes.filter(function (examen) {
        return examen.estadoFiltro === "cerrado";
    }).length;

    const proximo = examenes.find(function (examen) {
        return examen.estadoFiltro === "proximo" || examen.estadoFiltro === "abierto";
    });

    ponerTexto("totalExamenesAbiertos", abiertos);
    ponerTexto("totalExamenesRealizados", realizados);
    ponerTexto("proximoExamenTexto", proximo ? proximo.fechaCorta : "---");
}

function cargarExamenDestacado(examenes) {
    const examenAbierto = examenes.find(function (examen) {
        return examen.estadoFiltro === "abierto";
    });

    const examenDestacado = examenAbierto || examenes[0];

    if (!examenDestacado) {
        return;
    }

    const tarjeta = document.getElementById("examenDestacado");
    const boton = document.getElementById("botonExamenDestacado");

    ponerTexto("tituloExamenDestacado", examenDestacado.nombre);
    ponerTexto("descripcionExamenDestacado", examenDestacado.descripcion);
    ponerTexto("fechaLimiteExamenDestacado", examenDestacado.fechaCompleta);

    if (tarjeta !== null) {
        tarjeta.dataset.estado = examenDestacado.estadoFiltro;
    }

    if (boton !== null) {
        boton.dataset.examen = examenDestacado.id;
        boton.textContent = examenDestacado.estadoFiltro === "abierto" ? "Entrar" : "Ver detalles";

        boton.addEventListener("click", function () {
            guardarExamenSeleccionado(examenDestacado.id);
        });
    }
}

function prepararFiltrosExamenes() {
    const filtros = document.querySelectorAll(".filtro-examen");

    filtros.forEach(function (filtro) {
        filtro.addEventListener("click", function () {
            filtroActivo = filtro.dataset.filtro || "todos";

            filtros.forEach(function (boton) {
                boton.classList.toggle("filtro-examen--activo", boton === filtro);
            });

            renderizarExamenes();
        });
    });
}

function renderizarExamenes() {
    const contenedor = document.getElementById("listadoExamenes");

    if (contenedor === null) {
        return;
    }

    const examenesFiltrados = examenesActuales.filter(function (examen) {
        return filtroActivo === "todos" || examen.estadoFiltro === filtroActivo;
    });

    contenedor.innerHTML = "";

    if (examenesFiltrados.length === 0) {
        const mensaje = document.createElement("p");

        mensaje.className = "mensaje-sin-examenes";
        mensaje.textContent = "No hay exámenes con este filtro.";

        contenedor.appendChild(mensaje);
        return;
    }

    examenesFiltrados.forEach(function (examen) {
        const fila = document.createElement("article");
        const esAbierto = examen.estadoFiltro === "abierto";
        const textoAccion = esAbierto ? "Entrar" : "Ver detalles";
        const claseAccion = esAbierto
            ? "fila-examen__accion fila-examen__accion--principal"
            : "fila-examen__accion";

        fila.className = "fila-examen";

        fila.innerHTML =
            '<div class="fila-examen__nombre">' +
                '<strong>' + examen.nombre + '</strong>' +
                '<span>' + examen.descripcionCorta + '</span>' +
            '</div>' +
            '<p class="fila-examen__fecha" data-duracion="' + examen.duracion + '">' + examen.fechaCompleta + '</p>' +
            '<p class="fila-examen__duracion">' + examen.duracion + '</p>' +
            '<p class="fila-examen__estado">' +
                '<span class="etiqueta-examen etiqueta-examen--' + examen.estadoFiltro + '">' + examen.estado + '</span>' +
            '</p>' +
            '<a href="detalle_examen.html" class="' + claseAccion + '" data-examen="' + examen.id + '">' +
                textoAccion +
            '</a>';

        const enlace = fila.querySelector(".fila-examen__accion");

        enlace.addEventListener("click", function () {
            guardarExamenSeleccionado(examen.id);
        });

        contenedor.appendChild(fila);
    });
}

function guardarExamenSeleccionado(idExamen) {
    localStorage.setItem("doaExamenSeleccionado", idExamen);
}

function obtenerExamenesAsignatura(idAsignatura) {
    const datos = {
        matematicas: [
            {
                id: "matematicas_parcial_01",
                nombre: "Parcial 01",
                descripcion: "Examen tipo test sobre límites, derivadas e introducción a integrales.",
                descripcionCorta: "Límites y derivadas",
                fechaCompleta: "15 Nov, 2026",
                fechaCorta: "15 Nov",
                duracion: "45 min",
                estado: "Abierto",
                estadoFiltro: "abierto"
            },
            {
                id: "matematicas_quiz_derivadas",
                nombre: "Quiz de derivadas",
                descripcion: "Cuestionario breve de repaso sobre derivadas básicas.",
                descripcionCorta: "Cuestionario corregido",
                fechaCompleta: "12 Oct, 2026",
                fechaCorta: "12 Oct",
                duracion: "20 min",
                estado: "Cerrado",
                estadoFiltro: "cerrado"
            },
            {
                id: "matematicas_parcial_02",
                nombre: "Parcial 02",
                descripcion: "Segundo examen parcial de la asignatura.",
                descripcionCorta: "Integrales y aplicaciones",
                fechaCompleta: "28 Nov, 2026",
                fechaCorta: "28 Nov",
                duracion: "50 min",
                estado: "Próximo",
                estadoFiltro: "proximo"
            }
        ],

        programacion: [
            {
                id: "programacion_recursividad",
                nombre: "Examen unidad 03",
                descripcion: "Examen tipo test sobre recursividad, caso base y llamadas recursivas.",
                descripcionCorta: "Recursividad",
                fechaCompleta: "15 Nov, 2026",
                fechaCorta: "15 Nov",
                duracion: "35 min",
                estado: "Abierto",
                estadoFiltro: "abierto"
            },
            {
                id: "programacion_arrays",
                nombre: "Quiz arrays",
                descripcion: "Cuestionario de repaso de arrays y estructuras básicas.",
                descripcionCorta: "Arrays y bucles",
                fechaCompleta: "10 Oct, 2026",
                fechaCorta: "10 Oct",
                duracion: "20 min",
                estado: "Cerrado",
                estadoFiltro: "cerrado"
            },
            {
                id: "programacion_grafos",
                nombre: "Examen grafos",
                descripcion: "Examen próximo sobre grafos y recorridos básicos.",
                descripcionCorta: "Grafos",
                fechaCompleta: "22 Nov, 2026",
                fechaCorta: "22 Nov",
                duracion: "45 min",
                estado: "Próximo",
                estadoFiltro: "proximo"
            }
        ],

        fisica: [
            {
                id: "fisica_cinematica",
                nombre: "Control de cinemática",
                descripcion: "Examen tipo test sobre movimiento, velocidad y aceleración.",
                descripcionCorta: "Movimiento y fuerzas",
                fechaCompleta: "19 Nov, 2026",
                fechaCorta: "19 Nov",
                duracion: "40 min",
                estado: "Abierto",
                estadoFiltro: "abierto"
            },
            {
                id: "fisica_fuerzas",
                nombre: "Cuestionario de fuerzas",
                descripcion: "Cuestionario corregido sobre fuerzas y leyes básicas.",
                descripcionCorta: "Fuerzas",
                fechaCompleta: "11 Oct, 2026",
                fechaCorta: "11 Oct",
                duracion: "25 min",
                estado: "Cerrado",
                estadoFiltro: "cerrado"
            }
        ]
    };

    return datos[idAsignatura] || datos.matematicas;
}