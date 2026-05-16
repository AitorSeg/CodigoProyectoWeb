cargar_usuario_header();

function cargar_usuario_header() {
  const usuario_demo = JSON.parse(sessionStorage.getItem("usuarioDemoDOA"));

  document.getElementById("nombreUsuarioHeader").textContent =
    usuario_demo.nombre;
  document.getElementById("rolUsuarioHeader").textContent =
    usuario_demo.rol_texto;
}
