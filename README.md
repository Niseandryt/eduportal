# EduPortal (Frontend)

App movil/web en Ionic + Angular (standalone) + Capacitor. Consume la API Laravel en `../api/eduportal`.

- **Stack:** Ionic 9, Angular 22 (standalone), axios, Capacitor 8 (Android)
- **Auth:** token Bearer en `sessionStorage` (ver `src/app/services/auth.service.ts`); interceptor axios lo adjunta y redirige a `/login` en 401
- **Roles:** el menu y los botones se adaptan segun `esMaestro()` / `esAlumno()`; las rutas estan protegidas con guards (`src/app/guards/auth.guard.ts`)

## Requisitos

- Node.js 20+ y npm
- La API corriendo (ver `../api/eduportal/README.md`)
- Para Android: Android Studio (SDK + Gradle) y, opcionalmente, ngrok

## Setup

```bash
npm install
```

## URL de la API

Se define en un solo lugar:

- **Desarrollo (web):** `src/environments/environment.ts` → `apiUrl`
  - `http://127.0.0.1:8000/api` si la API corre en el mismo PC
  - `http://<ip-local>:8000/api` (ej. `http://192.168.1.2:8000/api`) para probar desde otro dispositivo en la red
- **Produccion / APK:** `src/environments/environment.prod.ts` → `apiUrl`
  - Android exige HTTPS: usa el subdominio temporal de ngrok, ej. `https://xxxxx.ngrok.app/api`

## Ejecutar en el navegador

```bash
npm start          # ng serve en http://localhost:4200
# o
npx ionic serve    # http://localhost:8100
```

## Android (Capacitor)

La plataforma `android/` ya esta agregada y la app ya compila. Solo si necesitas regenerarla o actualizarla:

```bash
# 1. Asegura la apiUrl correcta en environment.prod.ts (ngrok HTTPS)
# 2. Compila el bundle web a www/
npm run build

# 3. Copia el build al proyecto nativo
npx cap sync android

# 4. Abre el proyecto en Android Studio
npx cap open android
```

Dentro de Android Studio:

- **Sync** el proyecto Gradle cuando lo pida (barra superior)
- Deja que descargue el **SDK/Build Tools** si falta algo
- **Build > Build Bundle(s) / APK(s) > Build APK(s)** para generar el APK, o **Run** con un emulador/dispositivo

> Solo si `android/` no existiera o se borrara: `npx cap add android` y luego los pasos de arriba.

## Estructura

```
src/app/
  guards/auth.guard.ts        authGuard, guestGuard, roleGuard
  services/api.client.ts      axios instance + interceptor Bearer
  services/auth.service.ts    login/logout, user signal, esMaestro/esAlumno
  services/api.service.ts     llamadas a la API (grupos, tareas, entregas, ...)
  login/                      pagina de login
  perfil/                     perfil del usuario (carnet o departamento)
  grupos/                     lista + crear/editar/inscribir (maestro)
  tareas/                     lista + crear/editar (maestro), entregar (alumno), calificar (maestro)
  materiales/                 lista + crear/editar (maestro)
  foros/  foro-detalle/       foros y respuestas (editar/eliminar solo el autor)
```

## Pruebas y calidad

```bash
npm test           # unit tests (vitest)
npm run lint       # eslint
npm run build      # build de produccion a www/
```
