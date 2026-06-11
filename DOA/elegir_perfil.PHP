<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

if (!isset($_SESSION["id_usuario"])) {
  header("Location: ../GTI/login.php?redir=doa");
  exit;
}

function limpiar_texto($texto)
{
  return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function obtener_icono_rol($rol)
{
  return match ($rol) {
    'alumno' => 'graduation-cap',
    'profesor' => 'user',
    'secretaria' => 'briefcase-business',
  };
}

function obtener_texto_rol($rol)
{
  return match ($rol) {
    'alumno' => 'Alumno',
    'profesor' => 'Profesor',
    'secretaria' => 'Secretaría',
  };
}

function obtener_titulo_grupo($rol)
{
  return match ($rol) {
    'alumno' => 'Alumnos',
    'profesor' => 'Profesores',
    'secretaria' => 'Secretaría',
  };
}

function obtener_password_demo($dni)
{
  $partes_dni = explode('-', $dni);
  return $partes_dni[1];
}

$errores = [];
$correo_demo = "";
$password_demo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $correo_demo = strtolower(trim($_POST["correo_demo"]));
  $password_demo = trim($_POST["password_demo"]);

  if ($correo_demo === "" || $password_demo === "") {
    $errores[] = "Introduce el correo electrónico y la contraseña.";
  }

  if (count($errores) === 0) {
    $consulta_usuario_demo = $pdo->prepare("
      SELECT id_usuario, dni, nombre, apellidos, email, password_hash, rol
      FROM usuarios
      WHERE email = :email
      AND tipo_usuario = 'demo'
      AND estado = 'activo'
      LIMIT 1
    ");

    $consulta_usuario_demo->execute([
      "email" => $correo_demo
    ]);

    $usuario_demo = $consulta_usuario_demo->fetch();

    if (!$usuario_demo || !password_verify($password_demo, $usuario_demo["password_hash"])) {
      $errores[] = "Las credenciales no pertenecen a ningún usuario de prueba.";
    } else {
      $_SESSION["doa_id_usuario"] = $usuario_demo["id_usuario"];
      $_SESSION["doa_dni"] = $usuario_demo["dni"];
      $_SESSION["doa_nombre"] = $usuario_demo["nombre"];
      $_SESSION["doa_apellidos"] = $usuario_demo["apellidos"];
      $_SESSION["doa_email"] = $usuario_demo["email"];
      $_SESSION["doa_rol"] = $usuario_demo["rol"];

      if ($usuario_demo["rol"] === "profesor") {
        header("Location: panel_profesor.php");
        exit;
      }

      if ($usuario_demo["rol"] === "secretaria") {
        header("Location: panel_secretaria.php");
        exit;
      }

      header("Location: panel_principal.php");
      exit;
    }
  }
}

$consulta_usuarios_demo = $pdo->query("
  SELECT id_usuario, dni, nombre, apellidos, email, rol
  FROM usuarios
  WHERE tipo_usuario = 'demo'
  AND estado = 'activo'
  AND dni IS NOT NULL
  ORDER BY FIELD(rol, 'alumno', 'profesor', 'secretaria'), nombre, apellidos
");

$usuarios_demo = $consulta_usuarios_demo->fetchAll();

$usuarios_por_rol = [
  "alumno" => [],
  "profesor" => [],
  "secretaria" => []
];

foreach ($usuarios_demo as $usuario_demo) {
  $usuarios_por_rol[$usuario_demo["rol"]][] = $usuario_demo;
}

$total_usuarios_demo = count($usuarios_demo);
$total_alumnos = count($usuarios_por_rol["alumno"]);
$total_profesores = count($usuarios_por_rol["profesor"]);
$total_secretaria = count($usuarios_por_rol["secretaria"]);
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>Elegir perfil de prueba | DOA</title>
  <!-- Enlaces a hojas de estilo -->
  <link href="css/doa.css" rel="stylesheet" />
  <link href="css/elegir_perfil.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com" rel="preconnect" />
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
    rel="stylesheet" />
  <!-- Fin enlaces a hojas de estilo -->
</head>

<body class="pagina-doa pagina-elegir-perfil">
  <!-- Inicio de el header -->
  <header class="cabecera-doa">
    <div class="cabecera-doa__logo">
      <div class="marca-doa">
        <a
          aria-label="Volver a elegir perfil de prueba"
          class="marca-doa__link"
          href="elegir_perfil.php">
          <img
            alt="Logo de DOA"
            class="logo-doa"
            src="img/LogoDoaAzulRecortado.png" />
        </a>
        <a class="marca-doa__powered" href="../index.php" aria-label="Volver a GTI">
          <span class="marca-doa__powered-text">Powered by</span>

          <img
            alt="Grado en Tecnologías Interactivas"
            class="marca-doa__powered-logo"
            src="img/logoGTI_blanco_fondo_negro-removebg-preview.png" />
        </a>
      </div>
    </div>
    <div class="cabecera-doa__centro"></div>
    <div class="cabecera-doa__derecha">
      <a class="boton-volver-gti" href="../index.php">
        Volver a GTI
      </a>
    </div>
  </header>
  <!-- Final de el header -->
  <!-- Inicio del contenido principal de la página -->
  <main class="contenido-elegir-perfil">
    <section class="presentacion-perfil">
      <div aria-hidden="true" class="presentacion-perfil__icono">
        <img alt="" src="img/iconos/blue-user.svg" />
      </div>
      <div class="presentacion-perfil__texto">
        <h1>Elegir perfil de prueba</h1>
        <p>
          Selecciona un usuario de prueba para completar el inicio de sesión.
          También puedes escribir los datos de inicio de sesión manualmente.
        </p>
      </div>
    </section>
    <section aria-label="Acceso a la demo de DOA" class="zona-acceso-demo">
      <div class="caja-demo caja-demo--usuarios">
        <h2 class="sr-only">Usuarios de prueba</h2>
        <nav aria-label="Filtrar usuarios de prueba" class="filtros-perfil">
          <button
            class="filtro-perfil filtro-perfil--activo"
            data-filtro="todos"
            type="button">
            <span aria-hidden="true" class="filtro-perfil__icono">
              <img
                alt=""
                class="icono-estado icono-estado--gris"
                src="img/iconos/grey-layout-grid.svg" />
              <img
                alt=""
                class="icono-estado icono-estado--azul"
                src="img/iconos/blue-layout-grid.svg" />
            </span>
            <span>
              Todos
              <small> (<?php echo $total_usuarios_demo; ?>) </small>
            </span>
          </button>
          <button class="filtro-perfil" data-filtro="alumno" type="button">
            <span aria-hidden="true" class="filtro-perfil__icono">
              <img
                alt=""
                class="icono-estado icono-estado--gris"
                src="img/iconos/grey-graduation-cap.svg" />
              <img
                alt=""
                class="icono-estado icono-estado--azul"
                src="img/iconos/blue-graduation-cap.svg" />
            </span>
            <span>
              Alumnos
              <small> (<?php echo $total_alumnos; ?>) </small>
            </span>
          </button>
          <button class="filtro-perfil" data-filtro="profesor" type="button">
            <span aria-hidden="true" class="filtro-perfil__icono">
              <img
                alt=""
                class="icono-estado icono-estado--gris"
                src="img/iconos/grey-user.svg" />
              <img
                alt=""
                class="icono-estado icono-estado--azul"
                src="img/iconos/blue-user.svg" />
            </span>
            <span>
              Profesores
              <small> (<?php echo $total_profesores; ?>) </small>
            </span>
          </button>
          <button class="filtro-perfil" data-filtro="secretaria" type="button">
            <span aria-hidden="true" class="filtro-perfil__icono">
              <img
                alt=""
                class="icono-estado icono-estado--gris"
                src="img/iconos/grey-briefcase-business.svg" />
              <img
                alt=""
                class="icono-estado icono-estado--azul"
                src="img/iconos/blue-briefcase-business.svg" />
            </span>
            <span>
              Secretaría
              <small> (<?php echo $total_secretaria; ?>) </small>
            </span>
          </button>
        </nav>
        <div class="lista-usuarios-demo">
          <?php foreach ($usuarios_por_rol as $rol => $usuarios_rol) { ?>
            <?php
            $icono_rol = obtener_icono_rol($rol);
            $texto_rol = obtener_texto_rol($rol);
            $titulo_grupo = obtener_titulo_grupo($rol);
            ?>

            <section class="grupo-usuarios-demo" data-grupo="<?php echo limpiar_texto($rol); ?>">
              <h3><?php echo limpiar_texto($titulo_grupo); ?></h3>

              <?php foreach ($usuarios_rol as $usuario_demo) { ?>
                <?php
                $nombre_completo = trim($usuario_demo["nombre"] . " " . $usuario_demo["apellidos"]);
                $password_visible = obtener_password_demo($usuario_demo["dni"]);
                ?>

                <button
                  class="usuario-demo"
                  data-dni="<?php echo limpiar_texto($usuario_demo["dni"]); ?>"
                  data-email="<?php echo limpiar_texto($usuario_demo["email"]); ?>"
                  data-nombre="<?php echo limpiar_texto($nombre_completo); ?>"
                  data-password="<?php echo limpiar_texto($password_visible); ?>"
                  data-rol="<?php echo limpiar_texto($usuario_demo["rol"]); ?>"
                  data-rol-texto="<?php echo limpiar_texto($texto_rol); ?>"
                  type="button">

                  <span aria-hidden="true" class="usuario-demo__icono">
                    <img
                      alt=""
                      class="icono-estado icono-estado--gris"
                      src="img/iconos/grey-<?php echo limpiar_texto($icono_rol); ?>.svg" />
                    <img
                      alt=""
                      class="icono-estado icono-estado--azul"
                      src="img/iconos/blue-<?php echo limpiar_texto($icono_rol); ?>.svg" />
                  </span>

                  <span class="usuario-demo__datos">
                    <strong><?php echo limpiar_texto($nombre_completo); ?></strong>
                    <span>
                      <?php echo limpiar_texto($usuario_demo["email"]); ?> ·
                      contraseña:
                      <?php echo limpiar_texto($password_visible); ?>
                    </span>
                  </span>

                  <span aria-hidden="true" class="usuario-demo__flecha">
                    <img
                      alt=""
                      class="icono-estado icono-estado--gris"
                      src="img/iconos/grey-chevron-right.svg" />
                    <img
                      alt=""
                      class="icono-estado icono-estado--azul"
                      src="img/iconos/blue-chevron-right.svg" />
                  </span>
                </button>
              <?php } ?>
            </section>
          <?php } ?>
        </div>
      </div>
      <div class="caja-demo caja-demo--login">
        <div class="banner-eleccion-perfil" id="bannerEleccionPerfil">
          <div class="banner-eleccion-perfil__icono" aria-hidden="true">
            <img alt="" src="img/iconos/blue-user.svg" />
          </div>

          <div class="banner-eleccion-perfil__contenido">
            <h2>Elige un perfil para continuar</h2>
            <p>
              Pulsa uno de los usuarios de prueba disponibles. Sus credenciales se rellenarán automáticamente.
            </p>
          </div>
        </div>

        <h2 class="titulo-con-icono titulo-login-demo">
          <span aria-hidden="true" class="titulo-con-icono__icono">
            <img alt="" src="img/iconos/grey-lock-keyhole.svg" />
          </span>
          <span> Acceso a la demo </span>
        </h2>
        <form
          class="formulario-login-demo"
          id="formLoginDemo"
          method="post">
          <div class="campo-formulario">
            <label for="correoDemo"> Correo electrónico </label>
            <input
              id="correoDemo"
              name="correo_demo"
              placeholder="ejemplo@upv.es"
              type="email"
              value="<?php echo limpiar_texto($correo_demo); ?>"
              required />
          </div>
          <div class="campo-formulario">
            <label for="passwordDemo"> Contraseña </label>
            <div class="campo-password">
              <input
                id="passwordDemo"
                name="password_demo"
                placeholder="Introduce la contraseña"
                type="password"
                value="<?php echo limpiar_texto($password_demo); ?>"
                required />
              <button class="boton-mostrar-password" id="botonMostrarPassword" type="button">
                Mostrar
              </button>
            </div>
          </div>
          <div class="perfil-seleccionado">
            <p class="perfil-seleccionado__titulo">Perfil seleccionado</p>
            <ul>
              <li>
                <span> Nombre: </span>
                <strong id="perfilNombre"> Ninguno </strong>
              </li>
              <li>
                <span> DNI: </span>
                <strong id="perfilDni"> --- </strong>
              </li>
              <li>
                <span> Rol detectado: </span>
                <strong id="perfilRol"> --- </strong>
              </li>
            </ul>
          </div>
          <p class="mensaje-error-login <?php echo count($errores) === 0 ? 'oculto' : ''; ?>" id="mensajeErrorLogin">
            <?php
            if (count($errores) > 0) {
              echo limpiar_texto($errores[0]);
            }
            ?>
          </p>
          <button class="boton-acceso-demo boton-acceso-demo--secundario" id="botonAccesoDemo" type="submit">
            Acceder a DOA
          </button>
        </form>
        <div class="caja-info-demo">
          <div class="caja-info-demo__cabecera">
            <span aria-hidden="true" class="caja-info-demo__icono">
              <img alt="" src="img/iconos/grey-info.svg" />
            </span>
            <strong> Información </strong>
          </div>
          <p>
            Selecciona un perfil o introduce credenciales manualmente. Si los
            datos no coinciden con ningún usuario de prueba, no se podrá
            acceder a DOA.
          </p>
        </div>
      </div>
    </section>
  </main>
  <!-- Final del contenido principal de la página -->
  <script src="js/elegir_perfil.js"></script>
</body>

</html>