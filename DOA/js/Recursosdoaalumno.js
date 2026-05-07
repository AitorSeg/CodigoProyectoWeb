document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. SÚPER BASE DE DATOS DE TODAS LAS MATERIAS
    // ==========================================
    const baseDeDatos = {
        "programacion": {
            titulo: "Programación II",
            profesor: "Don Pepito",
            unidadActualTexto: "Unidad 03: Recursividad",
            archivos: {
                "UNIDAD 01": [
                    { nombre: "Introducción a la Programación", tipo: "PDF", tamano: "1.2 MB", etiqueta: "Teoría", claseEtiqueta: "etiqueta-neutral", fecha: "10/01/2026" }
                ],
                "UNIDAD 03": [
                    { nombre: "Recursividad", tipo: "PDF", tamano: "12.5 MB", etiqueta: "Unidad actual", claseEtiqueta: "etiqueta-actual", fecha: "19/03/2026" },
                    { nombre: "Ejercicios de prueba 1", tipo: "PDF", tamano: "2.6 MB", etiqueta: "Práctica", claseEtiqueta: "etiqueta-importante", fecha: "26/08/2025" }
                ],
                "PRACTICAS": [
                    { nombre: "Práctica 1 - Entorno", tipo: "ZIP", tamano: "5.1 MB", etiqueta: "Evaluación", claseEtiqueta: "etiqueta-alerta", fecha: "05/02/2026" }
                ]
            }
        },
        "matematicas": {
            titulo: "Matemáticas",
            profesor: "Don Pepito",
            unidadActualTexto: "Unidad 03: Límites",
            archivos: {
                "UNIDAD 01": [
                    { nombre: "Álgebra Básica", tipo: "PDF", tamano: "3.2 MB", etiqueta: "Teoría", claseEtiqueta: "etiqueta-neutral", fecha: "12/01/2026" },
                    { nombre: "Taller de Ecuaciones", tipo: "ZIP", tamano: "5.0 MB", etiqueta: "Práctica", claseEtiqueta: "etiqueta-importante", fecha: "15/01/2026" }
                ],
                "UNIDAD 03": [
                    { nombre: "Guía de Recursividad Numérica", tipo: "PDF", tamano: "4.5 MB", etiqueta: "Unidad actual", claseEtiqueta: "etiqueta-actual", fecha: "20/03/2026" }
                ],
                "EXAMENES": [
                    { nombre: "Examen Parcial 1 (Resuelto)", tipo: "PDF", tamano: "1.5 MB", etiqueta: "Repaso", claseEtiqueta: "etiqueta-neutral", fecha: "01/03/2026" }
                ]
            }
        },
        "fisica": {
            titulo: "Física",
            profesor: "Eolande Merriton Mizzi",
            unidadActualTexto: "Unidad 03: Movimiento y fuerzas",
            archivos: {
                "UNIDAD 01": [
                    { nombre: "Leyes de Newton", tipo: "PDF", tamano: "2.8 MB", etiqueta: "Teoría", claseEtiqueta: "etiqueta-neutral", fecha: "10/02/2026" }
                ],
                "UNIDAD 03": [
                    { nombre: "Laboratorio Movimiento", tipo: "PDF", tamano: "8.1 MB", etiqueta: "Urgente", claseEtiqueta: "etiqueta-alerta", fecha: "25/03/2026" }
                ],
                "PRACTICAS": [
                    { nombre: "Simulador de Fuerzas", tipo: "ZIP", tamano: "25 MB", etiqueta: "Importante", claseEtiqueta: "etiqueta-importante", fecha: "15/03/2026" }
                ]
            }
        }
    };

    // ==========================================
    // 2. LEER LA MATERIA DESDE EL LINK (URL)
    // ==========================================
    const parametrosURL = new URLSearchParams(window.location.search);
    const materiaURL = parametrosURL.get('materia');
    const materiaGuardada = window.obtenerAsignaturaSeleccionada ? window.obtenerAsignaturaSeleccionada() : 'programacion';
    const materiaSeleccionada = materiaURL || materiaGuardada || 'programacion';

    if (window.guardarAsignaturaSeleccionada && baseDeDatos[materiaSeleccionada]) {
        window.guardarAsignaturaSeleccionada(materiaSeleccionada);
    }

    const datosMateria = baseDeDatos[materiaSeleccionada] || baseDeDatos['matematicas'];
    const datosCabecera = window.DOA_ASIGNATURAS && window.DOA_ASIGNATURAS[materiaSeleccionada]
        ? window.DOA_ASIGNATURAS[materiaSeleccionada]
        : datosMateria;

    // ==========================================
    // 3. CAMBIAR TÍTULOS EN EL HTML
    // ==========================================
    document.getElementById('tituloAsignatura').textContent = datosCabecera.nombre || datosCabecera.titulo;
    document.getElementById('profesorAsignatura').textContent = datosCabecera.profesor;

    const labelUnidadActual = document.getElementById('unidadActualTextoAsignatura');
    if(labelUnidadActual) {
        labelUnidadActual.textContent = datosCabecera.unidadActualTexto;
    }

    // ==========================================
    // 4. ELEMENTOS DEL DOM PARA LA TABLA
    // ==========================================
    let unidadGlobalActual = 'UNIDAD 03';
    const listaCarpetas = document.querySelectorAll('.carpeta-click');
    const textoCarpetaPadre = document.getElementById('textoCarpetaPadre');
    const flechaBreadcrumb = document.getElementById('flechaBreadcrumb');
    const textoBreadcrumb = document.getElementById('textoBreadcrumb');
    const cuerpoTablaArchivos = document.getElementById('cuerpoTablaArchivos');

    const inputBuscador = document.getElementById('inputBuscador');
    const filtroTipo = document.getElementById('filtroTipo');
    const filtroEtiqueta = document.getElementById('filtroEtiqueta');

    const btnNavegacionMovil = document.getElementById('btnNavegacionMovil');
    const sidebar = document.getElementById('menuCarpetasGeneral');
    const btnFiltrarMovil = document.getElementById('btnFiltrarMovil');
    const contenedorFiltros = document.getElementById('contenedorFiltros');

    // ==========================================
    // 5. RENDERIZAR TABLA CON LOS DATOS DE LA MATERIA
    // ==========================================
    function renderizarTabla() {
        cuerpoTablaArchivos.innerHTML = '';

        const claveSinAcentos = unidadGlobalActual.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        const archivosDeLaCarpeta = datosMateria.archivos[unidadGlobalActual] || datosMateria.archivos[claveSinAcentos] || [];

        const tipoSeleccionado = filtroTipo.value;
        const etiquetaSeleccionada = filtroEtiqueta.value;

        const archivosFiltrados = archivosDeLaCarpeta.filter(archivo => {
            const pasaFiltroTipo = (tipoSeleccionado === "TODOS" || archivo.tipo === tipoSeleccionado);
            const pasaFiltroEtiqueta = (etiquetaSeleccionada === "TODAS" || archivo.etiqueta === etiquetaSeleccionada);
            return pasaFiltroTipo && pasaFiltroEtiqueta;
        });

        if (archivosFiltrados.length === 0) {
            cuerpoTablaArchivos.innerHTML = '<div style="padding: 24px; text-align:center; color: var(--color-muted); font-size: 13px;">No hay archivos publicados aquí.</div>';
            return;
        }

        archivosFiltrados.forEach(archivo => {
            let htmlEtiqueta = '';
            if (archivo.etiqueta) {
                htmlEtiqueta = `<span class="badge-etiqueta ${archivo.claseEtiqueta}">${archivo.etiqueta}</span>`;
            }

            const filaHTML = `
                <div class="biblioteca-tabla-fila archivo-fila">
                    <a href="#" class="nombre-archivo">${archivo.nombre}</a>
                    <span class="col-tipo">${archivo.tipo}</span>
                    <span class="col-movil-oculta">${archivo.tamano}</span>
                    <span class="col-movil-oculta">${htmlEtiqueta}</span>
                    <span class="col-movil-oculta">${archivo.fecha}</span>
                </div>
            `;
            cuerpoTablaArchivos.insertAdjacentHTML('beforeend', filaHTML);
        });
    }

    // ==========================================
    // 6. EVENTOS (Clics y Buscador)
    // ==========================================
    listaCarpetas.forEach(enlace => {
        enlace.addEventListener('click', (event) => {
            event.preventDefault();
            listaCarpetas.forEach(link => link.classList.remove('activo'));
            enlace.classList.add('activo');

            unidadGlobalActual = enlace.getAttribute('data-unidad');

            if(unidadGlobalActual.includes("UNIDAD")) {
                textoCarpetaPadre.style.display = "inline";
                flechaBreadcrumb.style.display = "inline";
                textoCarpetaPadre.textContent = "TEMARIO";
                textoBreadcrumb.textContent = unidadGlobalActual;
            } else {
                textoCarpetaPadre.style.display = "none";
                flechaBreadcrumb.style.display = "none";
                textoBreadcrumb.textContent = unidadGlobalActual;
            }

            filtroTipo.value = "TODOS";
            filtroEtiqueta.value = "TODAS";
            inputBuscador.value = '';

            renderizarTabla();
            sidebar.classList.remove('mostrar-movil');
        });
    });

    filtroTipo.addEventListener('change', renderizarTabla);
    filtroEtiqueta.addEventListener('change', renderizarTabla);

    if (inputBuscador) {
        inputBuscador.addEventListener('keyup', (event) => {
            const textoBusqueda = event.target.value.toLowerCase();
            const filas = document.querySelectorAll('.archivo-fila');
            filas.forEach(fila => {
                const nombreArchivo = fila.querySelector('.nombre-archivo').textContent.toLowerCase();
                fila.style.display = nombreArchivo.includes(textoBusqueda) ? 'grid' : 'none';
            });
        });
    }

    if (btnNavegacionMovil && sidebar && contenedorFiltros) {
        btnNavegacionMovil.addEventListener('click', () => {
            sidebar.classList.toggle('mostrar-movil');
            contenedorFiltros.classList.remove('mostrar-movil');
        });
    }

    if (btnFiltrarMovil && contenedorFiltros && sidebar) {
        btnFiltrarMovil.addEventListener('click', () => {
            contenedorFiltros.classList.toggle('mostrar-movil');
            sidebar.classList.remove('mostrar-movil');
        });
    }

    // 7. ARRANQUE
    renderizarTabla();
});