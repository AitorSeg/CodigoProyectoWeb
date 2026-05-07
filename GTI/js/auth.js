/*
    GTI - Login demo
*/

const loginForm = document.getElementById('loginForm');

if (loginForm !== null) {
    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailError = document.getElementById('error-email');
        const passwordError = document.getElementById('error-password');

        const email = emailInput.value.trim();
        const password = passwordInput.value;
        let isValid = true;

        emailError.style.display = 'none';
        passwordError.style.display = 'none';
        emailInput.style.borderColor = 'transparent';
        passwordInput.style.borderColor = 'transparent';

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            emailError.style.display = 'block';
            emailInput.style.borderColor = '#ff4d4d';
            isValid = false;
        }

        if (password === '') {
            passwordError.style.display = 'block';
            passwordInput.style.borderColor = '#ff4d4d';
            isValid = false;
        }

        if (isValid) {
            alert('Sesión iniciada correctamente en la demo de GTI.');
        }
    });
}
