# EduPortal API

API REST en Laravel para una plataforma educativa minima (estilo Moodle). Expone JSON para el frontend Ionic en `../../eduportal`.

- **Stack:** Laravel 13, PHP 8.3+, SQLite, Sanctum (tokens Bearer)
- **Roles:** multi-tabla — un `User` tiene un perfil `Alumno` (carnet) **o** `Maestro` (departamento), nunca ambos (validado en modelo y controlador)
- **Recursos:** users, grupos, inscripciones, tareas, entregas, materiales, foros, respuestas
- **Locale:** `es` — los errores de validacion (422) llegan en espanol

## Requisitos

- PHP 8.3+ y Composer
- Node.js 20+ (para las herramientas de frontend del skeleton)
- SQLite (viene con PHP; no requiere servidor aparte)

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan optimize
```

## Ejecutar

### Local (mismo PC que el frontend)

```bash
php artisan serve
```

La API queda en `http://127.0.0.1:8000/api`.

### Red local / dispositivo fisico

```bash
php artisan serve --host=0.0.0.0
```

Accesible desde otros dispositivos en `http://<ip-local>:8000/api` (ej. `http://192.168.1.2:8000/api`).

### Android con ngrok (HTTPS temporal)

Android exige HTTPS en produccion. Para exponer la API local con un subdominio temporal:

```bash
# Terminal 1: la API escuchando en todas las interfaces
php artisan serve --host=0.0.0.0

# Terminal 2: tunel HTTPS hacia el puerto 8000
ngrok http 8000
```

ngrok muestra una URL tipo `https://xxxxx.ngrok.app`. Colocala en el frontend:

```ts
// eduportal/src/environments/environment.prod.ts
apiUrl: 'https://xxxxx.ngrok.app/api',
```

y reconstruye la app (`npm run build` + `npx cap sync android`).

## Usuarios de prueba (seed)

| Rol     | Email                | Password  | Perfil                    |
| ------- | -------------------- | --------- | ------------------------- |
| Maestro | maestro@edutech.com  | 12345678  | Departamento: Informatica |
| Maestro | maestro2@edutech.com | 12345678  | Departamento: Matematica  |
| Alumno  | alumno@edutech.com   | 12345678  | Carnet: 20260001          |
| Alumno  | alumno2@edutech.com  | 12345678  | Carnet: 20260002          |
| Alumno  | alumno3@edutech.com  | 12345678  | Carnet: 20260003          |

## Endpoints

Publicos:

- `POST /api/register` — body: `name`, `email`, `password`, `password_confirmation`, y **solo uno** de `carnet` (alumno) o `departamento` (maestro)
- `POST /api/login` — devuelve `{ token, user }`

Con `Authorization: Bearer <token>`:

Los grupos y su contenido solo son visibles para usuarios inscritos y para el
maestro asignado al grupo. Las inscripciones solo pueden ser administradas por
maestros que pertenecen al grupo; al quitar una inscripcion se pierde el acceso.

- `POST /api/logout`
- `apiResource users` (sin `store`; el registro es publico)
- `apiResource grupos` — crear/editar/eliminar solo maestros
- `apiResource inscripciones` — filtros `?grupo_id=` `?user_id=`
- `apiResource tareas` — crear/editar/eliminar solo maestros; filtro `?grupo_id=`
- `apiResource entregas` — crear solo alumnos (una por tarea); calificar solo maestros; filtros `?tarea_id=` `?alumno_id=`
- `apiResource materiales` — crear/editar/eliminar solo maestros; filtro `?grupo_id=`
- `apiResource foros` — editar/eliminar solo el autor; filtro `?grupo_id=`
- `apiResource respuestas` — editar/eliminar solo el autor; filtro `?foro_id=`

## Pruebas

```bash
php artisan test
```
