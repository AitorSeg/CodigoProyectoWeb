/*
    Pantalla: Recursos del alumno
*/

const btn_navegacion_movil = document.getElementById("btnNavegacionMovil");
const sidebar_biblioteca = document.getElementById("menuCarpetasGeneral");
const btn_filtrar_movil = document.getElementById("btnFiltrarMovil");
const contenedor_filtros = document.getElementById("contenedorFiltros");

btn_navegacion_movil.addEventListener("click", function () {
  sidebar_biblioteca.classList.toggle("mostrar-movil");
  contenedor_filtros.classList.remove("mostrar-movil");
});

btn_filtrar_movil.addEventListener("click", function () {
  contenedor_filtros.classList.toggle("mostrar-movil");
  sidebar_biblioteca.classList.remove("mostrar-movil");
});