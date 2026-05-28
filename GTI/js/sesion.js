function actualizar_header_sesion() {
  const sesion_iniciada = localStorage.getItem("gtiSesionIniciada");
  const header_actions = document.querySelector(".header-actions");
  const boton_registro = document.querySelector(".register-btn");
  const boton_login = document.querySelector(".login-btn");

  if (sesion_iniciada === "true") {
    boton_registro.style.display = "none";
    boton_login.style.display = "none";

    mostrar_boton_cerrar_sesion(header_actions);
    return;
  }

  boton_registro.style.display = "inline-flex";
  boton_login.style.display = "inline-flex";
  eliminar_boton_cerrar_sesion();
}

function mostrar_boton_cerrar_sesion(header_actions) {
  let boton_cerrar_sesion = document.querySelector(".logout-btn");

  if (boton_cerrar_sesion === null) {
    boton_cerrar_sesion = document.createElement("button");
    boton_cerrar_sesion.textContent = "Cerrar sesión";
    boton_cerrar_sesion.classList.add("login-btn", "logout-btn");

    boton_cerrar_sesion.addEventListener("click", cerrar_sesion_gti);

    header_actions.appendChild(boton_cerrar_sesion);
  }
}

function eliminar_boton_cerrar_sesion() {
  const boton_cerrar_sesion = document.querySelector(".logout-btn");

  if (boton_cerrar_sesion !== null) {
    boton_cerrar_sesion.remove();
  }
}

function cerrar_sesion_gti() {
  localStorage.removeItem("gtiSesionIniciada");
  localStorage.removeItem("gtiUsuarioActual");
  localStorage.removeItem("paginaAnterior");

  window.location.href = obtener_url_inicio_gti();
}

function obtener_url_inicio_gti() {
  const esta_en_gti = window.location.pathname.includes("/GTI/");

  if (esta_en_gti) {
    return "../index.php";
  }

  return "index.php";
}

actualizar_header_sesion();