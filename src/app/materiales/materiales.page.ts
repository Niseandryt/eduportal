import { Component, OnInit, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import {
  IonHeader, IonToolbar, IonButtons, IonMenuButton, IonTitle, IonContent,
  IonList, IonItem, IonLabel, IonNote, IonSpinner, IonButton, IonIcon,
  IonCard, IonCardHeader, IonCardTitle, IonCardContent, IonInput, IonTextarea,
  IonSelect, IonSelectOption,
} from '@ionic/angular';
import { addIcons } from 'ionicons';
import { addOutline, libraryOutline, openOutline, createOutline } from 'ionicons/icons';
import { AuthService } from '../services/auth.service';
import { ApiService } from '../services/api.service';
import { Grupo, Material } from '../models';

@Component({
  selector: 'app-materiales',
  templateUrl: './materiales.page.html',
  imports: [
    FormsModule, IonHeader, IonToolbar, IonButtons, IonMenuButton, IonTitle,
    IonContent, IonList, IonItem, IonLabel, IonNote, IonSpinner, IonButton,
    IonIcon, IonCard, IonCardHeader, IonCardTitle, IonCardContent, IonInput,
    IonTextarea, IonSelect, IonSelectOption,
  ],
})
export class MaterialesPage implements OnInit {
  protected readonly auth = inject(AuthService);
  private readonly api = inject(ApiService);

  protected readonly materiales = signal<Material[]>([]);
  protected readonly grupos = signal<Grupo[]>([]);
  protected readonly cargando = signal(true);
  protected readonly error = signal('');
  protected readonly mostrarFormulario = signal(false);

  // Edicion (solo maestro)
  protected readonly editandoId = signal<number | null>(null);
  protected editTitulo = '';
  protected editDescripcion = '';
  protected editEnlace = '';

  protected grupoId: number | null = null;
  protected titulo = '';
  protected descripcion = '';
  protected enlace = '';

  constructor() {
    addIcons({ addOutline, libraryOutline, openOutline, createOutline });
  }

  async ngOnInit(): Promise<void> {
    await this.cargar();
  }

  async cargar(): Promise<void> {
    this.cargando.set(true);
    this.error.set('');
    try {
      const [materiales, grupos] = await Promise.all([
        this.api.getMateriales(),
        this.api.getGrupos(),
      ]);
      this.materiales.set(materiales);
      this.grupos.set(grupos);
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    } finally {
      this.cargando.set(false);
    }
  }

  async crear(): Promise<void> {
    if (!this.grupoId) return;
    this.error.set('');
    try {
      await this.api.createMaterial({
        grupo_id: this.grupoId,
        titulo: this.titulo,
        descripcion: this.descripcion || undefined,
        enlace: this.enlace || undefined,
      });
      this.titulo = '';
      this.descripcion = '';
      this.enlace = '';
      this.grupoId = null;
      this.mostrarFormulario.set(false);
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }

  empezarEdicion(material: Material): void {
    this.editandoId.set(material.id);
    this.editTitulo = material.titulo;
    this.editDescripcion = material.descripcion ?? '';
    this.editEnlace = material.enlace ?? '';
  }

  async guardarEdicion(material: Material): Promise<void> {
    this.error.set('');
    try {
      await this.api.updateMaterial(material.id, {
        titulo: this.editTitulo,
        descripcion: this.editDescripcion || undefined,
        enlace: this.editEnlace || undefined,
      });
      this.editandoId.set(null);
      await this.cargar();
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    }
  }
}