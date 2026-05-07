function actualizarHeaderSesion() {
    const sesionIniciada = localStorage.getItem("gtiSesionIniciada");

    const headerActions = document.querySelector(".header-actions");
    const botonRegistro = document.querySelector(".register-btn");
    const botonLogin = document.querySelector(".login-btn");

    if (headerActions === null) {
        return;
    }

    if (sesionIniciada === "true") {
        if (botonRegistro !== null) {
            botonRegistro.style.display = "none";
        }

        if (botonLogin !== null) {
            botonLogin.style.display = "none";
        }

        let botonCerrarSesion = document.querySelector(".logout-btn");

        if (botonCerrarSesion === null) {
            botonCerrarSesion = document.createElement("button");
            botonCerrarSesion.textContent = "Cerrar sesión";
            botonCerrarSesion.classList.add("login-btn");
            botonCerrarSesion.classList.add("logout-btn");

            botonCerrarSesion.addEventListener("click", function() {
                localStorage.removeItem("gtiSesionIniciada");
                localStorage.removeItem("gtiUsuarioActual");
                localStorage.removeItem("paginaAnterior");

                window.location.href = "../../../index.html";
            });

            headerActions.appendChild(botonCerrarSesion);
        }
    } else {
        if (botonRegistro !== null) {
            botonRegistro.style.display = "inline-flex";
        }

        if (botonLogin !== null) {
            botonLogin.style.display = "inline-flex";
        }

        const botonCerrarSesion = document.querySelector(".logout-btn");

        if (botonCerrarSesion !== null) {
            botonCerrarSesion.remove();
        }
    }
}

actualizarHeaderSesion();