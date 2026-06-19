<?php
require_once __DIR__ . '/../config/database.php';

class SegipController {

    // GET /api/segip/consultar/{ci}
    public static function consultar(string $ci): void {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM segip_personas WHERE ci = ?");
        $stmt->execute([$ci]);
        $persona = $stmt->fetch();

        if (!$persona) {
            jsonResponse([
                'success' => false,
                'error' => 'CI no encontrado en base de datos del SEGIP',
                'codigo' => 'SEGIP-404',
            ], 404);
        }

        jsonResponse([
            'success' => true,
            'fuente' => 'SEGIP - Servicio General de Identificación Personal',
            'consulta' => [
                'ci' => $persona['ci'],
                'ci_complemento' => $persona['ci_complemento'],
                'nombre' => $persona['nombre'],
                'apellido_paterno' => $persona['apellido_paterno'],
                'apellido_materno' => $persona['apellido_materno'],
                'nombre_completo' => trim($persona['nombre'] . ' ' . $persona['apellido_paterno'] . ' ' . $persona['apellido_materno']),
                'fecha_nacimiento' => $persona['fecha_nacimiento'],
                'lugar_nacimiento' => $persona['lugar_nacimiento'],
                'departamento' => $persona['departamento'],
                'provincia' => $persona['provincia'],
                'localidad' => $persona['localidad'],
                'genero' => $persona['genero'],
                'estado_civil' => $persona['estado_civil'],
                'profesion' => $persona['profesion'],
                'domicilio' => $persona['domicilio'],
                'fecha_emision' => $persona['fecha_emision'],
                'fecha_vencimiento' => $persona['fecha_vencimiento'],
            ],
        ]);
    }

    // GET /api/segip/consultar
    public static function consultarPorParametros(): void {
        $db = getDB();
        $query = "SELECT * FROM segip_personas WHERE 1=1";
        $params = [];

        if (!empty($_GET['ci'])) {
            $query .= " AND ci LIKE ?";
            $params[] = '%' . $_GET['ci'] . '%';
        }
        if (!empty($_GET['nombre'])) {
            $query .= " AND nombre LIKE ?";
            $params[] = '%' . $_GET['nombre'] . '%';
        }
        if (!empty($_GET['apellido'])) {
            $query .= " AND (apellido_paterno LIKE ? OR apellido_materno LIKE ?)";
            $params[] = '%' . $_GET['apellido'] . '%';
            $params[] = '%' . $_GET['apellido'] . '%';
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $personas = $stmt->fetchAll();

        jsonResponse([
            'success' => true,
            'total' => count($personas),
            'resultados' => $personas,
        ]);
    }

    // GET /api/segip/personas
    public static function listar(): void {
        $db = getDB();
        $personas = $db->query("SELECT * FROM segip_personas ORDER BY apellido_paterno ASC")->fetchAll();

        jsonResponse([
            'success' => true,
            'total' => count($personas),
            'personas' => $personas,
        ]);
    }

    // POST /api/segip/poblar
    public static function poblar(): void {
        $db = getDB();
        $input = input();
        $cantidad = $input['cantidad'] ?? $_GET['cantidad'] ?? 200;

        $db->exec("DELETE FROM segip_personas");

        $personas = self::generarPersonas((int)$cantidad);
        $stmt = $db->prepare("
            INSERT INTO segip_personas (
                ci, ci_complemento, nombre, apellido_paterno, apellido_materno,
                fecha_nacimiento, lugar_nacimiento, departamento, provincia, localidad,
                genero, estado_civil, profesion, domicilio, fecha_emision, fecha_vencimiento
            ) VALUES (
                :ci, :ci_complemento, :nombre, :apellido_paterno, :apellido_materno,
                :fecha_nacimiento, :lugar_nacimiento, :departamento, :provincia, :localidad,
                :genero, :estado_civil, :profesion, :domicilio, :fecha_emision, :fecha_vencimiento
            )
        ");

        $count = 0;
        foreach ($personas as $p) {
            try {
                $stmt->execute($p);
                $count++;
            } catch (Exception $e) {
                // skip duplicates
            }
        }

        jsonResponse([
            'success' => true,
            'mensaje' => "Base de datos SEGIP poblada con $count personas",
            'total_registros' => $count,
        ]);
    }

    private static function generarPersonas(int $cantidad): array {
        $nombresM = ['Juan', 'Carlos', 'Luis', 'Pedro', 'Jose', 'Miguel', 'Diego', 'Pablo', 'David', 'Andres',
                      'Marco', 'Victor', 'Raul', 'Jorge', 'Sergio', 'Fernando', 'Ricardo', 'Manuel', 'Oscar', 'Hugo'];
        $nombresF = ['Maria', 'Ana', 'Carmen', 'Rosa', 'Elena', 'Patricia', 'Laura', 'Sara', 'Martha', 'Julia',
                      'Ruth', 'Nancy', 'Liliana', 'Veronica', 'Monica', 'Silvia', 'Elizabeth', 'Claudia', 'Paola', 'Teresa'];
        $apellidos = ['Perez', 'Mamani', 'Quispe', 'Flores', 'Rodriguez', 'Lopez', 'Garcia', 'Martinez', 'Vargas', 'Condori',
                       'Choque', 'Morales', 'Gutierrez', 'Alvarez', 'Rojas', 'Torrez', 'Cruz', 'Huarachi', 'Pari', 'Ramos',
                       'Castro', 'Fernandez', 'Gonzales', 'Villca', 'Ticona', 'Apaza', 'Yujra', 'Callisaya', 'Lima', 'Mendoza'];
        $lugares = [['La Paz', 'Murillo', 'La Paz'], ['La Paz', 'Murillo', 'El Alto'],
                     ['Santa Cruz', 'Andres Ibañez', 'Santa Cruz'], ['Cochabamba', 'Cercado', 'Cochabamba'],
                     ['Oruro', 'Cercado', 'Oruro'], ['Potosi', 'Tomas Frias', 'Potosi'],
                     ['Chuquisaca', 'Oropeza', 'Sucre'], ['Tarija', 'Cercado', 'Tarija'],
                     ['Beni', 'Cercado', 'Trinidad'], ['Pando', 'Nicolas Suarez', 'Cobija']];
        $profesiones = ['Profesor', 'Abogado', 'Medico', 'Ingeniero', 'Contador', 'Arquitecto', 'Enfermero',
                         'Chofer', 'Comerciante', 'Agricultor', 'Ama de casa', 'Estudiante', 'Tecnico',
                         'Periodista', 'Administrador'];
        $estadosCivil = ['Soltero', 'Casado', 'Divorciado', 'Viudo', 'Soltero'];

        $personas = [];
        $usados = [];

        for ($i = 0; $i < $cantidad; $i++) {
            $sexo = rand(0, 1);
            $nombres = $sexo ? $nombresM : $nombresF;
            $genero = $sexo ? 'M' : 'F';

            $nombre = $nombres[array_rand($nombres)];
            $ap = $apellidos[array_rand($apellidos)];
            $am = $apellidos[array_rand($apellidos)];
            $lugar = $lugares[array_rand($lugares)];

            do {
                $ci = (string)rand(1000000, 9999999);
            } while (in_array($ci, $usados));
            $usados[] = $ci;

            $anio = rand(1970, 2010);
            $mes = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
            $dia = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

            $personas[] = [
                'ci' => $ci,
                'ci_complemento' => rand(0, 5) === 0 ? str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) : null,
                'nombre' => $nombre,
                'apellido_paterno' => $ap,
                'apellido_materno' => $am,
                'fecha_nacimiento' => "$anio-$mes-$dia",
                'lugar_nacimiento' => $lugar[2],
                'departamento' => $lugar[0],
                'provincia' => $lugar[1],
                'localidad' => $lugar[2],
                'genero' => $genero,
                'estado_civil' => $estadosCivil[array_rand($estadosCivil)],
                'profesion' => $profesiones[array_rand($profesiones)],
                'domicilio' => 'Calle ' . rand(1, 999) . ' #' . rand(100, 9999),
                'fecha_emision' => (rand(2015, 2024)) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'fecha_vencimiento' => (rand(2026, 2034)) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
            ];
        }

        return $personas;
    }
}
