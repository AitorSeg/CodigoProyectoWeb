const trialBtn = document.getElementById("trialBtn");

if (trialBtn !== null) {
    trialBtn.addEventListener("click", function() {
        const sesionIniciada = localStorage.getItem("gtiSesionIniciada");

        if (sesionIniciada === "true") {
            window.location.href = "../../DOA/html/elegir_perfil.html";
        } else {
            window.location.href = "../html/registro.html";
        }
    });
}