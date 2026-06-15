<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $emailHash = hash('sha256', strtolower(trim($request->email)));

        $user = Usuario::with('rol')->where('email_hash', $emailHash)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json(['message' => 'Credenciales incorrectas.'], 401);
        }

        if ($user->estado !== 'activo') {
            return response()->json(['message' => 'Cuenta no activa.'], 403);
        }

        $token = $user->createToken('mobile-app', [$user->rol->nombre ?? ''])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id_usuario,
                'nombre' => $user->nombre,
                'apellido' => $user->apellido,
                'email' => $request->email,
                'rol' => $user->rol->nombre ?? 'sin_rol',
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada.']);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load('rol');
        return response()->json([
            'id' => $user->id_usuario,
            'nombre' => $user->nombre,
            'apellido' => $user->apellido,
            'rol' => $user->rol->nombre ?? 'sin_rol',
        ]);
    }
}