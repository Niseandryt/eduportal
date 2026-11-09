import { Routes } from '@angular/router';
import { authGuard, guestGuard } from './guards/auth.guard';

export const routes: Routes = [
  {
    path: '',
    redirectTo: 'perfil',
    pathMatch: 'full',
  },
  {
    path: 'login',
    loadComponent: () => import('./login/login.page').then((m) => m.LoginPage),
    canActivate: [guestGuard],
  },
  {
    path: 'perfil',
    loadComponent: () => import('./perfil/perfil.page').then((m) => m.PerfilPage),
    canActivate: [authGuard],
  },
  {
    path: 'grupos',
    loadComponent: () => import('./grupos/grupos.page').then((m) => m.GruposPage),
    canActivate: [authGuard],
  },
  {
    path: 'tareas',
    loadComponent: () => import('./tareas/tareas.page').then((m) => m.TareasPage),
    canActivate: [authGuard],
  },
  {
    path: 'materiales',
    loadComponent: () => import('./materiales/materiales.page').then((m) => m.MaterialesPage),
    canActivate: [authGuard],
  },
  {
    path: 'foros',
    loadComponent: () => import('./foros/foros.page').then((m) => m.ForosPage),
    canActivate: [authGuard],
  },
  {
    path: 'foros/:id',
    loadComponent: () => import('./foro-detalle/foro-detalle.page').then((m) => m.ForoDetallePage),
    canActivate: [authGuard],
  },
  {
    path: '**',
    redirectTo: 'perfil',
  },
];
