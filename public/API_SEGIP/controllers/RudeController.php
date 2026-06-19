<?php
require_once __DIR__ . '/../config/database.php';

class RudeController {

    // POST /api/rude/estudiantes
    public static function registrar(): void {
        $data = input();
        $required = ['nombre', 'apellido', 'curso', 'gestion'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                jsonError("El campo '$field' es requerido");
            }
        }

        $db = getDB();
        $codigo = self::generarCodigo($data['gestion']);

        $stmt = $db->prepare("
            INSERT INTO estudiantes (
                codigo_rude, ci, nombre, apellido, fecha_nacimiento,
                lugar_nacimiento, nacionalidad, idioma_materno,
                discapacidad, discapacidad_tipo, departamento, provincia,
                localidad, zona, direccion, telefono, tutor_ci, tutor_nombre,
                curso, unidad_educativa, gestion
            ) VALUES (
                :codigo_rude, :ci, :nombre, :apellido, :fecha_nacimiento,
                :lugar_nacimiento, :nacionalidad, :idioma_materno,
                :discapacidad, :discapacidad_tipo, :departamento, :provincia,
                :localidad, :zona, :direccion, :telefono, :tutor_ci, :tutor_nombre,
                :curso, :unidad_educativa, :gestion
            )
        ");

        $stmt->execute([
            ':codigo_rude' => $codigo,
            ':ci' => $data['ci'] ?? null,
            ':nombre' => $data['nombre'],
            ':apellido' => $data['apellido'],
            ':fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
            ':lugar_nacimiento' => $data['lugar_nacimiento'] ?? null,
            ':nacionalidad' => $data['nacionalidad'] ?? 'Boliviana',
            ':idioma_materno' => $data['idioma_materno'] ?? 'Castellano',
            ':discapacidad' => $data['discapacidad'] ?? 0,
            ':discapacidad_tipo' => $data['discapacidad_tipo'] ?? null,
            ':departamento' => $data['departamento'] ?? null,
            ':provincia' => $data['provincia'] ?? null,
            ':localidad' => $data['localidad'] ?? null,
            ':zona' => $data['zona'] ?? null,
            ':direccion' => $data['direccion'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':tutor_ci' => $data['tutor_ci'] ?? null,
            ':tutor_nombre' => $data['tutor_nombre'] ?? null,
            ':curso' => $data['curso'],
            ':unidad_educativa' => $data['unidad_educativa'] ?? 'U.E. David Pinilla',
            ':gestion' => $data['gestion'],
        ]);

        jsonResponse([
            'success' => true,
            'codigo_rude' => $codigo,
            'mensaje' => 'Estudiante registrado en el sistema RUDE',
            'fecha_registro' => date('Y-m-d\TH:i:s'),
        ], 201);
    }

    // GET /api/rude/estudiantes/{codigo}
    public static function consultar(string $codigo): void {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM estudiantes WHERE codigo_rude = ?");
        $stmt->execute([$codigo]);
        $est = $stmt->fetch();

        if (!$est) {
            jsonError("Estudiante con código RUDE '$codigo' no encontrado", 404);
        }

        jsonResponse([
            'success' => true,
            'estudiante' => $est,
        ]);
    }

    // GET /api/rude/estudiantes
    public static function listar(): void {
        $db = getDB();
        $estudiantes = $db->query("SELECT * FROM estudiantes ORDER BY fecha_registro DESC")->fetchAll();

        jsonResponse([
            'success' => true,
            'total' => count($estudiantes),
            'estudiantes' => $estudiantes,
        ]);
    }

    // PUT /api/rude/estudiantes/{codigo}
    public static function actualizar(string $codigo): void {
        $data = input();
        $db = getDB();

        $existing = $db->prepare("SELECT id FROM estudiantes WHERE codigo_rude = ?");
        $existing->execute([$codigo]);
        if (!$existing->fetch()) {
            jsonError("Estudiante con código RUDE '$codigo' no encontrado", 404);
        }

        $campos = ['ci', 'nombre', 'apellido', 'fecha_nacimiento', 'lugar_nacimiento',
                    'nacionalidad', 'idioma_materno', 'discapacidad', 'discapacidad_tipo',
                    'departamento', 'provincia', 'localidad', 'zona', 'direccion',
                    'telefono', 'tutor_ci', 'tutor_nombre', 'curso', 'unidad_educativa', 'gestion', 'estado'];

        $sets = [];
        $params = [':codigo_rude' => $codigo];
        foreach ($campos as $c) {
            if (array_key_exists($c, $data)) {
                $sets[] = "$c = :$c";
                $params[":$c"] = $data[$c];
            }
        }

        if (empty($sets)) {
            jsonError("No hay campos para actualizar");
        }

        $sets[] = "fecha_actualizacion = datetime('now','-4 hours')";

        $sql = "UPDATE estudiantes SET " . implode(', ', $sets) . " WHERE codigo_rude = :codigo_rude";
        $db->prepare($sql)->execute($params);

        jsonResponse([
            'success' => true,
            'codigo_rude' => $codigo,
            'mensaje' => 'Estudiante actualizado en el sistema RUDE',
        ]);
    }

    // DELETE /api/rude/estudiantes/{codigo}
    public static function eliminar(string $codigo): void {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM estudiantes WHERE codigo_rude = ?");
        $stmt->execute([$codigo]);

        if ($stmt->rowCount() === 0) {
            jsonError("Estudiante con código RUDE '$codigo' no encontrado", 404);
        }

        jsonResponse([
            'success' => true,
            'mensaje' => "Estudiante $codigo eliminado del RUDE",
        ]);
    }

    // POST /api/rude/sincronizar
    public static function sincronizar(): void {
        $data = input();
        $estudiantes = $data['estudiantes'] ?? [];

        if (empty($estudiantes)) {
            jsonError("El campo 'estudiantes' es requerido y no debe estar vacío");
        }

        $db = getDB();
        $exitosos = 0;
        $fallidos = 0;
        $codigos = [];

        $stmt = $db->prepare("
            INSERT OR REPLACE INTO estudiantes (
                codigo_rude, ci, nombre, apellido, fecha_nacimiento,
                lugar_nacimiento, nacionalidad, idioma_materno,
                discapacidad, departamento, provincia, localidad,
                zona, direccion, telefono, tutor_ci, tutor_nombre,
                curso, unidad_educativa, gestion
            ) VALUES (
                :codigo_rude, :ci, :nombre, :apellido, :fecha_nacimiento,
                :lugar_nacimiento, :nacionalidad, :idioma_materno,
                :discapacidad, :departamento, :provincia, :localidad,
                :zona, :direccion, :telefono, :tutor_ci, :tutor_nombre,
                :curso, :unidad_educativa, :gestion
            )
        ");

        foreach ($estudiantes as $e) {
            try {
                $codigo = $e['codigo_rude'] ?? self::generarCodigo($e['gestion'] ?? date('Y'));
                $stmt->execute([
                    ':codigo_rude' => $codigo,
                    ':ci' => $e['ci'] ?? null,
                    ':nombre' => $e['nombre'] ?? '',
                    ':apellido' => $e['apellido'] ?? '',
                    ':fecha_nacimiento' => $e['fecha_nacimiento'] ?? null,
                    ':lugar_nacimiento' => $e['lugar_nacimiento'] ?? null,
                    ':nacionalidad' => $e['nacionalidad'] ?? 'Boliviana',
                    ':idioma_materno' => $e['idioma_materno'] ?? 'Castellano',
                    ':discapacidad' => $e['discapacidad'] ?? 0,
                    ':departamento' => $e['departamento'] ?? null,
                    ':provincia' => $e['provincia'] ?? null,
                    ':localidad' => $e['localidad'] ?? null,
                    ':zona' => $e['zona'] ?? null,
                    ':direccion' => $e['direccion'] ?? null,
                    ':telefono' => $e['telefono'] ?? null,
                    ':tutor_ci' => $e['tutor_ci'] ?? null,
                    ':tutor_nombre' => $e['tutor_nombre'] ?? null,
                    ':curso' => $e['curso'] ?? '',
                    ':unidad_educativa' => $e['unidad_educativa'] ?? 'U.E. David Pinilla',
                    ':gestion' => $e['gestion'] ?? date('Y'),
                ]);
                $codigos[] = $codigo;
                $exitosos++;
            } catch (Exception $ex) {
                $fallidos++;
            }
        }

        $db->prepare("INSERT INTO sincronizacion_log (tipo, codigos_rude, total, exitosos, fallidos) VALUES (?, ?, ?, ?, ?)")
           ->execute(['masiva', json_encode($codigos), count($estudiantes), $exitosos, $fallidos]);

        jsonResponse([
            'success' => true,
            'total' => count($estudiantes),
            'exitosos' => $exitosos,
            'fallidos' => $fallidos,
            'codigos_generados' => $codigos,
            'mensaje' => "Sincronización completada: $exitosos exitosos, $fallidos fallidos",
        ]);
    }

    // GET /api/rude/estadisticas
    public static function estadisticas(): void {
        $db = getDB();
        $total = $db->query("SELECT COUNT(*) as total FROM estudiantes")->fetch()['total'];
        $activos = $db->query("SELECT COUNT(*) as total FROM estudiantes WHERE estado='activo'")->fetch()['total'];
        $porGestion = $db->query("SELECT gestion, COUNT(*) as cantidad FROM estudiantes GROUP BY gestion ORDER BY gestion DESC")->fetchAll();
        $porCurso = $db->query("SELECT curso, COUNT(*) as cantidad FROM estudiantes GROUP BY curso ORDER BY cantidad DESC")->fetchAll();
        $sincronizaciones = $db->query("SELECT * FROM sincronizacion_log ORDER BY fecha DESC LIMIT 10")->fetchAll();

        jsonResponse([
            'success' => true,
            'unidad_educativa' => 'U.E. David Pinilla',
            'estadisticas' => [
                'total_estudiantes' => (int)$total,
                'estudiantes_activos' => (int)$activos,
                'por_gestion' => $porGestion,
                'por_curso' => $porCurso,
            ],
            'ultimas_sincronizaciones' => $sincronizaciones,
        ]);
    }

    // POST /api/integracion/registrar-desde-ci
    public static function registrarDesdeCI(): void {
        $data = input();
        $ci = $data['ci'] ?? '';
        $curso = $data['curso'] ?? '';
        $gestion = $data['gestion'] ?? date('Y');

        if (empty($ci)) {
            jsonError("El campo 'ci' es requerido");
        }
        if (empty($curso)) {
            jsonError("El campo 'curso' es requerido");
        }

        // 1. Consultar SEGIP
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM segip_personas WHERE ci = ?");
        $stmt->execute([$ci]);
        $segip = $stmt->fetch();

        if (!$segip) {
            jsonError("CI '$ci' no encontrado en SEGIP. Use POST /api/segip/poblar para poblar la base de datos.", 404);
        }

        // 2. Generar codigo RUDE
        $codigo = self::generarCodigo((int)$gestion);

        // 3. Registrar en RUDE con datos del SEGIP
        $stmt2 = $db->prepare("
            INSERT INTO estudiantes (
                codigo_rude, ci, nombre, apellido, fecha_nacimiento,
                lugar_nacimiento, nacionalidad, idioma_materno,
                departamento, provincia, localidad, direccion,
                curso, unidad_educativa, gestion
            ) VALUES (
                :codigo_rude, :ci, :nombre, :apellido, :fecha_nacimiento,
                :lugar_nacimiento, :nacionalidad, :idioma_materno,
                :departamento, :provincia, :localidad, :direccion,
                :curso, :unidad_educativa, :gestion
            )
        ");

        $apellido = trim(($segip['apellido_paterno'] ?? '') . ' ' . ($segip['apellido_materno'] ?? ''));
        $stmt2->execute([
            ':codigo_rude' => $codigo,
            ':ci' => $segip['ci'],
            ':nombre' => $segip['nombre'],
            ':apellido' => $apellido,
            ':fecha_nacimiento' => $segip['fecha_nacimiento'],
            ':lugar_nacimiento' => $segip['lugar_nacimiento'],
            ':nacionalidad' => 'Boliviana',
            ':idioma_materno' => 'Castellano',
            ':departamento' => $segip['departamento'],
            ':provincia' => $segip['provincia'],
            ':localidad' => $segip['localidad'],
            ':direccion' => $segip['domicilio'],
            ':curso' => $curso,
            ':unidad_educativa' => $data['unidad_educativa'] ?? 'U.E. David Pinilla',
            ':gestion' => $gestion,
        ]);

        // 4. Log de integracion
        $db->prepare("INSERT INTO integracion_log (tipo_consulta, ci, codigo_rude_generado, datos_enviados, respuesta_segip) VALUES (?, ?, ?, ?, ?)")
           ->execute([
               'SEGIP->RUDE',
               $ci,
               $codigo,
               json_encode($data, JSON_UNESCAPED_UNICODE),
               json_encode($segip, JSON_UNESCAPED_UNICODE),
           ]);

        jsonResponse([
            'success' => true,
            'codigo_rude' => $codigo,
            'mensaje' => 'Estudiante registrado desde datos del SEGIP',
            'datos_segip' => [
                'nombre_completo' => $segip['nombre'] . ' ' . $apellido,
                'fecha_nacimiento' => $segip['fecha_nacimiento'],
                'departamento' => $segip['departamento'],
            ],
            'curso' => $curso,
            'gestion' => $gestion,
        ], 201);
    }

    // GET /api/integracion/log
    public static function integracionLog(): void {
        $db = getDB();
        $logs = $db->query("SELECT * FROM integracion_log ORDER BY fecha DESC LIMIT 50")->fetchAll();
        jsonResponse(['success' => true, 'total' => count($logs), 'logs' => $logs]);
    }

    private static function generarCodigo(int $gestion): string {
        $db = getDB();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM estudiantes WHERE gestion = ?");
        $stmt->execute([$gestion]);
        $count = (int)$stmt->fetch()['total'] + 1;
        return sprintf("RUDE-%d-%06d", $gestion, $count);
    }
}
