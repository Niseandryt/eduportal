import { Component, OnInit, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import {
  IonHeader, IonToolbar, IonButtons, IonMenuButton, IonTitle, IonContent,
  IonList, IonItem, IonLabel, IonNote, IonSpinner, IonButton, IonIcon,
  IonCard, IonCardHeader, IonCardTitle, IonCardContent, IonInput, IonTextarea,
  IonSelect, IonSelectOption,
} from '@ionic/angular';
import { addIcons } from 'ionicons';
import {
  addOutline, clipboardOutline, createOutline, sendOutline, listOutline,
} from 'ionicons/icons';
import { AuthService } from '../services/auth.service';
import { ApiService } from '../services/api.service';
import { Entrega, Grupo, Tarea } from '../models';

@Component({
  selector: 'app-tareas',
  templateUrl: './tareas.page.html',
  imports: [
    FormsModule, IonHeader, IonToolbar, IonButtons, IonMenuButton, IonTitle,
    IonContent, IonList, IonItem, IonLabel, IonNote, IonSpinner, IonButton,
    IonIcon, IonCard, IonCardHeader, IonCardTitle, IonCardContent, IonInput,
    IonTextarea, IonSelect, IonSelectOption,
  ],
})
export class TareasPage implements OnInit {
  protected readonly auth = inject(AuthService);
  private readonly api = inject(ApiService);

  protected readonly tareas = signal<Tarea[]>([]);
  protected readonly grupos = signal<Grupo[]>([]);
  protected readonly cargando = signal(true);
  protected readonly error = signal('');
  protected readonly mostrarFormulario = signal(false);

  // Edicion (solo maestro)
  protected readonly editandoId = signal<number | null>(null);
  protected editTitulo = '';
  protected editDescripcion = '';
  protected editFecha = '';

  // Entregas
  protected readonly entregandoId = signal<number | null>(null);
  protected readonly entregasId = signal<number | null>(null);
  protected readonly entregas = signal<Entrega[]>([]);
  protected readonly misEntregas = signal<Entrega[]>([]);
  protected entregaContenido = '';
  protected readonly calificaciones: Record<number, string> = {};

  protected grupoId: number | null = null;
  protected titulo = '';
  protected descripcion = '';
  protected fechaEntrega = '';

  constructor() {
    addIcons({ addOutline, clipboardOutline, createOutline, sendOutline, listOutline });
  }

  async ngOnInit(): Promise<void> {
    await this.cargar();
  }

  async cargar(): Promise<void> {
    this.cargando.set(true);
    this.error.set('');
    try {
      const usuario = this.auth.user();
      const [tareas, grupos] = await Promise.all([this.api.getTareas(), this.api.getGrupos()]);
      this.tareas.set(tareas);
      this.grupos.set(grupos);
      if (this.auth.esAlumno() && usuario?.alumno) {
        this.misEntregas.set(await this.api.getEntregas({ alumno_id: usuario.alumno.id }));
      }
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    } finally {
      this.cargando.set(false);
    }
  }

  miEntrega(tareaId: number): Entrega | undefined {
    return this.misEntregas().find((e) => e.tarea_id === tareaId);
  }

  async crear(): Promise<void> {
    if (!this.grupoId) return;
    this.error.set('');
    try {
      await this.api.createTarea({
        grupo_id: this.grupoId,
        titulo: this.titulo,
        descripcion: this.descripcion || undefined,
        fecha_entrega: this.fechaEntrega || undefined,
      });
      this.titulo = '';
      this.descripcion = '';
      this.fechaEntrega = '';
      this.grupoId = null;
      this.mostrarFormulario.set(false);
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  empezarEdicion(tarea: Tarea): void {
    this.editandoId.set(tarea.id);
    this.editTitulo = tarea.titulo;
    this.editDescripcion = tarea.descripcion ?? '';
    this.editFecha = tarea.fecha_entrega ?? '';
    this.entregandoId.set(null);
    this.entregasId.set(null);
  }

  async guardarEdicion(tarea: Tarea): Promise<void> {
    this.error.set('');
    try {
      await this.api.updateTarea(tarea.id, {
        titulo: this.editTitulo,
        descripcion: this.editDescripcion || undefined,
        fecha_entrega: this.editFecha || undefined,
      });
      this.editandoId.set(null);
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  async entregar(tarea: Tarea): Promise<void> {
    if (!this.entregaContenido.trim()) return;
    this.error.set('');
    try {
      await this.api.createEntrega(tarea.id, this.entregaContenido);
      this.entregaContenido = '';
      this.entregandoId.set(null);
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  async alternarEntregas(tarea: Tarea): Promise<void> {
    if (this.entregasId() === tarea.id) {
      this.entregasId.set(null);
      return;
    }
    this.editandoId.set(null);
    this.entregasId.set(tarea.id);
    this.error.set('');
    try {
      this.entregas.set(await this.api.getEntregas({ tarea_id: tarea.id }));
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  async calificar(entrega: Entrega): Promise<void> {
    const valor = Number(this.calificaciones[entrega.id]);
    if (isNaN(valor)) return;
    this.error.set('');
    try {
      await this.api.updateEntrega(entrega.id, { calificacion: valor });
      delete this.calificaciones[entrega.id];
      this.entregas.set(await this.api.getEntregas({ tarea_id: entrega.tarea_id }));
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }
}