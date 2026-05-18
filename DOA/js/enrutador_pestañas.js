/*
    Enrutador Universal de Pestañas
*/

function enrutarPestanasAsignatura() {
    const texto_rol = document.getElementById('rolUsuarioHeader');
    let es_profesor = false;

    if (texto_rol) {
        const rol_usuario = texto_rol.textContent.toLowerCase();
        es_profesor = rol_usuario.includes('profesor') || rol_usuario.includes('docente');
    }

    const lista_pestanas = document.querySelectorAll('.pestanas-asignatura__item');

    for (let i = 0; i < lista_pestanas.length; i++) {
        const pestana_actual = lista_pestanas[i];
        const nombre_pestana = pestana_actual.textContent.toLowerCase().trim();

        if (nombre_pestana === 'recursos') {
            pestana_actual.href = es_profesor ? 'recursosdoa.html' : 'Recursosdoaalumno.html';
        }
        else if (nombre_pestana === 'tareas') {
            pestana_actual.href = 'listado_tareas.html';
        }
        else if (nombre_pestana === 'exámenes' || nombre_pestana === 'examenes') {
            pestana_actual.href = 'examenes.html';
        }
        else if (nombre_pestana === 'calificaciones') {
            pestana_actual.href = 'calificaciones.html';
        }
    }
}

// Llamada directa al cargar el script
enrutarPestanasAsignatura();