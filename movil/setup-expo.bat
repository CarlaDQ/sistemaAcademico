@echo off
cd /d "C:\xampp\htdocs\sistemaacademico-laravel\movil"
echo ============================================
=  SGA Movil - Configurar e Iniciar Expo
============================================
echo.
echo PASO 1: Instalando dependencias (npm install)
echo Esto puede tardar 5-10 minutos...
echo.
call npm install --legacy-peer-deps --prefer-offline
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo npm install fallo. Intentando con registro espejo...
    call npm install --legacy-peer-deps --registry https://registry.npmmirror.com
)
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: npm install no pudo completarse.
    echo Prueba: npm install --legacy-peer-deps
    pause
    exit /b 1
)
echo.
echo PASO 2: Iniciando servidor Expo...
echo Escanea el codigo QR con Expo Go en tu celular.
echo.
npx expo start --tunnel
pause