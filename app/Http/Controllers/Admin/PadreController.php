<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Padre;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class PadreController extends Controller
{
    public function index(Request $request)
    {
        $query = Padre::with(['usuario', 'tutores']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('usuario', function ($q) use ($s) {
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('apellido', 'like', "%{$s}%");
            });
        }

        $padres = $query->orderBy('created_at', 'desc')->paginate(15);
        $padres->each(function ($p) {
            $p->hijos_count = DB::table('estudiante_tutor')
                ->where('id_padre', $p->id_padre)
                ->count();
        });

        return view('admin.padres.index', compact('padres'));
    }

    public function create()
    {
        $cursos = Curso::where('activo', true)->with('estudiantes')->get();
        return view('admin.padres.form', compact('cursos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'parentesco' => 'required|in:padre,madre,tutor_legal,abuelo,otro',
            'ocupacion' => 'nullable|string|max:100',
            'estudiantes' => 'nullable|array',
            'estudiantes.*' => 'exists:estudiantes,id_estudiante',
        ]);

        $rolPadre = Rol::where('nombre', 'padre_familia')->firstOrFail();

        DB::transaction(function () use ($validated, $rolPadre, &$padre) {
            $lastId = Usuario::max('id_usuario') + 1;
            $email = strtolower(substr($validated['nombre'], 0, 3) . $validated['apellido'] . substr($lastId, 0, 3)) . '@davidpinilla.com';

            $existe = Usuario::where('email_hash', hash('sha256', strtolower(trim($email))))->first();
            if ($existe) {
                $email = strtolower(substr($validated['nombre'], 0, 3) . $validated['apellido'] . $lastId) . '@davidpinilla.com';
            }

            $usuario = Usuario::create([
                'id_rol' => $rolPadre->id_rol,
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'email_cifrado' => Crypt::encryptString($email),
                'email_hash' => hash('sha256', strtolower(trim($email))),
                'password_hash' => Hash::driver('argon2id')->make('12345678'),
                'telefono' => $validated['telefono'],
                'estado' => 'activo',
            ]);

            $padre = Padre::create([
                'id_usuario' => $usuario->id_usuario,
                'parentesco' => $validated['parentesco'],
                'ocupacion' => $validated['ocupacion'],
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
        });

        return redirect()->route('admin.padres.index')->with('success', 'Padre de familia creado correctamente.');
    }

    public function edit($id)
    {
        $padre = Padre::with('usuario')->findOrFail($id);
        $cursos = Curso::where('activo', true)->with('estudiantes')->get();
        $vinculados = DB::table('estudiante_tutor')
            ->where('id_padre', $id)
            ->pluck('id_estudiante')
            ->toArray();
        return view('admin.padres.form', compact('padre', 'cursos', 'vinculados'));
    }

    public function update(Request $request, $id)
    {
        $padre = Padre::with('usuario')->findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'parentesco' => 'required|in:padre,madre,tutor_legal,abuelo,otro',
            'ocupacion' => 'nullable|string|max:100',
            'estudiantes' => 'nullable|array',
            'estudiantes.*' => 'exists:estudiantes,id_estudiante',
        ]);

        DB::transaction(function () use ($padre, $validated) {
            $padre->usuario->update([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'telefono' => $validated['telefono'],
            ]);

            $padre->update([
                'parentesco' => $validated['parentesco'],
                'ocupacion' => $validated['ocupacion'],
            ]);

            DB::table('estudiante_tutor')->where('id_padre', $padre->id_padre)->delete();

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
        });

        return redirect()->route('admin.padres.index')->with('success', 'Padre de familia actualizado correctamente.');
    }

    public function destroy($id)
    {
        $padre = Padre::findOrFail($id);
        DB::transaction(function () use ($padre) {
            DB::table('estudiante_tutor')->where('id_padre', $padre->id_padre)->delete();
            if ($padre->usuario) {
                $padre->usuario->update(['estado' => 'inactivo']);
            }
        });
        return redirect()->route('admin.padres.index')->with('success', 'Padre de familia desactivado correctamente.');
    }

    public function buscarEstudiantes(Request $request)
    {
        $q = $request->get('q', '');
        $idCurso = $request->get('id_curso');
        $ids = $request->get('ids', []);

        $query = Estudiante::with('usuario', 'curso')
            ->where('activo', true);

        if (!empty($ids)) {
            $query->whereIn('id_estudiante', $ids);
        } elseif (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('usuario', function ($u) use ($q) {
                    $u->where('nombre', 'like', "%{$q}%")
                      ->orWhere('apellido', 'like', "%{$q}%")
                      ->orWhere('ci', 'like', "%{$q}%");
                })->orWhere('codigo_estudiante', 'like', "%{$q}%");
            });
        }

        if (!empty($idCurso)) {
            $query->where('id_curso', $idCurso);
        }

        $estudiantes = $query->limit(20)->get()->map(function ($e) {
            return [
                'id' => $e->id_estudiante,
                'text' => ($e->usuario->nombre ?? '') . ' ' . ($e->usuario->apellido ?? '')
                    . ' (' . $e->codigo_estudiante . ') - ' . ($e->curso->nombre ?? ''),
                'nombre' => $e->usuario->nombre ?? '',
                'apellido' => $e->usuario->apellido ?? '',
                'codigo' => $e->codigo_estudiante,
                'curso' => $e->curso->nombre ?? '',
            ];
        });

        return response()->json($estudiantes);
    }

    public function buscarPorCi($ci)
    {
        $usuario = Usuario::where('ci', $ci)
            ->whereHas('rol', function ($q) {
                $q->where('nombre', 'padre_familia');
            })
            ->with('padre')
            ->first();

        if (!$usuario || !$usuario->padre) {
            return response()->json(['encontrado' => false]);
        }

        return response()->json([
            'encontrado' => true,
            'id_padre' => $usuario->padre->id_padre,
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'telefono' => $usuario->telefono,
            'parentesco' => $usuario->padre->parentesco,
            'ocupacion' => $usuario->padre->ocupacion,
        ]);
    }
}
