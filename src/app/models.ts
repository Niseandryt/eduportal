export interface Alumno {
  id: number;
  user_id: number;
  carnet: string;
}

export interface Maestro {
  id: number;
  user_id: number;
  departamento: string;
}

export interface User {
  id: number;
  name: string;
  email: string;
  alumno: Alumno | null;
  maestro: Maestro | null;
  grupos?: Grupo[];
}

export interface Grupo {
  id: number;
  nombre: string;
  descripcion: string | null;
  maestro_id: number;
  maestro?: Maestro & { user?: User };
  users?: User[];
  tareas?: Tarea[];
  materiales?: Material[];
  foros?: Foro[];
}

export interface Tarea {
  id: number;
  grupo_id: number;
  maestro_id: number;
  titulo: string;
  descripcion: string | null;
  fecha_entrega: string | null;
  grupo?: Grupo;
}

export interface Material {
  id: number;
  grupo_id: number;
  maestro_id: number;
  titulo: string;
  descripcion: string | null;
  enlace: string | null;
  grupo?: Grupo;
}

export interface Foro {
  id: number;
  grupo_id: number;
  user_id: number;
  titulo: string;
  contenido: string;
  grupo?: Grupo;
  user?: User;
  respuestas?: Respuesta[];
}

export interface Respuesta {
  id: number;
  foro_id: number;
  user_id: number;
  contenido: string;
  created_at?: string;
  user?: User;
}

export interface Entrega {
  id: number;
  tarea_id: number;
  alumno_id: number;
  contenido: string | null;
  calificacion: string | null;
  tarea?: Tarea;
  alumno?: Alumno & { user?: User };
}

export interface Inscripcion {
  id: number;
  grupo_id: number;
  user_id: number;
  grupo?: Grupo;
  user?: User;
}

export interface LoginResponse {
  token: string;
  user: User;
}
