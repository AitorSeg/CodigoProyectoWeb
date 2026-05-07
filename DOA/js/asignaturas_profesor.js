document.addEventListener("DOMContentLoaded", function () {
    const enlacesAsignatura = document.querySelectorAll("[data-asignatura]");

    enlacesAsignatura.forEach(function (enlace) {
        enlace.addEventListener("click", function () {
            const asignatura = enlace.getAttribute("data-asignatura");

            if (asignatura) {
                localStorage.setItem("doaAsignaturaSeleccionada", asignatura);
            }
        });
    });
});