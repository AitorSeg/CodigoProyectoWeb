/*
    DOA - Elegir perfil de prueba
*/

document.addEventListener("DOMContentLoaded", function () {
    const tabButtons = document.querySelectorAll(".demo-tab");
    const userGroups = document.querySelectorAll(".demo-user-group");
    const userButtons = document.querySelectorAll(".demo-user-option");

    const formLogin = document.getElementById("formLoginDemo");
    const inputPerfilId = document.getElementById("perfilId");
    const inputEmail = document.getElementById("email");
    const inputPassword = document.getElementById("password");
    const botonMostrarPassword = document.getElementById("botonMostrarPassword");
    const mensajeError = document.getElementById("mensajeError");

    const tarjetaLogin = document.querySelector(".demo-form-card");

    const perfilNombre = document.getElementById("perfilNombre");
    const perfilDni = document.getElementById("perfilDni");
    const perfilRol = document.getElementById("perfilRol");

    function filtrarUsuarios(tipo) {
        userGroups.forEach(function (group) {
            if (tipo === "todos" || group.dataset.grupo === tipo) {
                group.classList.remove("hidden");
            } else {
                group.classList.add("hidden");
            }
        });
    }

    function activarTab(tabActiva) {
        tabButtons.forEach(function (tab) {
            tab.classList.remove("demo-tab-active");
        });

        tabActiva.classList.add("demo-tab-active");
    }

    function seleccionarUsuario(botonUsuario) {
        userButtons.forEach(function (button) {
            button.classList.remove("demo-user-option-active");
        });

        botonUsuario.classList.add("demo-user-option-active");

        inputPerfilId.value = botonUsuario.dataset.id;
        inputEmail.value = botonUsuario.dataset.email;
        inputPassword.value = botonUsuario.dataset.password;

        perfilNombre.textContent = botonUsuario.dataset.nombre;
        perfilDni.textContent = botonUsuario.dataset.dni;
        perfilRol.textContent = botonUsuario.dataset.rol;

        ocultarError();
        bajarAlLoginEnMovil();
    }

    function mostrarError(texto) {
        mensajeError.textContent = texto;
        mensajeError.classList.remove("hidden");
    }

    function ocultarError() {
        mensajeError.textContent = "";
        mensajeError.classList.add("hidden");
    }

    function bajarAlLoginEnMovil() {
        const esMovil = window.matchMedia("(max-width: 900px)").matches;

        if (!esMovil || tarjetaLogin === null) {
            return;
        }

        tarjetaLogin.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }

    tabButtons.forEach(function (tab) {
        tab.addEventListener("click", function () {
            activarTab(tab);
            filtrarUsuarios(tab.dataset.filtro);
        });
    });

    userButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            seleccionarUsuario(button);
        });
    });

    if (botonMostrarPassword !== null) {
        botonMostrarPassword.addEventListener("click", function () {
            const passwordEstaOculta = inputPassword.type === "password";

            inputPassword.type = passwordEstaOculta ? "text" : "password";
            botonMostrarPassword.textContent = passwordEstaOculta ? "Ocultar" : "Mostrar";
        });
    }

    formLogin.addEventListener("submit", function (event) {
        const email = inputEmail.value.trim();
        const password = inputPassword.value.trim();

        if (email === "" || password === "") {
            event.preventDefault();
            mostrarError("Introduce el correo electrónico y la contraseña.");
        }
    });

    filtrarUsuarios("todos");
});