<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Materia;
use App\Models\Curso;
use App\Models\Asignacion;
use App\Models\Horario;
use App\Models\Docente;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\AnioLectivo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class GenerarHorarios extends Command
{
    protected $signature = 'horarios:generar';
    protected $description = 'Genera asignaciones y horarios con las nuevas reglas';

    protected $slots = [
        ['hora_inicio' => '14:00:00', 'hora_fin' => '14:40:00'],
        ['hora_inicio' => '14:40:00', 'hora_fin' => '15:20:00'],
        ['hora_inicio' => '15:20:00', 'hora_fin' => '16:00:00'],
        ['hora_inicio' => '16:30:00', 'hora_fin' => '17:10:00'],
        ['hora_inicio' => '17:10:00', 'hora_fin' => '17:50:00'],
    ];

    protected $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];

    public function handle()
    {
        $this->info('=== Generación de Horarios ===');

        $anio = AnioLectivo::where('activo', true)->firstOrFail();
        $cursos = Curso::where('id_curso', '>', 1)->orderBy('id_nivel')->orderBy('nombre')->get();

        if (!$this->confirm("Se eliminarán TODOS los horarios y asignaciones actuales. ¿Continuar?")) {
            return 0;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Horario::query()->delete();
        Asignacion::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $teacherIds = $this->crearDocentesFaltantes();
        $this->info('Docentes listos.');

        $subjectMap = $this->getMaterias();

        $data = $this->buildAsignacionesData($cursos, $subjectMap, $teacherIds, $anio->id_anio);

        foreach ($data as $row) {
            Asignacion::create($row);
        }
        $this->info(count($data) . ' asignaciones creadas.');

        $asignaciones = Asignacion::with('materia', 'curso')->get()->groupBy('id_curso');

        $this->generarHorarios($asignaciones, $teacherIds);

        $this->info('Horarios generados exitosamente.');
        return 0;
    }

    protected function crearDocentesFaltantes()
    {
        $rolDocente = Rol::where('nombre', 'docente')->firstOrFail();

        $nuevos = [
            ['nombre' => 'Roberto', 'ci' => '20000001'],
            ['nombre' => 'Laura',   'ci' => '20000002'],
            ['nombre' => 'Fernando','ci' => '20000003'],
        ];

        foreach ($nuevos as $n) {
            $user = Usuario::where('ci', $n['ci'])->first();
            if (!$user) {
                $user = Usuario::create([
                    'nombre' => $n['nombre'],
                    'apellido' => '',
                    'email_cifrado' => $n['nombre'] . '@docente.com',
                    'email_hash' => md5($n['nombre'] . '@docente.com'),
                    'password_hash' => Hash::make($n['ci'] . 'davpin'),
                    'id_rol' => $rolDocente->id_rol,
                    'ci' => $n['ci'],
                    'activo' => true,
                ]);
            }
            $docente = Docente::where('id_usuario', $user->id_usuario)->first();
            if (!$docente) {
                $maxCode = Docente::max('codigo_docente');
                $nextNum = $maxCode ? ((int)substr($maxCode, 3) + 1) : 1;
                Docente::create([
                    'id_usuario' => $user->id_usuario,
                    'codigo_docente' => 'DOC' . str_pad($nextNum, 4, '0', STR_PAD_LEFT),
                ]);
                $this->info("Docente creado: {$n['nombre']} (CI: {$n['ci']})");
            }
        }

        $userIdToDocenteId = function($userId) {
            $d = Docente::where('id_usuario', $userId)->first();
            return $d ? $d->id_docente : null;
        };

        return [
            'jhamil'  => $userIdToDocenteId(Usuario::where('nombre', 'Jhamil')->first()->id_usuario),
            'maria'   => $userIdToDocenteId(Usuario::where('nombre', 'María')->first()->id_usuario),
            'carlo'   => $userIdToDocenteId(Usuario::where('nombre', 'Carlo')->first()->id_usuario),
            'jose'    => $userIdToDocenteId(Usuario::where('nombre', 'José')->first()->id_usuario),
            'ana'     => $userIdToDocenteId(Usuario::where('nombre', 'Ana')->first()->id_usuario),
            'diego'   => $userIdToDocenteId(Usuario::where('nombre', 'Diego')->first()->id_usuario),
            'sofia'   => $userIdToDocenteId(Usuario::where('nombre', 'Sofía')->first()->id_usuario),
            'pablo'   => $userIdToDocenteId(Usuario::where('nombre', 'Pablo')->first()->id_usuario),
            'valentina' => $userIdToDocenteId(Usuario::where('nombre', 'Valentina')->first()->id_usuario),
            'andres'  => $userIdToDocenteId(Usuario::where('nombre', 'Andrés')->first()->id_usuario),
            'lucia'   => $userIdToDocenteId(Usuario::where('nombre', 'Lucía')->first()->id_usuario),
            'carmen'  => $userIdToDocenteId(Usuario::where('ci', '1234567')->first()->id_usuario),
            'roberto' => $userIdToDocenteId(Usuario::where('ci', '20000001')->first()->id_usuario),
            'laura'   => $userIdToDocenteId(Usuario::where('ci', '20000002')->first()->id_usuario),
            'fernando'=> $userIdToDocenteId(Usuario::where('ci', '20000003')->first()->id_usuario),
        ];
    }

    protected function getMaterias()
    {
        $map = [];
        foreach (Materia::all() as $m) {
            $key = $this->normalize($m->nombre);
            $map[$key] = $m->id_materia;
        }
        return $map;
    }

    protected function materiaLookup($map, $name)
    {
        $key = $this->normalize($name);
        return $map[$key] ?? null;
    }

    protected function normalize($str)
    {
        $str = mb_strtolower($str, 'UTF-8');
        $search  = ['á','é','í','ó','ú','ü','ñ','Á','É','Í','Ó','Ú','Ü','Ñ'];
        $replace = ['a','e','i','o','u','u','n','a','e','i','o','u','u','n'];
        return str_replace($search, $replace, $str);
    }

    protected function buildAsignacionesData($cursos, $m, $t, $idAnio)
    {
        $rows = [];
        $mathTeachers = [];

        foreach ($cursos as $curso) {
            $grado = (int)$curso->grado;

            if ($grado <= 2) {
                // 1°-2° (6 courses): 7 subjects
                $subjects = [
                    ['materia' => 'Matemáticas',        'docente' => $grado == 1 ? $t['maria'] : $t['ana']],
                    ['materia' => 'Lenguaje',            'docente' => $t['jose']],
                    ['materia' => 'Técnica Vocacional',  'docente' => $t['carlo']],
                    ['materia' => 'Música',              'docente' => $t['valentina']],
                    ['materia' => 'Biología',            'docente' => $t['diego']],
                    ['materia' => 'Ciencias Sociales',   'docente' => $t['pablo']],
                    ['materia' => 'Educación Física',    'docente' => $t['lucia']],
                ];
            } else {
                // 3°-6° (10 courses): 12 subjects
                if ($grado == 3) {
                    $mathTeacher = $t['jhamil'];
                } elseif ($grado == 4) {
                    $mathTeacher = $t['ana'];
                } elseif ($grado == 5) {
                    $mathTeacher = $t['carlo'];
                } else {
                    $mathTeacher = $t['maria'];
                }

                $litTeacher = ($grado <= 4) ? $t['sofia'] : $t['fernando'];
                $efTeacher = ($grado <= 4) ? $t['laura'] : $t['lucia'];
                $musicaTeacher = ($grado <= 4) ? $t['roberto'] : $t['valentina'];

                $subjects = [
                    ['materia' => 'Matemáticas',       'docente' => $mathTeacher],
                    ['materia' => 'Literatura',        'docente' => $litTeacher],
                    ['materia' => 'Técnica Vocacional','docente' => $t['jhamil']],
                    ['materia' => 'Música',            'docente' => $musicaTeacher],
                    ['materia' => 'Psicología',        'docente' => $t['andres']],
                    ['materia' => 'Filosofía',         'docente' => $t['andres']],
                    ['materia' => 'Artes Plásticas',   'docente' => $t['maria']],
                    ['materia' => 'Biología',          'docente' => $t['diego']],
                    ['materia' => 'Física',            'docente' => $t['carmen']],
                    ['materia' => 'Química',           'docente' => $t['carlo']],
                    ['materia' => 'Ciencias Sociales', 'docente' => $t['pablo']],
                    ['materia' => 'Educación Física',  'docente' => $efTeacher],
                ];
            }

            foreach ($subjects as $s) {
                $mid = $this->materiaLookup($m, $s['materia']);
                if (!$mid) {
                    $this->warn("Materia no encontrada: {$s['materia']}");
                    continue;
                }
                $rows[] = [
                    'id_docente' => $s['docente'],
                    'id_materia' => $mid,
                    'id_curso'   => $curso->id_curso,
                    'id_anio'    => $idAnio,
                    'activo'     => true,
                ];
            }
        }

        return $rows;
    }

    protected function generarHorarios($asignacionesPorCurso, $t)
    {
        $teacherSchedule = [];
        foreach ($t as $user_id) {
            $teacherSchedule[$user_id] = [];
        }

        $courseSchedule = []; // [id_curso][day][time] = true

        $generated = 0;
        $materiaIds = [
            'matematicas' => $this->getMateriaId('Matemáticas'),
            'literatura'  => $this->getMateriaId('Literatura'),
            'lenguaje'    => $this->getMateriaId('Lenguaje'),
            'ef'          => $this->getMateriaId('Educación Física'),
            'musica'      => $this->getMateriaId('Música'),
        ];

        // Build assignment plan: array of [needed_slots, subject_name, curso, id_asignacion, id_docente, preferred_zone]
        $allAssignments = [];

        foreach ($asignacionesPorCurso as $idCurso => $asigs) {
            $curso = $asigs->first()->curso;
            $grado = (int)$curso->grado;
            $esTercero = ($grado == 3);

            foreach ($asigs as $a) {
                $mid = $a->id_materia;
                $slots = 1;  // default: 1 slot
                $zone = 'any'; // any slot

                if ($mid == $materiaIds['matematicas'] || $mid == $materiaIds['literatura'] || $mid == $materiaIds['lenguaje']) {
                    $slots = 3;
                    $zone = 'any';
                } elseif ($mid == $materiaIds['ef']) {
                    $slots = 2;
                    $zone = 'before_recess'; // prefer first 3 slots
                } elseif ($mid == $materiaIds['musica']) {
                    $slots = 2;
                    $zone = 'after_recess'; // prefer last 2 slots
                }

                $allAssignments[] = [
                    'slots' => $slots,
                    'zone' => $zone,
                    'curso' => $curso,
                    'id_curso' => $idCurso,
                    'id_asignacion' => $a->id_asignacion,
                    'id_docente' => $a->id_docente,
                    'materia' => $a->materia,
                    'es_tercero' => $esTercero,
                ];
            }
        }

        // Sort: full-day first, then EF, then Música, then regular
        usort($allAssignments, fn($a, $b) => $b['slots'] <=> $a['slots']);

        $tryAssign = function ($ass, $dias, $slotsToTry, $neededLeft) use (&$teacherSchedule, &$courseSchedule, &$generated) {
            $done = 0;
            $teacherId = $ass['id_docente'];
            $cursoId = $ass['id_curso'];
            foreach ($dias as $dia) {
                if ($done >= $neededLeft) break;
                foreach ($slotsToTry as $si) {
                    if ($done >= $neededLeft) break;
                    $sl = $this->slots[$si];
                    if (!isset($teacherSchedule[$teacherId][$dia][$sl['hora_inicio']])
                        && !isset($courseSchedule[$cursoId][$dia][$sl['hora_inicio']])) {
                        $this->createHorario($ass['id_asignacion'], $dia, $sl['hora_inicio'], $sl['hora_fin']);
                        $teacherSchedule[$teacherId][$dia][$sl['hora_inicio']] = true;
                        $courseSchedule[$cursoId][$dia][$sl['hora_inicio']] = $ass['id_asignacion'];
                        $generated++;
                        $done++;
                    }
                }
            }
            return $done;
        };

        foreach ($allAssignments as $ass) {
            $diasParaCurso = $ass['es_tercero']
                ? array_values(array_filter($this->dias, fn($d) => $d !== 'lunes'))
                : $this->dias;

            $needed = $ass['slots'];
            $assigned = 0;

            // Determine slot range preference
            $slotRange = $ass['zone'] === 'after_recess' ? [3, 4] : ($ass['zone'] === 'before_recess' ? [0, 1, 2] : [0, 1, 2, 3, 4]);

            // Try preferred zone first
            $assigned += $tryAssign($ass, $diasParaCurso, $slotRange, $needed - $assigned);

            // If not enough slots in preferred zone, try other slots
            if ($assigned < $needed) {
                $otherSlots = array_values(array_diff([0, 1, 2, 3, 4], $slotRange));
                $assigned += $tryAssign($ass, $diasParaCurso, $otherSlots, $needed - $assigned);
            }

            if ($assigned < $needed) {
                $this->warn("{$ass['materia']->nombre} para {$ass['curso']->nombre}: solo $assigned de $needed slots");
            }
        }

        // FILL PHASE: round-robin across courses, shuffle subjects for fairness
        $filled = 0;
        $cursoIds = $asignacionesPorCurso->keys()->all();

        for ($pass = 0; $pass < 20; $pass++) {
            $anyFilled = false;
            foreach ($cursoIds as $idCurso) {
                $asigs = $asignacionesPorCurso[$idCurso];
                $curso = $asigs->first()->curso;
                $grado = (int)$curso->grado;
                $esTercero = ($grado == 3);
                $diasParaCurso = $esTercero
                    ? array_values(array_filter($this->dias, fn($d) => $d !== 'lunes'))
                    : $this->dias;

                $shuffled = $asigs->shuffle();
                $found = false;
                foreach ($diasParaCurso as $dia) {
                    if ($found) break;
                    foreach ($this->slots as $si => $sl) {
                        if ($found) break;
                        foreach ($shuffled as $a) {
                            $teacherId = $a->id_docente;
                            if (!isset($teacherSchedule[$teacherId][$dia][$sl['hora_inicio']])
                                && !isset($courseSchedule[$idCurso][$dia][$sl['hora_inicio']])) {
                                $this->createHorario($a->id_asignacion, $dia, $sl['hora_inicio'], $sl['hora_fin']);
                                $teacherSchedule[$teacherId][$dia][$sl['hora_inicio']] = true;
                                $courseSchedule[$idCurso][$dia][$sl['hora_inicio']] = $a->id_asignacion;
                                $generated++;
                                $filled++;
                                $anyFilled = true;
                                $found = true;
                                break;
                            }
                        }
                    }
                }
            }
            if (!$anyFilled) break;
        }

        $this->info("Total horarios generados: $generated (fill phase: $filled)");
    }

    protected function getMateriaId($nombre)
    {
        static $map = null;
        if ($map === null) {
            $map = $this->getMaterias();
        }
        return $this->materiaLookup($map, $nombre);
    }

    protected function createHorario($idAsignacion, $dia, $horaInicio, $horaFin)
    {
        Horario::create([
            'id_asignacion' => $idAsignacion,
            'dia_semana'    => $dia,
            'hora_inicio'   => $horaInicio,
            'hora_fin'      => $horaFin,
            'activo'        => true,
        ]);
    }

    protected function isTeacherBusy(&$schedule, $teacherId, $day, $time)
    {
        return isset($schedule[$teacherId][$day][$time]);
    }

    protected function markBusy(&$schedule, $teacherId, $day, $time)
    {
        $schedule[$teacherId][$day][$time] = true;
    }
}
