import { Injectable, computed, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { AxiosError } from 'axios';
import { api, TOKEN_KEY, USER_KEY } from './api.client';
import { LoginResponse, User } from '../models';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly router = inject(Router);

  readonly user = signal<User | null>(this.cargarUsuario());
  readonly isAuthenticated = computed(() => this.user() !== null);
  readonly esMaestro = computed(() => !!this.user()?.maestro);
  readonly esAlumno = computed(() => !!this.user()?.alumno);

  constructor() {
    // Si el token expira o es invalido, limpiar sesion y volver al login
    api.interceptors.response.use(
      (response) => response,
      (error: AxiosError) => {
        if (error.response?.status === 401 && !error.config?.url?.includes('/login')) {
          this.cerrarSesionLocal();
          this.router.navigate(['/login']);
        }
        return Promise.reject(error);
      },
    );
  }

  async login(email: string, password: string): Promise<void> {
    const { data } = await api.post<LoginResponse>('/login', { email, password });
    sessionStorage.setItem(TOKEN_KEY, data.token);
    sessionStorage.setItem(USER_KEY, JSON.stringify(data.user));
    this.user.set(data.user);
  }

  async logout(): Promise<void> {
    try {
      await api.post('/logout');
    } finally {
      this.cerrarSesionLocal();
      this.router.navigate(['/login']);
    }
  }

  mensajeError(error: unknown): string {
    if (error instanceof AxiosError) {
      if (error.response?.status === 422) {
        const errores = error.response.data?.errors as Record<string, string[]> | undefined;
        const primero = errores ? Object.values(errores)[0]?.[0] : undefined;
        if (primero) return primero;
      }
      const mensaje = (error.response?.data as { message?: string } | undefined)?.message;
      if (mensaje) return mensaje;
    }
    return 'No se pudo conectar con el servidor.';
  }

  private cerrarSesionLocal(): void {
    sessionStorage.removeItem(TOKEN_KEY);
    sessionStorage.removeItem(USER_KEY);
    this.user.set(null);
  }

  private cargarUsuario(): User | null {
    const guardado = sessionStorage.getItem(USER_KEY);
    if (!guardado || !sessionStorage.getItem(TOKEN_KEY)) return null;
    try {
      return JSON.parse(guardado) as User;
    } catch {
      return null;
    }
  }
}
