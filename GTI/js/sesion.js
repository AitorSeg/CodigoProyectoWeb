// Función que actualiza los elementos del header según el estado de sesión
// Usa `localStorage` para comprobar si el usuario ha iniciado sesión
function actualizarHeaderSesion() {
    // Valor guardado en localStorage: "true" cuando hay sesión iniciada
    const sesionIniciada = localStorage.getItem("gtiSesionIniciada");

    // Elementos del header que se muestran/ocultan según la sesión
    const headerActions = document.querySelector(".header-actions");
    const botonRegistro = document.querySelector(".register-btn");
    const botonLogin = document.querySelector(".login-btn");

    // Si no existe el contenedor de acciones del header, no hacemos nada
    if (headerActions === null) {
        return;
    }

    // Si la sesión está iniciada, ocultamos botones de registro/login
    if (sesionIniciada === "true") {
        if (botonRegistro !== null) {
            // Oculta el botón de registro para usuarios ya logueados
            botonRegistro.style.display = "none";
        }

        if (botonLogin !== null) {
            // Oculta el botón de login para usuarios ya logueados
            botonLogin.style.display = "none";
        }

        // Buscamos si ya existe un botón de cerrar sesión
        let botonCerrarSesion = document.querySelector(".logout-btn");

        // Si no existe, lo creamos y lo añadimos al header
        if (botonCerrarSesion === null) {
            botonCerrarSesion = document.createElement("button");
            botonCerrarSesion.textContent = "Cerrar sesión";
            // Reutilizamos la clase `login-btn` para estilos consistentes
            botonCerrarSesion.classList.add("login-btn");
            botonCerrarSesion.classList.add("logout-btn");

            // Al hacer click, eliminamos los datos de sesión y volvemos al inicio
            botonCerrarSesion.addEventListener("click", function() {
                // Limpiamos las claves relacionadas con la sesión
                localStorage.removeItem("gtiSesionIniciada");
                localStorage.removeItem("gtiUsuarioActual");
                localStorage.removeItem("paginaAnterior");

                // Redirige al index del proyecto (ruta relativa)
                window.location.href = "../../../index.html";
            });

            headerActions.appendChild(botonCerrarSesion);
        }
    } else {
        // Si no hay sesión iniciada, mostramos los botones de registro/login
        if (botonRegistro !== null) {
            botonRegistro.style.display = "inline-flex";
        }

        if (botonLogin !== null) {
            botonLogin.style.display = "inline-flex";
        }

        // Si hay un botón de cerrar sesión visible, lo eliminamos
        const botonCerrarSesion = document.querySelector(".logout-btn");

        if (botonCerrarSesion !== null) {
            botonCerrarSesion.remove();
        }
    }
}

// Ejecuta la actualización al cargar este script para sincronizar el header
actualizarHeaderSesion();