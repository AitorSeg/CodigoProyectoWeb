/*
  Pantalla: Elegir perfil de prueba
*/

const filtros = document.querySelectorAll(".filtro-perfil");
const grupos_usuarios = document.querySelectorAll(".grupo-usuarios-demo");
const usuarios = document.querySelectorAll(".usuario-demo");

const input_correo = document.getElementById("correoDemo");
const input_password = document.getElementById("passwordDemo");
const boton_mostrar_password = document.getElementById("botonMostrarPassword");
const mensaje_error = document.getElementById("mensajeErrorLogin");

const perfil_nombre = document.getElementById("perfilNombre");
const perfil_dni = document.getElementById("perfilDni");
const perfil_rol = document.getElementById("perfilRol");

const caja_login = document.querySelector(".caja-demo--login");

const banner_eleccion_perfil = document.getElementById("bannerEleccionPerfil");
const boton_acceso_demo = document.getElementById("botonAccesoDemo");

filtros.forEach(function (filtro) {
  filtro.addEventListener("click", function () {
    cambiar_filtro(filtro);
  });
});

usuarios.forEach(function (usuario) {
  usuario.addEventListener("click", function () {
    seleccionar_usuario(usuario);
  });
});

boton_mostrar_password.addEventListener("click", function () {
  cambiar_visibilidad_password();
});

function cambiar_filtro(filtro_seleccionado) {
  const filtro = filtro_seleccionado.dataset.filtro;

  filtros.forEach(function (filtro) {
    filtro.classList.remove("filtro-perfil--activo");
  });

  filtro_seleccionado.classList.add("filtro-perfil--activo");

  grupos_usuarios.forEach(function (grupo) {
    const grupo_tipo = grupo.dataset.grupo;

    if (filtro === "todos" || filtro === grupo_tipo) {
      grupo.classList.remove("oculto");
    } else {
      grupo.classList.add("oculto");
    }
  });
}

function seleccionar_usuario(usuario_seleccionado) {
  usuarios.forEach(function (usuario) {
    usuario.classList.remove("usuario-demo--activo");
  });

  usuario_seleccionado.classList.add("usuario-demo--activo");

  input_correo.value = usuario_seleccionado.dataset.email;
  input_password.value = usuario_seleccionado.dataset.password;
  input_password.type = "text";
  boton_mostrar_password.textContent = "Ocultar";

  perfil_nombre.textContent = usuario_seleccionado.dataset.nombre;
  perfil_dni.textContent = usuario_seleccionado.dataset.dni;
  perfil_rol.textContent = usuario_seleccionado.dataset.rolTexto;

  banner_eleccion_perfil.classList.add("banner-eleccion-perfil--seleccionado");

  banner_eleccion_perfil.querySelector("h2").textContent =
    "Perfil seleccionado";
  banner_eleccion_perfil.querySelector("p").textContent =
    "Las credenciales se han rellenado automáticamente. Ya puedes acceder a la demo de DOA.";

  boton_acceso_demo.classList.remove("boton-acceso-demo--secundario");
  boton_acceso_demo.classList.add("boton-acceso-demo--principal");

  ocultar_error();
  bajar_al_login_en_movil();
}

function cambiar_visibilidad_password() {
  if (input_password.type === "password") {
    input_password.type = "text";
    boton_mostrar_password.textContent = "Ocultar";
  } else {
    input_password.type = "password";
    boton_mostrar_password.textContent = "Mostrar";
  }
}

function ocultar_error() {
  mensaje_error.textContent = "";
  mensaje_error.classList.add("oculto");
}

function bajar_al_login_en_movil() {
  const es_pantalla_pequena = window.matchMedia("(max-width: 900px)").matches;

  if (!es_pantalla_pequena) {
    return;
  }

  caja_login.scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
}
