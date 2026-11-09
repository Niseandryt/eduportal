import { Component, OnInit, inject, input, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import {
  IonHeader, IonToolbar, IonButtons, IonBackButton, IonTitle, IonContent,
  IonList, IonItem, IonLabel, IonNote, IonSpinner, IonButton, IonIcon,
  IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent,
  IonTextarea, IonInput,
} from '@ionic/angular';
import { addIcons } from 'ionicons';
import {
  sendOutline, chatbubbleOutline, createOutline, trashOutline,
} from 'ionicons/icons';
import { AuthService } from '../services/auth.service';
import { ApiService } from '../services/api.service';
import { Foro, Respuesta } from '../models';

@Component({
  selector: 'app-foro-detalle',
  templateUrl: './foro-detalle.page.html',
  imports: [
    FormsModule, IonHeader, IonToolbar, IonButtons, IonBackButton, IonTitle,
    IonContent, IonList, IonItem, IonLabel, IonNote, IonSpinner, IonButton,
    IonIcon, IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent,
    IonTextarea, IonInput,
  ],
})
export class ForoDetallePage implements OnInit {
  protected readonly auth = inject(AuthService);
  private readonly api = inject(ApiService);

  // Viene del parametro de ruta /foros/:id (withComponentInputBinding)
  readonly id = input.required<string>();

  protected readonly foro = signal<Foro | null>(null);
  protected readonly cargando = signal(true);
  protected readonly error = signal('');
  protected contenido = '';

  // Edicion del foro (solo el autor)
  protected readonly editandoForo = signal(false);
  protected editTitulo = '';
  protected editContenido = '';

  // Edicion de respuestas (solo el autor)
  protected readonly editandoRespuestaId = signal<number | null>(null);
  protected editRespuestaContenido = '';

  constructor() {
    addIcons({ sendOutline, chatbubbleOutline, createOutline, trashOutline });
  }

  async ngOnInit(): Promise<void> {
    await this.cargar();
  }

  async cargar(): Promise<void> {
    this.cargando.set(true);
    this.error.set('');
    try {
      this.foro.set(await this.api.getForo(Number(this.id())));
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    } finally {
      this.cargando.set(false);
    }
  }

  esAutor(autorId: number): boolean {
    return autorId === this.auth.user()?.id;
  }

  async responder(): Promise<void> {
    if (!this.contenido.trim()) return;
    this.error.set('');
    try {
      await this.api.createRespuesta(Number(this.id()), this.contenido);
      this.contenido = '';
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  empezarEdicionForo(f: Foro): void {
    this.editandoForo.set(true);
    this.editTitulo = f.titulo;
    this.editContenido = f.contenido;
  }

  async guardarEdicionForo(f: Foro): Promise<void> {
    this.error.set('');
    try {
      await this.api.updateForo(f.id, {
        titulo: this.editTitulo,
        contenido: this.editContenido,
      });
      this.editandoForo.set(false);
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  empezarEdicionRespuesta(respuesta: Respuesta): void {
    this.editandoRespuestaId.set(respuesta.id);
    this.editRespuestaContenido = respuesta.contenido;
  }

  async guardarEdicionRespuesta(respuesta: Respuesta): Promise<void> {
    this.error.set('');
    try {
      await this.api.updateRespuesta(respuesta.id, this.editRespuestaContenido);
      this.editandoRespuestaId.set(null);
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  async eliminarRespuesta(respuesta: Respuesta): Promise<void> {
    this.error.set('');
    try {
      await this.api.deleteRespuesta(respuesta.id);
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }
}