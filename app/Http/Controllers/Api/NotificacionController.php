<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notificaciones = Notificacion::where('id_usuario_destino', $user->id_usuario)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notificaciones);
    }

    public function markRead($id)
    {
        $user = request()->user();
        $notif = Notificacion::where('id_notificacion', $id)
            ->where('id_usuario_destino', $user->id_usuario)
            ->firstOrFail();

        $notif->update([
            'leido' => true,
            'fecha_lectura' => now(),
        ]);

        return response()->json(['message' => 'Notificación marcada como leída.']);
    }

    public function unreadCount(Request $request)
    {
        $count = Notificacion::where('id_usuario_destino', $request->user()->id_usuario)
            ->where('leido', false)
            ->count();

        return response()->json(['unread' => $count]);
    }
}