<?php
/*
    Usuarios de prueba de DOA.
*/

if (!function_exists('limpiarTexto')) {
    function limpiarTexto($texto)
    {
        return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('contarUsuariosPorTipo')) {
    function contarUsuariosPorTipo($usuarios, $tipo)
    {
        if ($tipo === 'todos') {
            return count($usuarios);
        }

        $total = 0;

        foreach ($usuarios as $usuario) {
            if ($usuario['tipo'] === $tipo) {
                $total++;
            }
        }

        return $total;
    }
}

if (!function_exists('buscarUsuarioPorCredenciales')) {
    function buscarUsuarioPorCredenciales($usuarios, $email, $password)
    {
        $email = strtolower(trim($email));
        $password = trim($password);

        foreach ($usuarios as $usuario) {
            if ($usuario['email'] === $email && $usuario['password'] === $password) {
                return $usuario;
            }
        }

        return null;
    }
}

return [
    'usuarios' => [
        [
            'id' => 'alumno_01',
            'dni' => '01-9218611',
            'nombre' => 'Lief Simants Dredge',
            'email' => 'l.simdre@epsg.upv.es',
            'password' => '9218611',
            'tipo' => 'alumno',
            'rol' => 'Alumno',
        ],
        [
            'id' => 'alumno_02',
            'dni' => '04-1320191',
            'nombre' => 'Merline Kirdsch Kampshell',
            'email' => 'm.kirkam@epsg.upv.es',
            'password' => '1320191',
            'tipo' => 'alumno',
            'rol' => 'Alumno',
        ],
        [
            'id' => 'alumno_03',
            'dni' => '05-9971924',
            'nombre' => 'Debora Rawstorne',
            'email' => 'd.rawabc@epsg.upv.es',
            'password' => '9971924',
            'tipo' => 'alumno',
            'rol' => 'Alumno',
        ],
        [
            'id' => 'profesor_01',
            'dni' => '60-4525956',
            'nombre' => 'Kevan Pounds Mainston',
            'email' => 'k.poumai@upv.es',
            'password' => '4525956',
            'tipo' => 'profesor',
            'rol' => 'Profesor',
        ],
        [
            'id' => 'profesor_02',
            'dni' => '64-6055365',
            'nombre' => 'Luelle Pridmore Starsmeare',
            'email' => 'l.prista@upv.es',
            'password' => '6055365',
            'tipo' => 'profesor',
            'rol' => 'Profesor',
        ],
        [
            'id' => 'profesor_03',
            'dni' => '64-6738133',
            'nombre' => 'Eolande Merriton Mizzi',
            'email' => 'e.mermiz@upv.es',
            'password' => '6738133',
            'tipo' => 'profesor',
            'rol' => 'Profesor',
        ],
        [
            'id' => 'pas_01',
            'dni' => '88-1316390',
            'nombre' => 'Ondrea Brezlaw Sherwill',
            'email' => 'o.breshe@upv.es',
            'password' => '1316390',
            'tipo' => 'pas',
            'rol' => 'Secretaría',
        ],
        [
            'id' => 'pas_02',
            'dni' => '91-1970980',
            'nombre' => 'Brooke Malimoe Thomerson',
            'email' => 'b.maltho@upv.es',
            'password' => '1970980',
            'tipo' => 'pas',
            'rol' => 'Secretaría',
        ],
    ],

    'tipos' => [
        'todos' => 'Todos',
        'alumno' => 'Alumnos',
        'profesor' => 'Profesores',
        'pas' => 'Secretaría',
    ],
];
