const loginForm = document.getElementById("loginForm");

const usuariosFijos = [
    {
        nombre: "Daniel",
        apellidos: "Pasamar",
        email: "dapasa@har.upv.es",
        password: "1234",
        tipo: "cuenta_fija"
    },
    {
        nombre: "Jorge",
        apellidos: "Gil",
        email: "jogilo@upvnet.upv.es",
        password: "4567",
        tipo: "cuenta_fija"
    }
];

function obtenerUsuariosRegistrados() {
    const usuariosGuardados = localStorage.getItem("gtiUsuarios");

    if (usuariosGuardados === null) {
        return [];
    }

    return JSON.parse(usuariosGuardados);
}

function obtenerTodosLosUsuarios() {
    const usuariosRegistrados = obtenerUsuariosRegistrados();
    return usuariosFijos.concat(usuariosRegistrados);
}

if (loginForm !== null) {
    loginForm.addEventListener("submit", function(event) {
        event.preventDefault();

        const emailInput = document.getElementById("email");
        const passwordInput = document.getElementById("password");
        const emailError = document.getElementById("error-email");
        const passwordError = document.getElementById("error-password");

        const email = emailInput.value.trim().toLowerCase();
        const password = passwordInput.value;

        let isValid = true;

        emailError.style.display = "none";
        passwordError.style.display = "none";
        emailInput.style.borderColor = "transparent";
        passwordInput.style.borderColor = "transparent";

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            emailError.textContent = "Ingresa un correo válido.";
            emailError.style.display = "block";
            emailInput.style.borderColor = "#ff4d4d";
            isValid = false;
        }

        if (password === "") {
            passwordError.textContent = "La contraseña es obligatoria.";
            passwordError.style.display = "block";
            passwordInput.style.borderColor = "#ff4d4d";
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        const usuarios = obtenerTodosLosUsuarios();

        const usuarioEncontrado = usuarios.find(function(usuario) {
            return usuario.email === email && usuario.password === password;
        });

        if (usuarioEncontrado === undefined) {
            passwordError.textContent = "Correo o contraseña incorrectos.";
            passwordError.style.display = "block";
            emailInput.style.borderColor = "#ff4d4d";
            passwordInput.style.borderColor = "#ff4d4d";
            return;
        }

        localStorage.setItem("gtiSesionIniciada", "true");
        localStorage.setItem("gtiUsuarioActual", JSON.stringify(usuarioEncontrado));

        const paginaAnterior = localStorage.getItem("paginaAnterior");

        if (paginaAnterior !== null) {
            localStorage.removeItem("paginaAnterior");
            window.location.href = paginaAnterior;
        } else {
            window.location.href = "../../../index.php";
        }
    });
}

