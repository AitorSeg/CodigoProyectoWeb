/*
    Pantalla: Elegir perfil de prueba
*/

const filtros = document.querySelectorAll(".filtro-perfil");
const gruposUsuarios = document.querySelectorAll(".grupo-usuarios-demo");
const usuarios = document.querySelectorAll(".usuario-demo");

const formLogin = document.getElementById("formLoginDemo");
const inputCorreo = document.getElementById("correoDemo");
const inputPassword = document.getElementById("passwordDemo");
const botonMostrarPassword = document.getElementById("botonMostrarPassword");
const mensajeError = document.getElementById("mensajeErrorLogin");

const perfilNombre = document.getElementById("perfilNombre");
const perfilDni = document.getElementById("perfilDni");
const perfilRol = document.getElementById("perfilRol");

const cajaLogin = document.querySelector(".caja-demo--login");

filtros.forEach(function (filtro) {
  filtro.addEventListener("click", function () {
    cambiarFiltro(filtro);
  });
});

usuarios.forEach(function (usuario) {
  usuario.addEventListener("click", function () {
    seleccionarUsuario(usuario);
  });
});

botonMostrarPassword.addEventListener("click", function () {
  cambiarVisibilidadPassword();
});

formLogin.addEventListener("submit", function (evento) {
  evento.preventDefault();
  validarAcceso();
});

function cambiarFiltro(filtroSeleccionado) {
  const filtro = filtroSeleccionado.dataset.filtro;

  filtros.forEach(function (filtro) {
    filtro.classList.remove("filtro-perfil--activo");
  });

  filtroSeleccionado.classList.add("filtro-perfil--activo");

  gruposUsuarios.forEach(function (grupo) {
    const grupoTipo = grupo.dataset.grupo;

    if (filtro === "todos" || filtro === grupoTipo) {
      grupo.classList.remove("oculto");
    } else {
      grupo.classList.add("oculto");
    }
  });
}

function seleccionarUsuario(usuarioSeleccionado) {
  usuarios.forEach(function (usuario) {
    usuario.classList.remove("usuario-demo--activo");
  });

  usuarioSeleccionado.classList.add("usuario-demo--activo");

  inputCorreo.value = usuarioSeleccionado.dataset.email;
  inputPassword.value = usuarioSeleccionado.dataset.password;

  perfilNombre.textContent = usuarioSeleccionado.dataset.nombre;
  perfilDni.textContent = usuarioSeleccionado.dataset.dni;
  perfilRol.textContent = usuarioSeleccionado.dataset.rol;

  ocultarError();
  bajarAlLoginEnMovil();
}

function cambiarVisibilidadPassword() {
  if (inputPassword.type === "password") {
    inputPassword.type = "text";
    botonMostrarPassword.textContent = "Ocultar";
  } else {
    inputPassword.type = "password";
    botonMostrarPassword.textContent = "Mostrar";
  }
}

function validarAcceso() {
  const correo = inputCorreo.value.trim().toLowerCase();
  const password = inputPassword.value.trim();

  if (correo === "" || password === "") {
    mostrarError("Introduce el correo electrónico y la contraseña.");
    return;
  }

  const usuarioEncontrado = buscarUsuario(correo, password);

  if (usuarioEncontrado === null) {
    mostrarError("Las credenciales no pertenecen a ningún usuario de prueba.");
    return;
  }

  sessionStorage.setItem("usuarioDemoDOA", JSON.stringify(usuarioEncontrado));

  if (usuarioEncontrado.rol === "profesor") {
    window.location.href = "panel_profesor.php";
    return;
  }

  if (
    usuarioEncontrado.rol === "secretaria") {
    window.location.href = "panel_secretaria.php";
    return;
  }

  window.location.href = "panel_principal.php";
}

function buscarUsuario(correo, password) {
  for (let i = 0; i < usuarios.length; i++) {
    const usuario = usuarios[i];

    if (
      usuario.dataset.email.toLowerCase() === correo &&
      usuario.dataset.password === password
    ) {
      return {
        nombre: usuario.dataset.nombre,
        dni: usuario.dataset.dni,
        email: usuario.dataset.email,
        rol: usuario.dataset.rol,
        rol_texto: usuario.dataset.rolTexto,
      };
    }
  }

  return null;
}

function mostrarError(texto) {
  mensajeError.textContent = texto;
  mensajeError.classList.remove("oculto");
}

function ocultarError() {
  mensajeError.textContent = "";
  mensajeError.classList.add("oculto");
}

function bajarAlLoginEnMovil() {
  const esPantallaPequena = window.matchMedia("(max-width: 900px)").matches;

  if (!esPantallaPequena || cajaLogin === null) {
    return;
  }

  cajaLogin.scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
}
