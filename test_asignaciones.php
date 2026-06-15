<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;

// Find a docente user and create a token
$user = \App\Models\Usuario::find(7);
echo "User: {$user->nombre} (ID: {$user->id_usuario})\n";

$docente = \App\Models\Docente::where('id_usuario', $user->id_usuario)->first();
echo "Docente ID: " . ($docente ? $docente->id_docente : 'NOT FOUND') . "\n";

if ($docente) {
    $asignaciones = \App\Models\Asignacion::where('id_docente', $docente->id_docente)
        ->with('materia', 'curso')
        ->get();
    echo "Asignaciones: " . $asignaciones->count() . "\n";
    foreach ($asignaciones as $a) {
        echo "  - {$a->id_asignacion}: {$a->materia->nombre} / {$a->curso->nombre}\n";
    }
    
    echo "\nJSON test:\n";
    echo json_encode($asignaciones->first()) . "\n";
}
