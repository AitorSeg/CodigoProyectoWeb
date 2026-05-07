/*
    Pantalla: Notificaciones
*/

let notificaciones = [];
let filtroActivo = "todas";
let notificacionSeleccionada = null;

document.addEventListener("DOMContentLoaded", function () {
    notificaciones = cargarNotificacionesDemo();

    prepararFiltrosNotificaciones();
    prepararAccionesNotificaciones();
    renderizarResumenNotificaciones();
    renderizarListadoNotificaciones();

    if (notificaciones.length > 0) {
        seleccionarNotificacion(notificaciones[0].id);
    }
});

function cargarNotificacionesDemo() {
    const estadoLectura = obtenerEstadoLecturaGuardado();

    return [
        {
            id: "notificacion_tarea_derivadas",
            tipo: "tarea",
            tipoTexto: "Tarea",
            titulo: "Nueva tarea disponible",
            resumen: "Se ha publicado la tarea de derivadas en Matemáticas.",
            contenido: "Ya está disponible la tarea de derivadas de la Unidad 03. Revisa el enunciado, entrega la actividad antes de la fecha indicada y consulta los recursos del tema si necesitas repasar.",
            remitente: "Don Pepito",
            fecha: "Hoy",
            asignatura: "matematicas",
            accionTexto: "Ver tareas",
            accionHref: "listado_tareas.html",
            leida: estadoLectura.notificacion_tarea_derivadas === true ? true : false
        },
        {
            id: "notificacion_examen_programacion",
            tipo: "aviso",
            tipoTexto: "Aviso",
            titulo: "Recordatorio de examen",
            resumen: "El examen de Programación II estará disponible próximamente.",
            contenido: "Recuerda que el examen de la Unidad 03 de Programación II estará disponible durante el periodo indicado en la sección de exámenes. Comprueba la duración antes de empezar.",
            remitente: "Profesorado de Programación",
            fecha: "Ayer",
            asignatura: "programacion",
            accionTexto: "Ver exámenes",
            accionHref: "examenes.html",
            leida: estadoLectura.notificacion_examen_programacion === true ? true : false
        },
        {
            id: "notificacion_centro_mantenimiento",
            tipo: "aviso",
            tipoTexto: "Centro",
            titulo: "Mantenimiento programado",
            resumen: "El centro realizará tareas de mantenimiento fuera del horario lectivo.",
            contenido: "Se informa al alumnado de que se realizarán tareas de mantenimiento en los sistemas del centro. Durante ese periodo podrían producirse interrupciones puntuales en algunos servicios.",
            remitente: "Secretaría del centro",
            fecha: "Lun",
            accionTexto: "",
            accionHref: "",
            leida: estadoLectura.notificacion_centro_mantenimiento === true ? true : false
        },
        {
            id: "notificacion_recurso_fisica",
            tipo: "recurso",
            tipoTexto: "Recurso",
            titulo: "Nuevo recurso de Física",
            resumen: "Se ha añadido una guía de repaso a la biblioteca de Física.",
            contenido: "La asignatura de Física tiene disponible un nuevo recurso de repaso relacionado con la unidad actual. Puedes consultarlo desde la sección de recursos de la asignatura.",
            remitente: "Eolande Merriton Mizzi",
            fecha: "Vie",
            asignatura: "fisica",
            accionTexto: "Ver recursos",
            accionHref: "Recursosdoaalumno.html",
            leida: estadoLectura.notificacion_recurso_fisica === true ? true : false
        },
        {
            id: "notificacion_calificacion",
            tipo: "aviso",
            tipoTexto: "Calificación",
            titulo: "Nueva calificación publicada",
            resumen: "Se ha publicado una nueva nota en Matemáticas.",
            contenido: "Tu profesor ha publicado una nueva calificación asociada a una actividad de Matemáticas. Puedes revisarla desde la sección de calificaciones de la asignatura.",
            remitente: "Don Pepito",
            fecha: "Jue",
            asignatura: "matematicas",
            accionTexto: "Ver calificaciones",
            accionHref: "calificaciones.html",
            leida: estadoLectura.notificacion_calificacion === true ? true : false
        }
    ];
}

function obtenerEstadoLecturaGuardado() {
    const datos = localStorage.getItem("doaEstadoLecturaNotificaciones");

    if (datos === null) {
        return {};
    }

    try {
        return JSON.parse(datos);
    } catch (error) {
        return {};
    }
}

function guardarEstadoLectura() {
    const estadoLectura = {};

    notificaciones.forEach(function (notificacion) {
        estadoLectura[notificacion.id] = notificacion.leida;
    });

    localStorage.setItem("doaEstadoLecturaNotificaciones", JSON.stringify(estadoLectura));
}

function prepararFiltrosNotificaciones() {
    const filtros = document.querySelectorAll(".filtro-notificacion");

    filtros.forEach(function (filtro) {
        filtro.addEventListener("click", function () {
            filtroActivo = filtro.dataset.filtro || "todas";

            filtros.forEach(function (boton) {
                boton.classList.toggle("filtro-notificacion--activo", boton === filtro);
            });

            renderizarListadoNotificaciones();
        });
    });
}

function prepararAccionesNotificaciones() {
    const botonLectura = document.getElementById("botonLecturaNotificacion");
    const botonMarcarTodas = document.getElementById("botonMarcarTodas");
    const botonAccion = document.getElementById("botonAccionNotificacion");

    if (botonLectura !== null) {
        botonLectura.addEventListener("click", function () {
            cambiarEstadoLecturaNotificacionSeleccionada();
        });
    }

    if (botonMarcarTodas !== null) {
        botonMarcarTodas.addEventListener("click", function () {
            marcarTodasComoLeidas();
        });
    }

    if (botonAccion !== null) {
        botonAccion.addEventListener("click", function () {
            guardarAsignaturaDeNotificacion(notificacionSeleccionada);
        });
    }
}

function renderizarResumenNotificaciones() {
    const noLeidas = notificaciones.filter(function (notificacion) {
        return !notificacion.leida;
    }).length;

    const tareas = notificaciones.filter(function (notificacion) {
        return notificacion.tipo === "tarea";
    }).length;

    const avisos = notificaciones.filter(function (notificacion) {
        return notificacion.tipo === "aviso";
    }).length;

    ponerTexto("totalNoLeidas", noLeidas);
    ponerTexto("totalTareas", tareas);
    ponerTexto("totalAvisos", avisos);
}

function renderizarListadoNotificaciones() {
    const contenedor = document.getElementById("listaNotificaciones");

    if (contenedor === null) {
        return;
    }

    const filtradas = obtenerNotificacionesFiltradas();

    contenedor.innerHTML = "";

    if (filtradas.length === 0) {
        const mensaje = document.createElement("p");

        mensaje.className = "mensaje-sin-notificaciones";
        mensaje.textContent = "No hay notificaciones con este filtro.";

        contenedor.appendChild(mensaje);
        return;
    }

    filtradas.forEach(function (notificacion) {
        const bloque = document.createElement("article");
        const item = document.createElement("button");

        bloque.className = "bloque-notificacion";

        item.type = "button";
        item.className = "notificacion-item";

        if (!notificacion.leida) {
            item.classList.add("notificacion-item--no-leida");
        }

        if (notificacionSeleccionada !== null && notificacionSeleccionada.id === notificacion.id) {
            item.classList.add("notificacion-item--activa");
            bloque.classList.add("bloque-notificacion--activa");
        }

        item.innerHTML =
            '<div>' +
                '<p class="notificacion-item__titulo">' + notificacion.titulo + '</p>' +
            '</div>' +
            '<span class="notificacion-item__fecha">' + notificacion.fecha + '</span>' +
            '<p class="notificacion-item__resumen">' + notificacion.resumen + '</p>' +
            '<span class="notificacion-item__tipo">' + notificacion.tipoTexto + '</span>';

        item.addEventListener("click", function () {
            seleccionarNotificacion(notificacion.id);
        });

        bloque.appendChild(item);

        if (notificacionSeleccionada !== null && notificacionSeleccionada.id === notificacion.id) {
            const detalleMovil = document.createElement("div");

            detalleMovil.className = "detalle-notificacion-movil";
            detalleMovil.innerHTML = crearDetalleMovilNotificacion(notificacion);

            const enlaceMovil = detalleMovil.querySelector(".boton-accion-notificacion-movil");

            if (enlaceMovil !== null) {
                enlaceMovil.addEventListener("click", function () {
                    guardarAsignaturaDeNotificacion(notificacion);
                });
            }

            bloque.appendChild(detalleMovil);
        }

        contenedor.appendChild(bloque);
    });
}

function crearDetalleMovilNotificacion(notificacion) {
    let botonAccion = "";

    if (notificacion.accionHref !== "") {
        botonAccion =
            '<a href="' + notificacion.accionHref + '" class="boton-accion-notificacion boton-accion-notificacion-movil">' +
                notificacion.accionTexto +
            '</a>';
    }

    return (
        '<div class="detalle-notificacion-movil__cabecera">' +
            '<span class="etiqueta-notificacion">' + notificacion.tipoTexto + '</span>' +
            '<p>' + notificacion.remitente + " · " + notificacion.fecha + '</p>' +
        '</div>' +
        '<p class="detalle-notificacion-movil__texto">' + notificacion.contenido + '</p>' +
        '<div class="detalle-notificacion-movil__acciones">' +
            botonAccion +
        '</div>'
    );
}

function obtenerNotificacionesFiltradas() {
    return notificaciones.filter(function (notificacion) {
        if (filtroActivo === "todas") {
            return true;
        }

        if (filtroActivo === "no-leidas") {
            return !notificacion.leida;
        }

        return notificacion.tipo === filtroActivo;
    });
}

function seleccionarNotificacion(idNotificacion) {
    const notificacion = notificaciones.find(function (item) {
        return item.id === idNotificacion;
    });

    if (notificacion === undefined) {
        return;
    }

    notificacionSeleccionada = notificacion;

    if (!notificacion.leida) {
        notificacion.leida = true;
        guardarEstadoLectura();
    }

    cargarDetalleNotificacion(notificacion);
    renderizarResumenNotificaciones();
    renderizarListadoNotificaciones();
}

function cargarDetalleNotificacion(notificacion) {
    ponerTexto("detalleTipoNotificacion", notificacion.tipoTexto);
    ponerTexto("detalleTituloNotificacion", notificacion.titulo);
    ponerTexto("detalleMetaNotificacion", notificacion.remitente + " · " + notificacion.fecha);
    ponerTexto("detalleTextoNotificacion", notificacion.contenido);

    const botonLectura = document.getElementById("botonLecturaNotificacion");
    const botonAccion = document.getElementById("botonAccionNotificacion");

    if (botonLectura !== null) {
        botonLectura.textContent = notificacion.leida ? "Marcar como no leída" : "Marcar como leída";
    }

    if (botonAccion !== null) {
        if (notificacion.accionHref !== "") {
            botonAccion.classList.remove("hidden");
            botonAccion.href = notificacion.accionHref;
            botonAccion.textContent = notificacion.accionTexto;
        } else {
            botonAccion.classList.add("hidden");
            botonAccion.removeAttribute("href");
        }
    }
}

function cambiarEstadoLecturaNotificacionSeleccionada() {
    if (notificacionSeleccionada === null) {
        return;
    }

    notificacionSeleccionada.leida = !notificacionSeleccionada.leida;

    guardarEstadoLectura();
    renderizarResumenNotificaciones();
    renderizarListadoNotificaciones();
    cargarDetalleNotificacion(notificacionSeleccionada);
}

function marcarTodasComoLeidas() {
    notificaciones.forEach(function (notificacion) {
        notificacion.leida = true;
    });

    guardarEstadoLectura();
    renderizarResumenNotificaciones();
    renderizarListadoNotificaciones();

    if (notificacionSeleccionada !== null) {
        cargarDetalleNotificacion(notificacionSeleccionada);
    }
}

function guardarAsignaturaDeNotificacion(notificacion) {
    if (notificacion === null || !notificacion.asignatura) {
        return;
    }

    if (typeof window.guardarAsignaturaSeleccionada === "function") {
        window.guardarAsignaturaSeleccionada(notificacion.asignatura);
        return;
    }

    localStorage.setItem("doaAsignaturaSeleccionada", notificacion.asignatura);
}

function ponerTexto(idElemento, texto) {
    const elemento = document.getElementById(idElemento);

    if (elemento !== null) {
        elemento.textContent = texto;
    }
}