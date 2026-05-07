/*
    DOA - Datos comunes de asignaturas
*/

window.DOA_ASIGNATURAS = {
    matematicas: {
        id: "matematicas",
        nombre: "Matemáticas",
        profesor: "Don Pepito",
        unidadActualTexto: "Unidad 03: Límites",
        unidadActualTitulo: "Unidad 03. Límites",
        descripcion:
            "En esta unidad trabajaremos el concepto de límite y su aplicación al análisis de funciones. Veremos cómo estudiar el comportamiento de una función y resolver ejercicios básicos paso a paso.",
        progresoEscritorio: "40%",
        progresoMovil: "33%",
        progresoClase: "progreso-asignatura--avance-40-33",
        evaluacion: {
            titulo: "Parcial 1",
            fecha: "15 Oct, 2025",
            hora: "10:00 AM",
            lugar: "Edificio G, Aula 6"
        },
        tarea: {
            titulo: "Ejercicio límites",
            vencimiento: "2 días"
        }
    },

    programacion: {
        id: "programacion",
        nombre: "Programación II",
        profesor: "Don Pepito",
        unidadActualTexto: "Unidad 03: Recursividad",
        unidadActualTitulo: "Unidad 03. Recursividad",
        descripcion:
            "En esta unidad aprenderás qué es la recursividad y cómo usarla para resolver problemas paso a paso. Veremos cómo una función puede llamarse a sí misma de forma controlada mediante un caso base y una llamada recursiva.",
        progresoEscritorio: "40%",
        progresoMovil: "33%",
        progresoClase: "progreso-asignatura--avance-40-33",
        evaluacion: {
            titulo: "Parcial 1",
            fecha: "15 Oct, 2025",
            hora: "10:00 AM",
            lugar: "Edificio G, Aula 6"
        },
        tarea: {
            titulo: "Ejercicio recursividad",
            vencimiento: "2 días"
        }
    },

    fisica: {
        id: "fisica",
        nombre: "Física",
        profesor: "Eolande Merriton Mizzi",
        unidadActualTexto: "Unidad 03: Movimiento y fuerzas",
        unidadActualTitulo: "Unidad 03. Movimiento y fuerzas",
        descripcion:
            "En esta unidad repasaremos los conceptos básicos de movimiento y fuerzas. Estudiaremos cómo describir trayectorias, interpretar magnitudes físicas y resolver problemas sencillos.",
        progresoEscritorio: "40%",
        progresoMovil: "33%",
        progresoClase: "progreso-asignatura--avance-40-33",
        evaluacion: {
            titulo: "Control de unidad",
            fecha: "22 Oct, 2025",
            hora: "09:00 AM",
            lugar: "Laboratorio 2"
        },
        tarea: {
            titulo: "Ejercicio cinemática",
            vencimiento: "5 días"
        }
    }
};

window.guardarAsignaturaSeleccionada = function (idAsignatura) {
    localStorage.setItem("doaAsignaturaSeleccionada", idAsignatura);
};

window.obtenerAsignaturaSeleccionada = function () {
    const idGuardado = localStorage.getItem("doaAsignaturaSeleccionada");

    if (idGuardado && window.DOA_ASIGNATURAS[idGuardado]) {
        return idGuardado;
    }

    return "matematicas";
};