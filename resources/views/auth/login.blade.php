<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion - U.E. David Pinilla</title>
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
        .login-wrapper {
            width: 100%;
            max-width: 440px;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #1e3c72, #2a4a7f);
            color: #fff;
            text-align: center;
            padding: 32px 24px 24px;
        }
        .login-header .icon-wrap {
            width: 72px;
            height: 72px;
            background: rgba(255,255,255,0.15);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 2.2rem;
        }
        .login-header h4 { font-weight: 700; margin-bottom: 4px; font-size: 1.2rem; }
        .login-header p { opacity: 0.7; font-size: 0.85rem; margin-bottom: 0; }
        .login-body { padding: 32px 32px 28px; }
        .login-body .form-label { font-size: 0.85rem; font-weight: 600; color: #444; margin-bottom: 6px; }
        .login-body .input-group-text {
            background: #f4f6f9;
            border: 1px solid #e0e4e8;
            color: #888;
            border-right: none;
        }
        .login-body .form-control {
            border: 1px solid #e0e4e8;
            border-left: none;
            padding: 10px 14px;
            font-size: 0.9rem;
        }
        .login-body .form-control:focus {
            border-color: #1e3c72;
            box-shadow: none;
        }
        .login-body .form-control:focus + .input-group-text,
        .login-body .input-group:focus-within .input-group-text {
            border-color: #1e3c72;
        }
        .btn-login {
            background: linear-gradient(135deg, #1e3c72, #2a4a7f);
            border: none;
            width: 100%;
            padding: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #152a4f, #1e3c72);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30,60,114,0.3);
        }
        .login-footer {
            text-align: center;
            padding: 0 32px 28px;
        }
        .login-footer small { color: #aaa; font-size: 0.75rem; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:72px;height:72px;border-radius:18px;object-fit:cover;margin-bottom:16px;">
                <h4>U.E. David Pinilla</h4>
                <p>Unidad Educativa David Pinilla</p>
            </div>
            <div class="login-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert" style="border-radius:12px;border:none;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div>{{ $errors->first() }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electronico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}"
                                   placeholder="correo@ejemplo.com" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Contrasena</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" placeholder="Ingrese su contrasena" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                    </button>
                </form>
            </div>
            <div class="login-footer">
                <small>U.E. David Pinilla &copy; {{ date('Y') }}. Todos los derechos reservados.</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>