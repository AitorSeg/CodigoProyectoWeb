const registerForm = document.getElementById("registerForm");

function obtenerUsuarios() {
    const usuariosGuardados = localStorage.getItem("gtiUsuarios");

    if (usuariosGuardados === null) {
        return [];
    }

    return JSON.parse(usuariosGuardados);
}

function guardarUsuarios(usuarios) {
    localStorage.setItem("gtiUsuarios", JSON.stringify(usuarios));
}

if (registerForm !== null) {
    registerForm.addEventListener("submit", function(event) {
        event.preventDefault();

        const nombreInput = document.getElementById("nombre");
        const apellidosInput = document.getElementById("apellidos");
        const emailInput = document.getElementById("email");
        const passwordInput = document.getElementById("pass");
        const confirmInput = document.getElementById("passConfirm");
        const passError = document.getElementById("passError");

        const nombre = nombreInput.value.trim();
        const apellidos = apellidosInput.value.trim();
        const email = emailInput.value.trim().toLowerCase();
        const password = passwordInput.value;
        const passwordConfirm = confirmInput.value;

        passError.style.display = "none";
        confirmInput.style.borderColor = "transparent";

        if (password !== passwordConfirm) {
            passError.textContent = "Las contraseñas no coinciden";
            passError.style.display = "block";
            confirmInput.style.borderColor = "#ff4d4d";
            return;
        }

        const usuarios = obtenerUsuarios();

        const usuarioExiste = usuarios.some(function(usuario) {
            return usuario.email === email;
        });

        if (usuarioExiste) {
            passError.textContent = "Ya existe una cuenta con este correo";
            passError.style.display = "block";
            emailInput.style.borderColor = "#ff4d4d";
            return;
        }

        const nuevoUsuario = {
            nombre: nombre,
            apellidos: apellidos,
            email: email,
            password: password
        };

        usuarios.push(nuevoUsuario);
        guardarUsuarios(usuarios);

        alert("Registro completado correctamente. Ahora puedes iniciar sesión.");
        window.location.href = "login.php";
    });
}
