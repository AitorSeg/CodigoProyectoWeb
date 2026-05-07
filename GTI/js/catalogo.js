const trialBtn = document.getElementById("trialBtn");

if (trialBtn !== null) {
    trialBtn.addEventListener("click", function() {
        const sesionIniciada = localStorage.getItem("gtiSesionIniciada");

        if (sesionIniciada === "true") {
            window.location.href = "../../DOA/html/elegir_perfil.html";
        } else {
            localStorage.setItem("paginaAnterior", window.location.href);
            window.location.href = "../HTML/login.html";
        }
    });
}