document.addEventListener('DOMContentLoaded', () => {
    const baseDatosLocal = {
        "programacion": { titulo: "Programación II", profesor: "Kevan Pounds", archivos: { "UNIDAD 01": [{ nombre: "Intro", tipo: "PDF", tamano: "1.2 MB", etiqueta: "Teoría", claseEtiqueta: "etiqueta-neutral", fecha: "10/01/2026" }], "UNIDAD 03": [{ nombre: "Recursividad", tipo: "PDF", tamano: "12.5 MB", etiqueta: "Unidad actual", claseEtiqueta: "etiqueta-actual", fecha: "19/03/2026" }, { nombre: "Ejercicios 1", tipo: "PDF", tamano: "2.6 MB", etiqueta: "Práctica", claseEtiqueta: "etiqueta-importante", fecha: "26/08/2025" }], "PRÁCTICAS": [{ nombre: "Práctica 1", tipo: "ZIP", tamano: "5.1 MB", etiqueta: "Práctica", claseEtiqueta: "etiqueta-importante", fecha: "05/02/2026" }] } },
        "matematicas": { titulo: "Matemáticas", profesor: "Don Pepito", archivos: { "UNIDAD 01": [{ nombre: "Álgebra", tipo: "PDF", tamano: "3.2 MB", etiqueta: "Teoría", claseEtiqueta: "etiqueta-neutral", fecha: "12/01/2026" }], "UNIDAD 03": [{ nombre: "Guía Numérica", tipo: "PDF", tamano: "4.5 MB", etiqueta: "Unidad actual", claseEtiqueta: "etiqueta-actual", fecha: "20/03/2026" }] } },
        "fisica": { titulo: "Física", profesor: "Eolande Merriton Mizzi", archivos: { "UNIDAD 03": [{ nombre: "Laboratorio", tipo: "PDF", tamano: "8.1 MB", etiqueta: "Urgente", claseEtiqueta: "etiqueta-alerta", fecha: "25/03/2026" }] } }
    };

    let materiaSeleccionada = "programacion";
    if (typeof window.obtenerAsignaturaSeleccionada === 'function') { materiaSeleccionada = window.obtenerAsignaturaSeleccionada() || "programacion"; }
    else { materiaSeleccionada = localStorage.getItem("asignaturaSeleccionada") || new URLSearchParams(window.location.search).get('materia') || "programacion"; }
    materiaSeleccionada = materiaSeleccionada.toLowerCase().trim();

    const datosMateria = baseDatosLocal[materiaSeleccionada] || baseDatosLocal["programacion"];

    const titHTML = document.getElementById('tituloAsignatura');
    const profHTML = document.getElementById('profesorAsignatura');
    if(titHTML) titHTML.textContent = datosMateria.titulo;
    if(profHTML) profHTML.textContent = datosMateria.profesor;

    let unidadGlobalActual = 'UNIDAD 03';
    const listaCarpetas = document.querySelectorAll('.carpeta-click');
    const cuerpoTablaArchivos = document.getElementById('cuerpoTablaArchivos');
    const filtroTipo = document.getElementById('filtroTipo');
    const filtroEtiqueta = document.getElementById('filtroEtiqueta');

    function renderizarTabla() {
        if(!cuerpoTablaArchivos) return;
        cuerpoTablaArchivos.innerHTML = '';
        const archivos = datosMateria.archivos[unidadGlobalActual] || [];
        const tipoSel = filtroTipo ? filtroTipo.value : "TODOS";
        const etiSel = filtroEtiqueta ? filtroEtiqueta.value : "TODAS";

        const archivosFiltrados = archivos.filter(a => { return (tipoSel === "TODOS" || a.tipo === tipoSel) && (etiSel === "TODAS" || a.etiqueta === etiSel); });

        if (archivosFiltrados.length === 0) {
            cuerpoTablaArchivos.innerHTML = '<div style="padding:24px;text-align:center;color:var(--color-muted);font-size:13px;">No hay archivos aquí.</div>';
            return;
        }

        archivosFiltrados.forEach(archivo => {
            let badge = archivo.etiqueta ? `<span class="badge-etiqueta ${archivo.claseEtiqueta}">${archivo.etiqueta}</span>` : '';
            cuerpoTablaArchivos.insertAdjacentHTML('beforeend', `
                <div class="biblioteca-tabla-fila archivo-fila">
                    <a href="#" class="nombre-archivo">${archivo.nombre}</a>
                    <span class="col-tipo">${archivo.tipo}</span>
                    <span class="col-movil-oculta">${archivo.tamano}</span>
                    <span class="col-movil-oculta">${badge}</span>
                    <span class="col-movil-oculta">${archivo.fecha}</span>
                </div>
            `);
        });
    }

    if(listaCarpetas) {
        listaCarpetas.forEach(enlace => {
            enlace.addEventListener('click', (e) => {
                e.preventDefault();
                listaCarpetas.forEach(link => link.classList.remove('activo'));
                enlace.classList.add('activo');
                unidadGlobalActual = enlace.getAttribute('data-unidad');

                const textoCarpetaPadre = document.getElementById('textoCarpetaPadre');
                const flechaBreadcrumb = document.getElementById('flechaBreadcrumb');
                const textoBreadcrumb = document.getElementById('textoBreadcrumb');

                if(unidadGlobalActual.includes("UNIDAD")) {
                    if(textoCarpetaPadre) textoCarpetaPadre.style.display = "inline";
                    if(flechaBreadcrumb) flechaBreadcrumb.style.display = "inline";
                } else {
                    if(textoCarpetaPadre) textoCarpetaPadre.style.display = "none";
                    if(flechaBreadcrumb) flechaBreadcrumb.style.display = "none";
                }
                if(textoBreadcrumb) textoBreadcrumb.textContent = unidadGlobalActual;

                renderizarTabla();
                const sidebar = document.getElementById('menuCarpetasGeneral');
                if(sidebar) sidebar.classList.remove('mostrar-movil');
            });
        });
    }

    if(filtroTipo) filtroTipo.addEventListener('change', renderizarTabla);
    if(filtroEtiqueta) filtroEtiqueta.addEventListener('change', renderizarTabla);

    const btnNavegacionMovil = document.getElementById('btnNavegacionMovil');
    const sidebar = document.getElementById('menuCarpetasGeneral');
    const btnFiltrarMovil = document.getElementById('btnFiltrarMovil');
    const contenedorFiltros = document.getElementById('contenedorFiltros');

    if(btnNavegacionMovil && sidebar) {
        btnNavegacionMovil.addEventListener('click', () => {
            sidebar.classList.toggle('mostrar-movil');
            if(contenedorFiltros) contenedorFiltros.classList.remove('mostrar-movil');
        });
    }
    if(btnFiltrarMovil && contenedorFiltros) {
        btnFiltrarMovil.addEventListener('click', () => {
            contenedorFiltros.classList.toggle('mostrar-movil');
            if(sidebar) sidebar.classList.remove('mostrar-movil');
        });
    }

    renderizarTabla();

    // SUBIDA DOCENTE
    const formSubirRecurso = document.getElementById('formSubirRecurso');
    if(formSubirRecurso) {
        formSubirRecurso.addEventListener('submit', (e) => {
            e.preventDefault();
            const inputTitulo = document.getElementById('tituloRecurso');
            const inputArchivo = document.getElementById('archivoRecurso');
            if(inputTitulo.value.trim() !== '' && inputArchivo.files.length > 0) {
                const extension = inputArchivo.files[0].name.split('.').pop().toUpperCase();
                if(!datosMateria.archivos[unidadGlobalActual]) datosMateria.archivos[unidadGlobalActual] = [];
                datosMateria.archivos[unidadGlobalActual].unshift({
                    nombre: inputTitulo.value.trim(),
                    tipo: extension,
                    tamano: (inputArchivo.files[0].size / 1048576).toFixed(1) + " MB",
                    etiqueta: "Nuevo",
                    claseEtiqueta: "etiqueta-success", // Verde
                    fecha: new Date().toLocaleDateString('es-ES')
                });
                formSubirRecurso.reset();
                renderizarTabla();
                alert("Recurso subido exitosamente a " + unidadGlobalActual);
            }
        });
    }
});