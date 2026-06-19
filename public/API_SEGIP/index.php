<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/RudeController.php';
require_once __DIR__ . '/controllers/SegipController.php';

initDB();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');
$method = $_SERVER['REQUEST_METHOD'];

// Normalize base path (remove /APISIMULADO/index.php/ or /APISIMULADO/)
$base = dirname($_SERVER['SCRIPT_NAME']);
if ($base === '/' || $base === '\\') $base = '';
$route = substr($uri, strlen($base));
$route = '/' . trim($route, '/');

try {
    // POST /api/rude/estudiantes
    if ($route === '/api/rude/estudiantes' && $method === 'POST') {
        RudeController::registrar();
    }
    // GET /api/rude/estudiantes
    elseif ($route === '/api/rude/estudiantes' && $method === 'GET') {
        RudeController::listar();
    }
    // POST /api/rude/sincronizar
    elseif ($route === '/api/rude/sincronizar' && $method === 'POST') {
        RudeController::sincronizar();
    }
    // GET /api/rude/estadisticas
    elseif ($route === '/api/rude/estadisticas' && $method === 'GET') {
        RudeController::estadisticas();
    }
    // GET/PUT/DELETE /api/rude/estudiantes/{codigo}
    elseif (preg_match('#^/api/rude/estudiantes/([A-Za-z0-9\-]+)$#', $route, $m)) {
        $codigo = $m[1];
        switch ($method) {
            case 'GET': RudeController::consultar($codigo); break;
            case 'PUT': RudeController::actualizar($codigo); break;
            case 'DELETE': RudeController::eliminar($codigo); break;
            default: jsonError('Método no permitido', 405);
        }
    }
    // === SEGIP ===
    // POST /api/segip/poblar
    elseif ($route === '/api/segip/poblar' && $method === 'POST') {
        SegipController::poblar();
    }
    // GET /api/segip/personas
    elseif ($route === '/api/segip/personas' && $method === 'GET') {
        SegipController::listar();
    }
    // GET /api/segip/consultar (con parametros de busqueda)
    elseif ($route === '/api/segip/consultar' && $method === 'GET') {
        SegipController::consultarPorParametros();
    }
    // GET /api/segip/consultar/{ci}
    elseif (preg_match('#^/api/segip/consultar/([A-Za-z0-9]+)$#', $route, $m)) {
        SegipController::consultar($m[1]);
    }
    // === INTEGRACION RUDE + SEGIP ===
    // POST /api/integracion/registrar-desde-ci
    elseif ($route === '/api/integracion/registrar-desde-ci' && $method === 'POST') {
        RudeController::registrarDesdeCI();
    }
    // GET /api/integracion/log
    elseif ($route === '/api/integracion/log' && $method === 'GET') {
        RudeController::integracionLog();
    }
    // GET / → welcome
    elseif ($route === '/' || $route === '') {
        jsonResponse([
            'nombre' => 'API SIMULADA RUDE - Bolivia',
            'version' => '1.0.0',
            'unidad_educativa' => 'U.E. David Pinilla',
            'modulos' => [
                'RUDE' => [
                    ['POST', '/api/rude/estudiantes', 'Registrar estudiante'],
                    ['GET', '/api/rude/estudiantes', 'Listar estudiantes'],
                    ['GET', '/api/rude/estudiantes/{codigo}', 'Consultar estudiante por código RUDE'],
                    ['PUT', '/api/rude/estudiantes/{codigo}', 'Actualizar estudiante'],
                    ['DELETE', '/api/rude/estudiantes/{codigo}', 'Eliminar estudiante'],
                    ['POST', '/api/rude/sincronizar', 'Sincronización masiva'],
                    ['GET', '/api/rude/estadisticas', 'Estadísticas del módulo educativo'],
                ],
                'SEGIP' => [
                    ['POST', '/api/segip/poblar', 'Poblar base de datos SEGIP con 50 personas ficticias'],
                    ['GET', '/api/segip/personas', 'Listar todas las personas del SEGIP'],
                    ['GET', '/api/segip/consultar', 'Buscar personas por ci, nombre, apellido'],
                    ['GET', '/api/segip/consultar/{ci}', 'Consultar una persona por CI'],
                ],
                'INTEGRACION' => [
                    ['POST', '/api/integracion/registrar-desde-ci', 'Registrar estudiante en RUDE desde CI del SEGIP'],
                    ['GET', '/api/integracion/log', 'Ver historial de integraciones SEGIP→RUDE'],
                ],
            ],
        ]);
    }
    else {
        jsonError('Endpoint no encontrado: ' . $route, 404);
    }
} catch (Exception $e) {
    jsonError('Error interno: ' . $e->getMessage(), 500);
}
