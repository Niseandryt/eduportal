import { Component, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import {
  IonContent, IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent,
  IonItem, IonInput, IonButton, IonNote, IonSpinner,
} from '@ionic/angular';
import { AuthService } from '../services/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
  imports: [
    FormsModule, IonContent, IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle,
    IonCardContent, IonItem, IonInput, IonButton, IonNote, IonSpinner,
  ],
})
export class LoginPage {
  private readonly auth = inject(AuthService);
  private readonly router = inject(Router);

  protected email = '';
  protected password = '';
  protected readonly cargando = signal(false);
  protected readonly error = signal('');

  async entrar(): Promise<void> {
    if (this.cargando()) return;
    this.error.set('');
    this.cargando.set(true);
    try {
      await this.auth.login(this.email, this.password);
      this.router.navigate(['/perfil']);
    } catch (e) {
      this.error.set(this.auth.mensajeError(e));
    } finally {
      this.cargando.set(false);
    }
  }
}
