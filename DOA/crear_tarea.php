<?php
$rol_pagina = "profesor";
$pagina_activa = "asignaturas";
$enlace_panel = "panel_profesor.php";
$placeholder_buscador = "Buscar recurso, tarea...";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Crear tarea | DOA
    </title>
    <!-- Enlaces a hojas de estilo -->
    <link href="css/doa.css" rel="stylesheet" />
    <link href="css/doa-layout.css" rel="stylesheet" />
    <link href="css/doa-componentes.css" rel="stylesheet" />
    <link href="css/detalle_asignatura.css" rel="stylesheet" />
    <link href="css/crear_tarea.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet" />
    <!-- Fin enlaces a hojas de estilo -->
</head>

<body class="pagina-doa pagina-crear-tarea">
    <!-- Header -->
    <?php include "includes/header-doa.php"; ?>
    <!-- Inicio del contenido principal -->
    <div class="layout-doa">
        <!-- Barra Lateral -->
        <?php include "includes/barra-lateral-doa.php"; ?>
        <!-- Inicio del contenido principal de la página -->
        <main class="contenido-doa contenido-detalle-asignatura contenido-crear-tarea">
         <!-- Inicio del detalle de asignatura principal -->
         <section class="detalle-asignatura-principal">
          <!-- Inicio de la cabecera de detalles de asignatura -->
          <div class="cabecera-detalle-asignatura">
           <!-- Inicio de la información de la asignatura -->
           <div class="cabecera-detalle-asignatura__texto">
            <a class="enlace-volver-asignaturas" href="asignaturas_profesor.php">
             <span aria-hidden="true" class="enlace-volver-asignaturas__icono">
              <img alt="" src="img/iconos/grey-chevron-right.svg"/>
             </span>
             <span>
              Volver a mis asignaturas
             </span>
            </a>
            <h1 id="tituloAsignatura">
             Programaci&oacute;n II
            </h1>
            <ul class="metadatos-asignatura">
             <li>
              <img alt="" src="img/iconos/grey-user.svg"/>
              <span id="profesorAsignatura">
               Don Pepito
              </span>
             </li>
             <li>
              <img alt="" src="img/iconos/grey-notebook.svg"/>
              <span id="unidadActualTextoAsignatura">
               Unidad 03:
               <strong>
                Recursividad
               </strong>
              </span>
             </li>
            </ul>
           </div>
           <!-- Final de la información de la asignatura -->
           <!-- Inicio de las pestañas de navegación entre secciones de la asignatura -->
           <div class="cabecera-detalle-asignatura__pestanas">
            <nav aria-label="Secciones de la asignatura" class="pestanas-asignatura">
             <a class="pestanas-asignatura__item" href="recursos_profesor.php">
              Recursos
             </a>
             <a class="pestanas-asignatura__item pestanas-asignatura__item--activo" href="listado_tareas_profe.php">
              Tareas
             </a>
             <a class="pestanas-asignatura__item" href="examenes_profesor.php">
              Ex&aacute;menes
             </a>
             <a class="pestanas-asignatura__item" href="detalle_tarea_entregada.php">
              Calificaciones
             </a>
            </nav>
           </div>
           <!-- Final de las pestañas de navegación entre secciones de la asignatura -->
          </div>
          <!-- Final de la cabecera de detalles de asignatura -->
          <!-- Inicio del formulario para crear o editar una tarea -->
          <form class="formulario-crear-tarea">
           <!-- Inicio del campo de nombre de la tarea -->
           <div class="campo-titulo-tarea">
            <input aria-label="Nombre de la tarea" id="nombreTarea" name="nombreTarea" type="text" value="Nombre de la tarea"/>
            <button aria-label="Editar nombre de la tarea" class="boton-editar-campo" type="button">
             <img alt="" src="img/iconos/blue-pencil.svg"/>
            </button>
           </div>
           <!-- Final del campo de nombre de la tarea -->
           <!-- Inicio del campo de descripción de la tarea -->
           <div class="campo-descripcion-tarea">
            <textarea aria-label="Descripci&oacute;n de la tarea" id="descripcionTarea" name="descripcionTarea">Descripci&oacute;n de la tarea:</textarea>
            <button aria-label="Editar descripci&oacute;n de la tarea" class="boton-editar-campo boton-editar-campo--area" type="button">
             <img alt="" src="img/iconos/blue-pencil.svg"/>
            </button>
           </div>
           <!-- Final del campo de descripción de la tarea -->
           <div class="separador-crear-tarea">
           </div>
           <!-- Inicio del bloque de fechas de la tarea -->
           <div class="fechas-crear-tarea">
            <div class="fecha-crear-tarea">
             <label for="fechaEmision">
              Fecha de emisi&oacute;n:
             </label>
             <input id="fechaEmision" name="fechaEmision" type="text" value="Lunes, 14 de Abril"/>
            </div>
            <div class="fecha-crear-tarea fecha-crear-tarea--editable">
             <label for="fechaEntrega">
              Fecha de entrega:
             </label>
             <div class="fecha-crear-tarea__campo">
              <input id="fechaEntrega" name="fechaEntrega" type="date"/>
              <button aria-label="Editar fecha de entrega" class="boton-editar-campo" type="button">
               <img alt="" src="img/iconos/blue-pencil.svg"/>
              </button>
             </div>
            </div>
           </div>
           <!-- Final del bloque de fechas de la tarea -->
           <!-- Inicio de la zona inferior de entregas, recursos y acciones -->
           <div class="zona-crear-tarea">
            <!-- Inicio del bloque de entregas -->
            <section class="entregas-tarea">
             <h2>
              Entregas
             </h2>
             <div class="tabla-entregas">
              <div class="tabla-entregas__cabecera">
               <p>
                Nombre
               </p>
               <p>
                Etiquetas
               </p>
               <p>
                Fecha
               </p>
               <p>
                Calificaci&oacute;n
               </p>
              </div>
             </div>
            </section>
            <!-- Final del bloque de entregas -->
            <!-- Inicio del bloque de recursos subidos -->
            <section class="recursos-subidos">
             <h2>
              Recursos Subidos
             </h2>
             <button class="boton-anadir-recurso" type="button">
              A&ntilde;adir recurso
             </button>
            </section>
            <!-- Final del bloque de recursos subidos -->
            <!-- Inicio de los botones finales del formulario -->
            <div class="acciones-crear-tarea">
             <button class="boton-secundario" type="button">
              Guardar cambios
             </button>
             <button class="boton-principal" onclick="window.location.href='listado_tareas_profe.php'" type="button">
              Crear
             </button>
             <button class="boton-secundario" onclick="window.location.href='listado_tareas_profe.php'" type="button">
              Cancelar
             </button>
            </div>
            <!-- Final de los botones finales del formulario -->
           </div>
           <!-- Final de la zona inferior de entregas, recursos y acciones -->
          </form>
          <!-- Final del formulario para crear o editar una tarea -->
         </section>
         <!-- Final del detalle de asignatura principal -->
        </main>
        <!-- Final del contenido principal de la página -->
    </div>
    <!-- Final del contenido principal -->
    <script src="js/doa_layout.js">
    </script>
    <script src="js/doa-datos.js">
    </script>
</body>

</html>
