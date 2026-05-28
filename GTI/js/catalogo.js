const trialBtn = document.getElementById("trialBtn");

if (trialBtn !== null) {
    trialBtn.addEventListener("click", function() {
        const sesionIniciada = localStorage.getItem("gtiSesionIniciada");

        if (sesionIniciada === "true") {
            window.location.href = "../../DOA/elegir_perfil.php";
        } else {
            window.location.href = "../registro.php";
        }
    });
}
