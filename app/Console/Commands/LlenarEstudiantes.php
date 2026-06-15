<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Estudiante;
use App\Models\Curso;
use App\Models\Asignacion;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class LlenarEstudiantes extends Command
{
    protected $signature = 'estudiantes:llenar';
    protected $description = 'Llena estudiantes en todos los cursos';

    private $nombresFemeninos = [
        'Camila', 'Valentina', 'Sofía', 'Isabella', 'Luciana', 'Martina', 'Gabriela',
        'Daniela', 'Andrea', 'Fernanda', 'Alejandra', 'Paola', 'Carolina', 'Natalia',
        'Ximena', 'Mariana', 'Renata', 'Josefina', 'Antonella', 'Guadalupe', 'Victoria',
        'Florencia', 'Bianca', 'Cecilia', 'Verónica', 'Patricia', 'Silvia', 'Ruth',
        'Elena', 'Rosa', 'Mónica', 'Claudia', 'Lourdes', 'Marisol', 'Johana', 'Noelia',
        'Lidia', 'Esther', 'Marlene', 'Beatriz', 'Gloria', 'Maribel', 'Juana', 'Nancy',
    ];

    private $nombresMasculinos = [
        'Santiago', 'Mateo', 'Sebastián', 'Nicolás', 'Matías', 'Benjamín', 'Diego',
        'Alejandro', 'Andrés', 'Joaquín', 'Gabriel', 'David', 'José', 'Daniel',
        'Samuel', 'Emilio', 'Tomás', 'Ángel', 'Miguel', 'Jorge', 'Carlos', 'Luis',
        'Pedro', 'Juan', 'Pablo', 'Marcelo', 'Fernando', 'Gustavo', 'Oscar', 'Raúl',
        'Rubén', 'Hugo', 'Iván', 'Ramiro', 'Víctor', 'Eduardo', 'Roberto', 'Marco',
        'Francisco', 'Ricardo', 'Julio', 'Alberto', 'Mauricio', 'Edwin', 'Rodrigo',
    ];

    private $apellidos = [
        'García', 'Rodríguez', 'Martínez', 'López', 'Hernández', 'González', 'Pérez',
        'Quispe', 'Mamani', 'Flores', 'Condori', 'Morales', 'Vargas', 'Ríos', 'Cruz',
        'Torres', 'Álvarez', 'Gutiérrez', 'Rivera', 'Rojas', 'Miranda', 'Chávez',
        'Romero', 'Moreno', 'Jiménez', 'Hurtado', 'Mendoza', 'Castro', 'Ortiz',
        'Salazar', 'Paredes', 'Zeballos', 'Céspedes', 'Villarroel', 'Carrasco',
        'Vaca', 'Aguilar', 'Navarro', 'Peña', 'Roca', 'Velasco', 'Soliz', 'Camacho',
        'Valencia', 'Sandoval', 'Cárdenas', 'Molina', 'Serrano', 'Padilla', 'Castillo',
    ];

    public function handle()
    {
        $this->info('=== Llenado de Estudiantes ===');

        $rolEstudiante = Rol::where('nombre', 'Estudiante')->firstOrFail();
        $cursos = Curso::where('activo', true)->where('id_curso', '>', 1)->orderBy('id_nivel')->orderBy('nombre')->get();

        $created = 0;
        $ciBase = 10000000;

        foreach ($cursos as $curso) {
            $asignaciones = Asignacion::where('id_curso', $curso->id_curso)->where('activo', true)->pluck('id_materia')->toArray();
            $existingCount = Estudiante::where('id_curso', $curso->id_curso)->count();
            $targetPerCourse = 20;
            $totalToCreate = max(0, $targetPerCourse - $existingCount);

            if ($totalToCreate <= 0) {
                $this->warn("{$curso->nombre} ya tiene $existingCount estudiantes");
                continue;
            }

            $this->info("{$curso->nombre}: creando $totalToCreate estudiantes...");

            $g = mt_rand(0, 1);
            $nombresLista = $g ? $this->nombresMasculinos : $this->nombresFemeninos;

            for ($i = 0; $i < $totalToCreate; $i++) {
                $nombre = $nombresLista[array_rand($nombresLista)];
                $apellido1 = $this->apellidos[array_rand($this->apellidos)];
                $apellido2 = $this->apellidos[array_rand($this->apellidos)];
                $apellido = "$apellido1 $apellido2";
                $ci = $ciBase + $created;

                // Ensure unique CI
                $attempts = 0;
                while (Usuario::where('ci', (string)$ci)->exists() && $attempts < 100) {
                    $ci++;
                    $attempts++;
                }

                if ($attempts >= 100) {
                    $this->error("No se pudo generar CI único después de 100 intentos");
                    continue;
                }

                $password = $ci . 'davpin';
                $lastId = Usuario::max('id_usuario') + 1;
                $email = strtolower(substr($nombre, 0, 3) . str_replace(' ', '', $apellido1) . $lastId) . '@davidpinilla.com';

                DB::transaction(function () use ($rolEstudiante, $ci, $nombre, $apellido, $email, $password, $curso, $asignaciones, &$created) {
                    $usuario = Usuario::create([
                        'id_rol' => $rolEstudiante->id_rol,
                        'ci' => (string)$ci,
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'email_cifrado' => Crypt::encryptString($email),
                        'email_hash' => hash('sha256', strtolower(trim($email))),
                        'password_hash' => Hash::driver('argon2id')->make($password),
                        'estado' => 'activo',
                    ]);

                    $codigo = 'EST' . str_pad($usuario->id_usuario, 5, '0', STR_PAD_LEFT);

                    $estudiante = Estudiante::create([
                        'id_usuario' => $usuario->id_usuario,
                        'id_curso' => $curso->id_curso,
                        'codigo_estudiante' => $codigo,
                        'activo' => true,
                    ]);

                    // Link to course subjects
                    foreach ($asignaciones as $mid) {
                        DB::table('estudiante_materia')->insert([
                            'id_estudiante' => $estudiante->id_estudiante,
                            'id_materia' => $mid,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $created++;
                });
            }
        }

        $this->info("Total estudiantes creados: $created");
    }
}
