import { Component, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import {
  IonHeader, IonToolbar, IonButtons, IonMenuButton, IonTitle, IonContent,
  IonList, IonItem, IonLabel, IonNote, IonSpinner, IonButton, IonIcon,
  IonCard, IonCardHeader, IonCardTitle, IonCardContent, IonInput, IonTextarea,
  IonSelect, IonSelectOption,
} from '@ionic/angular';
import { addIcons } from 'ionicons';
import { addOutline, chatbubblesOutline, createOutline } from 'ionicons/icons';
import { AuthService } from '../services/auth.service';
import { ApiService } from '../services/api.service';
import { Foro, Grupo } from '../models';

@Component({
  selector: 'app-foros',
  templateUrl: './foros.page.html',
  imports: [
    RouterLink, FormsModule, IonHeader, IonToolbar, IonButtons, IonMenuButton,
    IonTitle, IonContent, IonList, IonItem, IonLabel, IonNote, IonSpinner,
    IonButton, IonIcon, IonCard, IonCardHeader, IonCardTitle, IonCardContent,
    IonInput, IonTextarea, IonSelect, IonSelectOption,
  ],
})
export class ForosPage {
  protected readonly auth = inject(AuthService);
  private readonly api = inject(ApiService);

  protected readonly foros = signal<Foro[]>([]);
  protected readonly grupos = signal<Grupo[]>([]);
  protected readonly cargando = signal(true);
  protected readonly error = signal('');
  protected readonly mostrarFormulario = signal(false);

  // Edicion (solo el autor)
  protected readonly editandoId = signal<number | null>(null);
  protected editTitulo = '';
  protected editContenido = '';

  protected grupoId: number | null = null;
  protected titulo = '';
  protected contenido = '';

  constructor() {
    addIcons({ addOutline, chatbubblesOutline, createOutline });
  }

  async ionViewWillEnter(): Promise<void> {
    await this.cargar();
  }

  async cargar(): Promise<void> {
    this.cargando.set(true);
    this.error.set('');
    try {
      const [foros, grupos] = await Promise.all([
        this.api.getForos(),
        this.api.getGrupos(),
      ]);
      this.foros.set(foros);
      this.grupos.set(grupos);
    } catch (e) {
      this.foros.set([]);
      this.grupos.set([]);
      this.error.set(this.auth.mensajeError(e));
    } finally {
      this.cargando.set(false);
    }
  }

  esAutor(foro: Foro): boolean {
    return foro.user_id === this.auth.user()?.id;
  }

  async crear(): Promise<void> {
    if (!this.grupoId) return;
    this.error.set('');
    try {
      await this.api.createForo({
        grupo_id: this.grupoId,
        titulo: this.titulo,
        contenido: this.contenido,
      });
      this.titulo = '';
      this.contenido = '';
      this.grupoId = null;
      this.mostrarFormulario.set(false);
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  empezarEdicion(foro: Foro): void {
    this.editandoId.set(foro.id);
    this.editTitulo = foro.titulo;
    this.editContenido = foro.contenido;
  }

  async guardarEdicion(foro: Foro): Promise<void> {
    this.error.set('');
    try {
      await this.api.updateForo(foro.id, {
        titulo: this.editTitulo,
        contenido: this.editContenido,
      });
      this.editandoId.set(null);
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }
}