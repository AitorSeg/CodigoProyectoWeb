const boton_prueba = document.getElementById("trialBtn");

boton_prueba.addEventListener("click", function () {
  const sesion_iniciada = localStorage.getItem("gtiSesionIniciada");

  if (sesion_iniciada === "true") {
    window.location.href = "../DOA/elegir_perfil.php";
    return;
  }

  localStorage.setItem("paginaAnterior", "../DOA/elegir_perfil.php");
  window.location.href = "login.php";
});