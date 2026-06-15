<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $emailHash = hash('sha256', strtolower(trim($request->email)));

        $user = Usuario::with('rol')->where('email_hash', $emailHash)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.'])->withInput();
        }

        if ($user->estado === 'bloqueado') {
            return back()->withErrors(['email' => 'Cuenta bloqueada. Contacte al administrador.'])->withInput();
        }

        if (!password_verify($request->password, $user->password_hash)) {
            $user->increment('intentos_fallidos');

            if ($user->intentos_fallidos >= 5) {
                $user->update(['estado' => 'bloqueado']);
                return back()->withErrors(['email' => 'Cuenta bloqueada por múltiples intentos fallidos.'])->withInput();
            }

            return back()->withErrors(['email' => 'Credenciales incorrectas.'])->withInput();
        }

        if ($user->estado !== 'activo') {
            return back()->withErrors(['email' => 'Cuenta no activa. Contacte al administrador.'])->withInput();
        }

        $user->update([
            'intentos_fallidos' => 0,
            'ultimo_acceso' => now(),
            'ip_ultimo_acceso' => $request->ip(),
        ]);

        session([
            'user_id' => $user->id_usuario,
            'user_name' => $user->nombre . ' ' . $user->apellido,
            'user_rol' => $user->rol->nombre ?? 'sin_rol',
            'user_email_hash' => $user->email_hash,
        ]);

        $request->session()->regenerate();

        $rolName = $user->rol->nombre ?? '';

        switch ($rolName) {
            case 'administrador':
                return redirect()->intended(route('admin.dashboard'));
            case 'docente':
                return redirect()->intended(route('docente.calificaciones.index'));
            case 'estudiante':
                return redirect()->intended(route('estudiante.notas'));
            case 'padre_familia':
                return redirect()->intended(route('padre.hijos.index'));
            default:
                return redirect()->intended(url('/'));
        }
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()->route('login');
    }
}
