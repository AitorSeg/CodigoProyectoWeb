<?php
session_start();

unset($_SESSION["doa_id_usuario"]);
unset($_SESSION["doa_dni"]);
unset($_SESSION["doa_nombre"]);
unset($_SESSION["doa_apellidos"]);
unset($_SESSION["doa_email"]);
unset($_SESSION["doa_rol"]);

header("Location: elegir_perfil.php");
exit;