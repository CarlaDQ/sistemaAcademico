@echo off
cd /d "%~dp0"
echo ============================================
=   SGA Movil - Iniciar Servidor Expo
============================================
echo.
echo 1. Asegurate de que tu celular y PC esten
echo    en la MISMA red WiFi
echo.
echo 2. Instala Expo Go en tu celular:
echo    - Android: Google Play Store "Expo Go"
echo    - iOS: App Store "Expo Go"
echo.
echo 3. Escanea el codigo QR con Expo Go
echo.
echo ============================================
echo.
npx expo start --host tunnel
pause