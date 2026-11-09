import { Injectable } from '@angular/core';
import { api } from './api.client';
import { Entrega, Foro, Grupo, Inscripcion, Material, Respuesta, Tarea, User } from '../models';

@Injectable({ providedIn: 'root' })
export class ApiService {
  // Usuarios
  async getUsers(): Promise<User[]> {
    const { data } = await api.get<User[]>('/users');
    return data;
  }

  async getUsuario(id: number): Promise<User> {
    const { data } = await api.get<User>(`/users/${id}`);
    return data;
  }

  // Grupos
  async getGrupos(): Promise<Grupo[]> {
    const { data } = await api.get<Grupo[]>('/grupos');
    return data;
  }

  async createGrupo(datos: { nombre: string; descripcion?: string }): Promise<Grupo> {
    const { data } = await api.post<{ grupo: Grupo }>('/grupos', datos);
    return data.grupo;
  }

  async updateGrupo(id: number, datos: { nombre?: string; descripcion?: string }): Promise<Grupo> {
    const { data } = await api.put<{ grupo: Grupo }>(`/grupos/${id}`, datos);
    return data.grupo;
  }

  // Inscripciones
  async getInscripciones(grupoId?: number): Promise<Inscripcion[]> {
    const { data } = await api.get<Inscripcion[]>('/inscripciones', {
      params: grupoId ? { grupo_id: grupoId } : {},
    });
    return data;
  }

  async createInscripcion(grupoId: number, userId: number): Promise<Inscripcion> {
    const { data } = await api.post<{ inscripcion: Inscripcion }>('/inscripciones', {
      grupo_id: grupoId,
      user_id: userId,
    });
    return data.inscripcion;
  }

  async deleteInscripcion(id: number): Promise<void> {
    await api.delete(`/inscripciones/${id}`);
  }

  // Tareas
  async getTareas(grupoId?: number): Promise<Tarea[]> {
    const { data } = await api.get<Tarea[]>('/tareas', {
      params: grupoId ? { grupo_id: grupoId } : {},
    });
    return data;
  }

  async createTarea(datos: {
    grupo_id: number;
    titulo: string;
    descripcion?: string;
    fecha_entrega?: string;
  }): Promise<Tarea> {
    const { data } = await api.post<{ tarea: Tarea }>('/tareas', datos);
    return data.tarea;
  }

  async updateTarea(
    id: number,
    datos: { grupo_id?: number; titulo?: string; descripcion?: string; fecha_entrega?: string },
  ): Promise<Tarea> {
    const { data } = await api.put<{ tarea: Tarea }>(`/tareas/${id}`, datos);
    return data.tarea;
  }

  // Entregas
  async getEntregas(filtros: { tarea_id?: number; alumno_id?: number } = {}): Promise<Entrega[]> {
    const { data } = await api.get<Entrega[]>('/entregas', { params: filtros });
    return data;
  }

  async createEntrega(tareaId: number, contenido: string): Promise<Entrega> {
    const { data } = await api.post<{ entrega: Entrega }>('/entregas', {
      tarea_id: tareaId,
      contenido,
    });
    return data.entrega;
  }

  async updateEntrega(id: number, datos: { contenido?: string; calificacion?: number }): Promise<Entrega> {
    const { data } = await api.put<{ entrega: Entrega }>(`/entregas/${id}`, datos);
    return data.entrega;
  }

  // Materiales
  async getMateriales(grupoId?: number): Promise<Material[]> {
    const { data } = await api.get<Material[]>('/materiales', {
      params: grupoId ? { grupo_id: grupoId } : {},
    });
    return data;
  }

  async createMaterial(datos: {
    grupo_id: number;
    titulo: string;
    descripcion?: string;
    enlace?: string;
  }): Promise<Material> {
    const { data } = await api.post<{ material: Material }>('/materiales', datos);
    return data.material;
  }

  async updateMaterial(
    id: number,
    datos: { grupo_id?: number; titulo?: string; descripcion?: string; enlace?: string },
  ): Promise<Material> {
    const { data } = await api.put<{ material: Material }>(`/materiales/${id}`, datos);
    return data.material;
  }

  // Foros y respuestas
  async getForos(grupoId?: number): Promise<Foro[]> {
    const { data } = await api.get<Foro[]>('/foros', {
      params: grupoId ? { grupo_id: grupoId } : {},
    });
    return data;
  }

  async getForo(id: number): Promise<Foro> {
    const { data } = await api.get<Foro>(`/foros/${id}`);
    return data;
  }

  async createForo(datos: { grupo_id: number; titulo: string; contenido: string }): Promise<Foro> {
    const { data } = await api.post<{ foro: Foro }>('/foros', datos);
    return data.foro;
  }

  async updateForo(id: number, datos: { titulo?: string; contenido?: string }): Promise<Foro> {
    const { data } = await api.put<{ foro: Foro }>(`/foros/${id}`, datos);
    return data.foro;
  }

  async createRespuesta(foroId: number, contenido: string): Promise<Respuesta> {
    const { data } = await api.post<{ respuesta: Respuesta }>('/respuestas', {
      foro_id: foroId,
      contenido,
    });
    return data.respuesta;
  }

  async updateRespuesta(id: number, contenido: string): Promise<Respuesta> {
    const { data } = await api.put<{ respuesta: Respuesta }>(`/respuestas/${id}`, { contenido });
    return data.respuesta;
  }

  async deleteRespuesta(id: number): Promise<void> {
    await api.delete(`/respuestas/${id}`);
  }
}
