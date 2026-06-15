<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Usuario;
use App\Models\Estudiante;
use App\Models\Docente;
use App\Models\Rol;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Padre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::with('rol');

        if ($request->filled('rol')) {
            $query->where('id_rol', $request->rol);
        }

        if ($request->filled('id_curso')) {
            $query->whereHas('estudiante', function ($q) use ($request) {
                $q->where('id_curso', $request->id_curso);
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('apellido', 'like', "%{$s}%")
                  ->orWhere('ci', 'like', "%{$s}%")
                  ->orWhere('email_hash', hash('sha256', strtolower(trim($s))));
            });
        }

        $usuarios = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Rol::where('activo', true)->get();
        $cursos = Curso::where('activo', true)->get();

        foreach ($usuarios as $u) {
            try {
                $u->email_decrypted = $u->email_cifrado ? Crypt::decryptString($u->email_cifrado) : '—';
            } catch (\Exception $e) {
                $u->email_decrypted = '—';
            }
        }

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'cursos'));
    }

    public function create()
    {
        $roles = Rol::where('activo', true)->get();
        $cursos = Curso::where('activo', true)->get();
        $materias = Materia::where('activo', true)->get();
        $asignaciones = Asignacion::where('activo', true)->with('materia')->get();
        return view('admin.usuarios.form', compact('roles', 'cursos', 'materias', 'asignaciones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ci' => 'required|string|max:20|unique:usuarios,ci',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'id_rol' => 'required|exists:roles,id_rol',
            'estado' => 'required|in:activo,inactivo',
            'id_curso' => 'required_if:id_rol,estudiante|exists:cursos,id_curso',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|in:masculino,femenino,otro,prefiero_no_decir',
            'especialidad' => 'nullable|string|max:120',
            'titulo_academico' => 'nullable|string|max:120',
            'ocupacion' => 'nullable|string|max:100',
            'parentesco' => 'nullable|in:padre,madre,tutor_legal,abuelo,otro',
            'estudiantes' => 'nullable|array',
            'estudiantes.*' => 'exists:estudiantes,id_estudiante',
            'padre_nombre' => 'nullable|string|max:100',
            'padre_apellido' => 'nullable|string|max:100',
            'padre_telefono' => 'nullable|string|max:20',
            'padre_parentesco' => 'nullable|in:padre,madre,tutor_legal,abuelo,otro',
            'padre_ocupacion' => 'nullable|string|max:100',
            'padre_ci' => 'nullable|string|max:20',
            'padre_existente' => 'nullable|integer',
        ]);

        $rol = Rol::findOrFail($validated['id_rol']);
        $email = '';
        $ci = '';

        DB::transaction(function () use ($validated, $rol, &$usuario, &$email, &$ci) {
            $ci = $validated['ci'];
            $password = $ci . 'davpin';

            $lastId = Usuario::max('id_usuario') + 1;
            $email = strtolower(substr($validated['nombre'], 0, 3) . $validated['apellido'] . substr($lastId, 0, 3)) . '@davidpinilla.com';

            $existe = Usuario::where('email_hash', hash('sha256', strtolower(trim($email))))->first();
            if ($existe) {
                $email = strtolower(substr($validated['nombre'], 0, 3) . $validated['apellido'] . $lastId) . '@davidpinilla.com';
            }

            $usuario = Usuario::create([
                'id_rol' => $validated['id_rol'],
                'ci' => $ci,
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'email_cifrado' => Crypt::encryptString($email),
                'email_hash' => hash('sha256', strtolower(trim($email))),
                'password_hash' => Hash::driver('argon2id')->make($password),
                'telefono' => $validated['telefono'],
                'estado' => $validated['estado'],
            ]);

            $rolNombre = strtolower($rol->nombre);

            if ($rolNombre === 'estudiante') {
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
                } elseif (!empty($validated['padre_nombre']) && !empty($validated['padre_apellido'])) {
                    $rolPadre = Rol::where('nombre', 'padre_familia')->firstOrFail();
                    $padreLastId = Usuario::max('id_usuario') + 1;
                    $padreEmail = strtolower(substr($validated['padre_nombre'], 0, 3) . $validated['padre_apellido'] . substr($padreLastId, 0, 3)) . '@davidpinilla.com';
                    $padrePassword = $ci . 'davpin';

                    $padreUsuario = Usuario::create([
                        'id_rol' => $rolPadre->id_rol,
                        'ci' => $validated['padre_ci'] ?? null,
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
            }

            if ($rolNombre === 'docente') {
                $codigo = 'DOC' . str_pad($usuario->id_usuario, 5, '0', STR_PAD_LEFT);
                Docente::create([
                    'id_usuario' => $usuario->id_usuario,
                    'codigo_docente' => $codigo,
                    'especialidad' => $validated['especialidad'] ?? null,
                    'titulo_academico' => $validated['titulo_academico'] ?? null,
                    'fecha_ingreso' => now(),
                    'activo' => true,
                ]);
            }

            if ($rolNombre === 'padre_familia') {
                $padre = Padre::create([
                    'id_usuario' => $usuario->id_usuario,
                    'parentesco' => $validated['parentesco'] ?? 'otro',
                    'ocupacion' => $validated['ocupacion'] ?? null,
                ]);

                if (!empty($validated['estudiantes'])) {
                    $inserts = [];
                    foreach ($validated['estudiantes'] as $idEst) {
                        $inserts[] = [
                            'id_estudiante' => $idEst,
                            'id_padre' => $padre->id_padre,
                            'es_contacto_principal' => 1,
                        ];
                    }
                    DB::table('estudiante_tutor')->insert($inserts);
                }
            }
        });

        $msg = 'Usuario creado. Email: ' . $email . ' - Contraseña: ' . $ci . 'davpin';
        if ($request->filled('padre_existente')) {
            $msg .= ' Padre vinculado correctamente.';
        } elseif (!empty($validated['padre_nombre'])) {
            $msg .= ' Cuenta del padre/madre creada y vinculada.';
        }
        return redirect()->route('admin.usuarios.index')->with('success', $msg);
    }

    public function show($id)
    {
        return redirect()->route('admin.usuarios.edit', $id);
    }

    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        try {
            $usuario->email_decrypted = $usuario->email_cifrado ? Crypt::decryptString($usuario->email_cifrado) : '—';
        } catch (\Exception $e) {
            $usuario->email_decrypted = '—';
        }
        $roles = Rol::where('activo', true)->get();
        $cursos = Curso::where('activo', true)->get();
        $materias = Materia::where('activo', true)->get();
        $asignaciones = Asignacion::where('activo', true)->with('materia')->get();
        return view('admin.usuarios.form', compact('usuario', 'roles', 'cursos', 'materias', 'asignaciones'));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $validated = $request->validate([
            'ci' => 'required|string|max:20|unique:usuarios,ci,' . $id . ',id_usuario',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'id_rol' => 'required|exists:roles,id_rol',
            'estado' => 'required|in:activo,inactivo',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'ci' => $validated['ci'],
            'id_rol' => $validated['id_rol'],
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'telefono' => $validated['telefono'],
            'estado' => $validated['estado'],
        ];

        if ($request->filled('password')) {
            $data['password_hash'] = Hash::driver('argon2id')->make($validated['password']);
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->update(['estado' => 'inactivo']);
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario desactivado correctamente.');
    }
}
