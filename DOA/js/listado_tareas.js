document.addEventListener("DOMContentLoaded", function () {
    const filtroTipo = document.getElementById("filtroTipo");
    const filtroEstado = document.getElementById("filtroEstado");
    const ordenTareas = document.getElementById("ordenTareas");
    const tablaTareas = document.querySelector(".tabla-tareas");

    if (!filtroTipo || !filtroEstado || !ordenTareas || !tablaTareas) {
        return;
    }

    const filasOriginales = Array.from(tablaTareas.querySelectorAll(".fila-tarea"));

    function actualizarListado() {
        const tipoSeleccionado = filtroTipo.value;
        const estadoSeleccionado = filtroEstado.value;
        const ordenSeleccionado = ordenTareas.value;

        let filasFiltradas = filasOriginales.filter(function (fila) {
            const tipo = fila.dataset.tipo;
            const estado = fila.dataset.estado;

            const coincideTipo =
                tipoSeleccionado === "todas" || tipo === tipoSeleccionado;

            const coincideEstado =
                estadoSeleccionado === "todos" || estado === estadoSeleccionado;

            return coincideTipo && coincideEstado;
        });

        if (ordenSeleccionado === "cercana") {
            filasFiltradas.sort(function (a, b) {
                return new Date(a.dataset.fechaEntrega) - new Date(b.dataset.fechaEntrega);
            });
        }

        if (ordenSeleccionado === "tarde") {
            filasFiltradas.sort(function (a, b) {
                return new Date(b.dataset.fechaEntrega) - new Date(a.dataset.fechaEntrega);
            });
        }

        filasOriginales.forEach(function (fila) {
            fila.remove();
        });

        filasFiltradas.forEach(function (fila) {
            tablaTareas.appendChild(fila);
            fila.hidden = false;
        });
    }

    filtroTipo.addEventListener("change", actualizarListado);
    filtroEstado.addEventListener("change", actualizarListado);
    ordenTareas.addEventListener("change", actualizarListado);
});