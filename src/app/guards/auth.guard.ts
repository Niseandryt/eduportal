import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

export const authGuard: CanActivateFn = () => {
  const auth = inject(AuthService);
  const router = inject(Router);
  return auth.isAuthenticated() ? true : router.createUrlTree(['/login']);
};

export const guestGuard: CanActivateFn = () => {
  const auth = inject(AuthService);
  const router = inject(Router);
  return auth.isAuthenticated() ? router.createUrlTree(['/perfil']) : true;
};

export function roleGuard(role: 'alumno' | 'maestro'): CanActivateFn {
  return () => {
    const auth = inject(AuthService);
    const router = inject(Router);
    const permitido = role === 'maestro' ? auth.esMaestro() : auth.esAlumno();
    return permitido ? true : router.createUrlTree(['/perfil']);
  };
}
