<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class DocenteController extends Controller
{
    public function index(Request $request)
    {
        $query = Docente::with(['usuario', 'asignaciones'])->where('activo', true);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('usuario', function ($q) use ($s) {
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('apellido', 'like', "%{$s}%");
            })->orWhere('codigo_docente', 'like', "%{$s}%")
              ->orWhere('especialidad', 'like', "%{$s}%");
        }

        $docentes = $query->orderBy('created_at', 'desc')->paginate(15);
        $docentes->each(function ($d) {
            $d->asignaciones_count = $d->asignaciones->count();
        });

        return view('admin.docentes.index', compact('docentes'));
    }

    public function create()
    {
        return view('admin.docentes.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'especialidad' => 'nullable|string|max:200',
            'titulo_academico' => 'nullable|string|max:200',
            'fecha_ingreso' => 'nullable|date',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $rolDocente = Rol::where('nombre', 'Docente')->firstOrFail();

        DB::transaction(function () use ($validated, $rolDocente, &$docente) {
            $lastId = Usuario::max('id_usuario') + 1;
            $email = strtolower(substr($validated['nombre'], 0, 3) . $validated['apellido'] . substr($lastId, 0, 3)) . '@davidpinilla.com';

            $existe = Usuario::where('email_hash', hash('sha256', strtolower(trim($email))))->first();
            if ($existe) {
                $email = strtolower(substr($validated['nombre'], 0, 3) . $validated['apellido'] . $lastId) . '@davidpinilla.com';
            }

            $usuario = Usuario::create([
                'id_rol' => $rolDocente->id_rol,
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'email_cifrado' => Crypt::encryptString($email),
                'email_hash' => hash('sha256', strtolower(trim($email))),
                'password_hash' => Hash::driver('argon2id')->make($validated['password']),
                'telefono' => $validated['telefono'],
                'estado' => 'activo',
            ]);

            $codigo = 'DOC' . str_pad($usuario->id_usuario, 5, '0', STR_PAD_LEFT);

            $docente = Docente::create([
                'id_usuario' => $usuario->id_usuario,
                'codigo_docente' => $codigo,
                'especialidad' => $validated['especialidad'],
                'titulo_academico' => $validated['titulo_academico'],
                'fecha_ingreso' => $validated['fecha_ingreso'],
                'activo' => true,
            ]);
        });

        return redirect()->route('admin.docentes.index')->with('success', 'Docente creado correctamente.');
    }

    public function edit($id)
    {
        $docente = Docente::with('usuario')->findOrFail($id);
        return view('admin.docentes.form', compact('docente'));
    }

    public function update(Request $request, $id)
    {
        $docente = Docente::with('usuario')->findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'especialidad' => 'nullable|string|max:200',
            'titulo_academico' => 'nullable|string|max:200',
            'fecha_ingreso' => 'nullable|date',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($docente, $validated, $request) {
            $dataUsuario = [
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'telefono' => $validated['telefono'],
            ];
            if ($request->filled('password')) {
                $dataUsuario['password_hash'] = Hash::driver('argon2id')->make($validated['password']);
            }
            $docente->usuario->update($dataUsuario);

            $docente->update([
                'especialidad' => $validated['especialidad'],
                'titulo_academico' => $validated['titulo_academico'],
                'fecha_ingreso' => $validated['fecha_ingreso'],
            ]);
        });

        return redirect()->route('admin.docentes.index')->with('success', 'Docente actualizado correctamente.');
    }

    public function destroy($id)
    {
        $docente = Docente::findOrFail($id);
        DB::transaction(function () use ($docente) {
            $docente->update(['activo' => false]);
            if ($docente->usuario) {
                $docente->usuario->update(['estado' => 'inactivo']);
            }
        });
        return redirect()->route('admin.docentes.index')->with('success', 'Docente desactivado correctamente.');
    }
}
