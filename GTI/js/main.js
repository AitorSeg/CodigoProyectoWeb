// Validación de Registro
const regForm = document.getElementById('registerForm');
if(regForm) {
    regForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const pass = document.getElementById('pass').value;
        const passConfirm = document.getElementById('passConfirm').value;
        const passError = document.getElementById('passError');

        if(pass !== passConfirm) {
            passError.style.display = 'block';
        } else {
            passError.style.display = 'none';
            alert("¡Registro exitoso! Bienvenido a GTI.");
        }
    });
}

// Validación de Login
const loginForm = document.getElementById('loginForm');
if(loginForm) {
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        alert("Iniciando sesión...");
        // Aquí iría tu conexión a PHP
    });
}
