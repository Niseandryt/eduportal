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
  addOutline, peopleOutline, createOutline, personAddOutline, closeOutline,
} from 'ionicons/icons';
import { AuthService } from '../services/auth.service';
import { ApiService } from '../services/api.service';
import { Grupo, Inscripcion, User } from '../models';

@Component({
  selector: 'app-grupos',
  templateUrl: './grupos.page.html',
  imports: [
    FormsModule, IonHeader, IonToolbar, IonButtons, IonMenuButton, IonTitle,
    IonContent, IonList, IonItem, IonLabel, IonNote, IonSpinner, IonButton,
    IonIcon, IonCard, IonCardHeader, IonCardTitle, IonCardContent, IonInput,
    IonTextarea, IonSelect, IonSelectOption,
  ],
})
export class GruposPage implements OnInit {
  protected readonly auth = inject(AuthService);
  private readonly api = inject(ApiService);

  protected readonly grupos = signal<Grupo[]>([]);
  protected readonly cargando = signal(true);
  protected readonly error = signal('');
  protected readonly mostrarFormulario = signal(false);

  // Edicion (solo maestro)
  protected readonly editandoId = signal<number | null>(null);
  protected editNombre = '';
  protected editDescripcion = '';

  // Inscripciones (solo maestro)
  protected readonly inscribiendoId = signal<number | null>(null);
  protected readonly inscripciones = signal<Inscripcion[]>([]);
  protected readonly alumnos = signal<User[]>([]);
  protected alumnoSeleccionado: number | null = null;

  protected nombre = '';
  protected descripcion = '';

  constructor() {
    addIcons({ addOutline, peopleOutline, createOutline, personAddOutline, closeOutline });
  }

  async ngOnInit(): Promise<void> {
    await this.cargar();
  }

  async cargar(): Promise<void> {
    this.cargando.set(true);
    this.error.set('');
    try {
      const usuario = this.auth.user();
      if (this.auth.esAlumno() && usuario) {
        // El alumno solo ve los grupos en los que esta inscrito
        const completo = await this.api.getUsuario(usuario.id);
        this.grupos.set(completo.grupos ?? []);
      } else {
        this.grupos.set(await this.api.getGrupos());
      }
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    } finally {
      this.cargando.set(false);
    }
  }

  async crear(): Promise<void> {
    this.error.set('');
    try {
      await this.api.createGrupo({
        nombre: this.nombre,
        descripcion: this.descripcion || undefined,
      });
      this.nombre = '';
      this.descripcion = '';
      this.mostrarFormulario.set(false);
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  empezarEdicion(grupo: Grupo): void {
    this.editandoId.set(grupo.id);
    this.editNombre = grupo.nombre;
    this.editDescripcion = grupo.descripcion ?? '';
    this.inscribiendoId.set(null);
  }

  async guardarEdicion(grupo: Grupo): Promise<void> {
    this.error.set('');
    try {
      await this.api.updateGrupo(grupo.id, {
        nombre: this.editNombre,
        descripcion: this.editDescripcion || undefined,
      });
      this.editandoId.set(null);
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  async alternarInscripcion(grupo: Grupo): Promise<void> {
    if (this.inscribiendoId() === grupo.id) {
      this.inscribiendoId.set(null);
      return;
    }
    this.editandoId.set(null);
    this.inscribiendoId.set(grupo.id);
    this.alumnoSeleccionado = null;
    this.error.set('');
    try {
      const [inscripciones, usuarios] = await Promise.all([
        this.api.getInscripciones(grupo.id),
        this.api.getUsers(),
      ]);
      this.inscripciones.set(inscripciones);
      this.alumnos.set(usuarios.filter((u) => u.alumno));
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  async inscribir(grupo: Grupo): Promise<void> {
    if (!this.alumnoSeleccionado) return;
    this.error.set('');
    try {
      await this.api.createInscripcion(grupo.id, this.alumnoSeleccionado);
      this.alumnoSeleccionado = null;
      this.inscripciones.set(await this.api.getInscripciones(grupo.id));
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  async quitar(inscripcion: Inscripcion): Promise<void> {
    this.error.set('');
    try {
      await this.api.deleteInscripcion(inscripcion.id);
      this.inscripciones.set(await this.api.getInscripciones(inscripcion.grupo_id));
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }
}