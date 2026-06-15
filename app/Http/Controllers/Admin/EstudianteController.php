<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Estudiante;
use App\Models\Usuario;
use App\Models\Curso;
use App\Models\Rol;
use App\Models\Padre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class EstudianteController extends Controller
{
    public function index(Request $request)
    {
        $query = Estudiante::with(['usuario', 'curso'])->where('activo', true);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('usuario', function ($q) use ($s) {
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('apellido', 'like', "%{$s}%");
            })->orWhere('codigo_estudiante', 'like', "%{$s}%");
        }

        if ($request->filled('id_curso')) {
            $query->where('id_curso', $request->id_curso);
        }

        $estudiantes = $query->orderBy('created_at', 'desc')->paginate(15);
        $cursos = Curso::where('activo', true)->get();

        return view('admin.estudiantes.index', compact('estudiantes', 'cursos'));
    }

    public function create()
    {
        $cursos = Curso::where('activo', true)->get();
        $materias = \App\Models\Materia::where('activo', true)->get();
        $asignaciones = Asignacion::where('activo', true)->with('materia')->get();
        return view('admin.estudiantes.form', compact('cursos', 'materias', 'asignaciones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ci' => 'required|string|max:20|unique:usuarios,ci',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'id_curso' => 'required|exists:cursos,id_curso',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|in:masculino,femenino,otro,prefiero_no_decir',
            'padre_nombre' => 'nullable|string|max:100',
            'padre_apellido' => 'nullable|string|max:100',
            'padre_telefono' => 'nullable|string|max:20',
            'padre_parentesco' => 'nullable|in:padre,madre,tutor_legal,abuelo,otro',
            'padre_ocupacion' => 'nullable|string|max:100',
            'padre_ci' => 'nullable|string|max:20',
            'padre_existente' => 'nullable|integer',
        ]);

        $rolEstudiante = Rol::where('nombre', 'Estudiante')->firstOrFail();

        DB::transaction(function () use ($validated, $rolEstudiante, $request, &$estudiante) {
            $lastId = Usuario::max('id_usuario') + 1;
            $email = strtolower(substr($validated['nombre'], 0, 3) . $validated['apellido'] . substr($lastId, 0, 3)) . '@davidpinilla.com';

            $existe = Usuario::where('email_hash', hash('sha256', strtolower(trim($email))))->first();
            if ($existe) {
                $email = strtolower(substr($validated['nombre'], 0, 3) . $validated['apellido'] . $lastId) . '@davidpinilla.com';
            }

            $password = $validated['ci'] . 'davpin';

            $usuario = Usuario::create([
                'id_rol' => $rolEstudiante->id_rol,
                'ci' => $validated['ci'],
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'email_cifrado' => Crypt::encryptString($email),
                'email_hash' => hash('sha256', strtolower(trim($email))),
                'password_hash' => Hash::driver('argon2id')->make($password),
                'telefono' => $validated['telefono'],
                'estado' => 'activo',
            ]);

            $codigo = 'EST' . str_pad($usuario->id_usuario, 5, '0', STR_PAD_LEFT);

            $estudiante = Estudiante::create([
                'id_usuario' => $usuario->id_usuario,
                'id_curso' => $validated['id_curso'],
                'codigo_estudiante' => $codigo,
                'fecha_nacimiento' => $validated['fecha_nacimiento'],
                'genero' => $validated['genero'],
                'activo' => true,
            ]);

            $materiasCurso = Asignacion::where('id_curso', $validated['id_curso'])
                ->where('activo', true)
                ->pluck('id_materia')
                ->unique();
            if ($materiasCurso->isNotEmpty()) {
                $data = [];
                foreach ($materiasCurso as $idMateria) {
                    $data[] = [
                        'id_estudiante' => $estudiante->id_estudiante,
                        'id_materia' => $idMateria,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('estudiante_materia')->insert($data);
            }

            if ($request->filled('padre_existente')) {
                DB::table('estudiante_tutor')->insert([
                    'id_estudiante' => $estudiante->id_estudiante,
                    'id_padre' => $request->padre_existente,
                    'es_contacto_principal' => 1,
                ]);
            } elseif ($request->filled('padre_nombre') && $request->filled('padre_apellido')) {
                $rolPadre = Rol::where('nombre', 'padre_familia')->firstOrFail();
                $padreLastId = Usuario::max('id_usuario') + 1;
                $padreEmail = strtolower(substr($validated['padre_nombre'], 0, 3) . $validated['padre_apellido'] . substr($padreLastId, 0, 3)) . '@davidpinilla.com';
                $padrePassword = $validated['ci'] . 'davpin';

                $padreUsuario = Usuario::create([
                    'id_rol' => $rolPadre->id_rol,
                    'ci' => $request->padre_ci,
                    'nombre' => $validated['padre_nombre'],
                    'apellido' => $validated['padre_apellido'],
                    'email_cifrado' => Crypt::encryptString($padreEmail),
                    'email_hash' => hash('sha256', strtolower(trim($padreEmail))),
                    'password_hash' => Hash::driver('argon2id')->make($padrePassword),
                    'telefono' => $validated['padre_telefono'],
                    'estado' => 'activo',
                ]);

                $padre = Padre::create([
                    'id_usuario' => $padreUsuario->id_usuario,
                    'parentesco' => $validated['padre_parentesco'] ?? 'otro',
                    'ocupacion' => $validated['padre_ocupacion'] ?? null,
                ]);

                DB::table('estudiante_tutor')->insert([
                    'id_estudiante' => $estudiante->id_estudiante,
                    'id_padre' => $padre->id_padre,
                    'es_contacto_principal' => 1,
                ]);
            }
        });

        $msg = 'Estudiante creado. Email: auto-generado. Contraseña: ' . $validated['ci'] . 'davpin';
        if ($request->filled('padre_existente')) {
            $msg .= ' Padre vinculado correctamente.';
        } elseif ($request->filled('padre_nombre')) {
            $msg .= ' Cuenta del padre/madre creada y vinculada.';
        }
        return redirect()->route('admin.estudiantes.index')->with('success', $msg);
    }

    public function edit($id)
    {
        $estudiante = Estudiante::with(['usuario', 'materias'])->findOrFail($id);
        $cursos = Curso::where('activo', true)->get();
        $materias = \App\Models\Materia::where('activo', true)->get();
        $asignaciones = Asignacion::where('activo', true)->with('materia')->get();
        return view('admin.estudiantes.form', compact('estudiante', 'cursos', 'materias', 'asignaciones'));
    }

    public function update(Request $request, $id)
    {
        $estudiante = Estudiante::with('usuario')->findOrFail($id);

        $validated = $request->validate([
            'ci' => 'required|string|max:20|unique:usuarios,ci,' . $estudiante->id_usuario . ',id_usuario',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'id_curso' => 'required|exists:cursos,id_curso',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|in:masculino,femenino,otro,prefiero_no_decir',
        ]);

        DB::transaction(function () use ($estudiante, $validated) {
            $dataUsuario = [
                'ci' => $validated['ci'],
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'telefono' => $validated['telefono'],
            ];
            $estudiante->usuario->update($dataUsuario);

            $estudiante->update([
                'id_curso' => $validated['id_curso'],
                'fecha_nacimiento' => $validated['fecha_nacimiento'],
                'genero' => $validated['genero'],
            ]);

            $materiasCurso = Asignacion::where('id_curso', $validated['id_curso'])
                ->where('activo', true)
                ->pluck('id_materia')
                ->unique();
            DB::table('estudiante_materia')->where('id_estudiante', $estudiante->id_estudiante)->delete();
            if ($materiasCurso->isNotEmpty()) {
                $data = [];
                foreach ($materiasCurso as $idMateria) {
                    $data[] = [
                        'id_estudiante' => $estudiante->id_estudiante,
                        'id_materia' => $idMateria,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('estudiante_materia')->insert($data);
            }
        });

        return redirect()->route('admin.estudiantes.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    public function destroy($id)
    {
        $estudiante = Estudiante::findOrFail($id);
        DB::transaction(function () use ($estudiante) {
            $estudiante->update(['activo' => false]);
            if ($estudiante->usuario) {
                $estudiante->usuario->update(['estado' => 'inactivo']);
            }
        });
        return redirect()->route('admin.estudiantes.index')->with('success', 'Estudiante desactivado correctamente.');
    }
}
