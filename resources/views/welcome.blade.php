<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U.E. David Pinilla - Sistema Acad&eacute;mico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #0f1b33 0%, #1e3c72 50%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            padding: 20px;
        }
        .welcome-wrapper { width: 100%; max-width: 440px; }
        .welcome-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .welcome-header {
            background: linear-gradient(135deg, #1e3c72, #2a4a7f);
            color: #fff;
            text-align: center;
            padding: 40px 24px 32px;
        }
        .welcome-header .icon-wrap {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.15);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 2.5rem;
        }
        .welcome-header h2 { font-weight: 700; margin-bottom: 4px; }
        .welcome-header p { opacity: 0.7; font-size: 0.9rem; margin-bottom: 0; }
        .welcome-body { padding: 32px; text-align: center; }
        .welcome-body p { color: #666; margin-bottom: 24px; }
        .btn-sga {
            background: linear-gradient(135deg, #1e3c72, #2a4a7f);
            border: none;
            width: 100%;
            padding: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s;
            color: #fff;
            text-decoration: none;
            display: inline-block;
        }
        .btn-sga:hover {
            background: linear-gradient(135deg, #152a4f, #1e3c72);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30,60,114,0.3);
            color: #fff;
        }
        .welcome-footer { text-align: center; padding: 0 32px 28px; }
        .welcome-footer small { color: #aaa; font-size: 0.75rem; }
    </style>
</head>
<body>
    <div class="welcome-wrapper">
        <div class="welcome-card">
            <div class="welcome-header">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:80px;height:80px;border-radius:20px;object-fit:cover;margin-bottom:16px;">
                <h2>U.E. David Pinilla</h2>
                <p>Unidad Educativa David Pinilla</p>
            </div>
            <div class="welcome-body">
                <p>Bienvenido al sistema acad&eacute;mico de la Unidad Educativa David Pinilla. Inicia sesi&oacute;n para acceder a tus cursos, calificaciones y m&aacute;s.</p>
                <a href="{{ route('login') }}" class="btn-sga">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesi&oacute;n
                </a>
            </div>
            <div class="welcome-footer">
                <small>U.E. David Pinilla &copy; {{ date('Y') }}. Todos los derechos reservados.</small>
            </div>
        </div>
    </div>
</body>
</html>
