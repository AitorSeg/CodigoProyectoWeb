const contactForm = document.getElementById("contactForm");

if (contactForm !== null) {
    contactForm.addEventListener("submit", function(event) {
        event.preventDefault();

        const email = document.getElementById("email").value.trim();
        const titulo = document.getElementById("titulo").value.trim();
        const duda = document.getElementById("duda").value.trim();

        if (email === "" || titulo === "" || duda === "") {
            alert("Completa todos los campos antes de enviar.");
            return;
        }

        alert("Tu duda se ha enviado correctamente.");
        contactForm.reset();
    });
}
