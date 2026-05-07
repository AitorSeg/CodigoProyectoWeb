/*
    GTI - Registro demo
*/

const registerForm = document.getElementById('registerForm');

if (registerForm !== null) {
    registerForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const passwordInput = document.getElementById('pass') || document.getElementById('password');
        const confirmInput = document.getElementById('passConfirm') || document.getElementById('confirmPassword');
        const passError = document.getElementById('passError') || document.getElementById('error-confirm');

        if (passwordInput === null || confirmInput === null || passError === null) {
            return;
        }

        if (passwordInput.value !== confirmInput.value) {
            passError.style.display = 'block';
            confirmInput.style.borderColor = '#ff4d4d';
            return;
        }

        passError.style.display = 'none';
        confirmInput.style.borderColor = 'transparent';
        alert('Registro completado correctamente en la demo de GTI.');
    });
}
