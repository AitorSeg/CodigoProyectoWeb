const enlaces_asignatura = document.querySelectorAll("[data-asignatura]");

enlaces_asignatura.forEach(function (enlace) {
  enlace.addEventListener("click", function () {
    localStorage.setItem("doaAsignaturaSeleccionada", enlace.dataset.asignatura);
  });
});