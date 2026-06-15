# SGA Móvil — Aplicación de Asistencia Georreferenciada

App cross-platform (Android/iOS) para el Sistema de Gestión Académica.

## Requisitos

- Node.js 18+
- Expo CLI (`npm i -g expo-cli`)
- Dispositivo físico o emulador (Android/iOS)

## Instalación

```bash
cd movil
npm install
```

## Configuración

Editar `src/api/client.js` y cambiar `API_URL` según tu entorno:

| Entorno | URL |
|---------|-----|
| Android Emulator | `http://10.0.2.2/sistemaacademico-laravel/public/api` |
| iOS Simulator | `http://localhost/sistemaacademico-laravel/public/api` |
| Dispositivo físico | `http://<TU_IP>:8000/api` (misma red WiFi) |

## Ejecutar

```bash
npx expo start
```

Escanea el QR con Expo Go (Android) o la cámara (iOS).

## Funcionalidades por Rol

| Rol | Funcionalidades |
|-----|----------------|
| **Estudiante** | Registrar asistencia con geocerca, ver calificaciones, historial |
| **Docente** | Registrar asistencia, gestionar geocercas, ver calificaciones/reportes |
| **Padre** | Ver calificaciones y asistencia de sus hijos |

## Estructura del proyecto

```
movil/
├── App.js                         # Punto de entrada
├── src/
│   ├── api/client.js              # Cliente Axios + JWT interceptor
│   ├── auth/AuthContext.js        # Contexto de autenticación
│   ├── navigation/AppNavigator.js # Navegación por stacks
│   └── screens/
│       ├── LoginScreen.js         # Login con email+password
│       ├── DashboardScreen.js     # Menú principal por rol
│       ├── AttendanceCheckin.js   # Check-in con GPS + geocerca
│       ├── AttendanceHistory.js   # Historial de asistencia
│       ├── GradesScreen.js        # Calificaciones agrupadas
│       ├── NotificationsScreen.js # Notificaciones push/in-app
│       └── GeocercaManagement.js  # CRUD de geocercas (docente)
└── package.json
```

## API Endpoints (Laravel)

| Método | Ruta | Auth | Descripción |
|--------|------|------|-------------|
| POST | `/api/auth/login` | No | Login con email+password |
| POST | `/api/auth/logout` | Sí | Cerrar sesión |
| GET | `/api/auth/profile` | Sí | Perfil del usuario |
| POST | `/api/asistencia/register` | Sí | Registrar asistencia con ubicación |
| GET | `/api/asistencia/history` | Sí | Historial de asistencia |
| GET | `/api/calificaciones` | Sí | Calificaciones del usuario |
| GET | `/api/notificaciones` | Sí | Notificaciones del usuario |
| GET/POST/PUT/DELETE | `/api/geocercas` | Sí | CRUD de geocercas |
