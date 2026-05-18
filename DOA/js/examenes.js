/*
    Pantalla: Exámenes
*/

let examenesActuales = [];
let filtroActivo = "todos";

const idAsignatura = window.obtenerAsignaturaSeleccionada();
const asignatura = window.DOA_ASIGNATURAS[idAsignatura] || window.DOA_ASIGNATURAS.matematicas;

examenesActuales = window.obtenerExamenesAsignatura(idAsignatura);

cargarCabeceraAsignatura(asignatura);
cargarResumenExamenes(examenesActuales);
cargarExamenDestacado(examenesActuales);
prepararFiltrosExamenes();
renderizarExamenes();

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

    const completados = examenes.filter(function (examen) {
        return examen.estadoFiltro === "cerrado";
    }).length;

    const proximo = examenes.find(function (examen) {
        return examen.estadoFiltro === "proximo" || examen.estadoFiltro === "abierto";
    });

    ponerTexto("totalExamenesAbiertos", abiertos);
    ponerTexto("totalExamenesRealizados", completados);
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
            window.guardarExamenSeleccionado(examenDestacado.id);
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
            window.guardarExamenSeleccionado(examen.id);
        });

        contenedor.appendChild(fila);
    });
}