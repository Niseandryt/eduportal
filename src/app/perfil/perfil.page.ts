import { Component, inject } from '@angular/core';
import {
  IonHeader, IonToolbar, IonButtons, IonMenuButton, IonTitle, IonContent,
  IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent,
  IonList, IonItem, IonLabel, IonIcon, IonNote,
} from '@ionic/angular';
import { addIcons } from 'ionicons';
import {
  personCircleOutline, mailOutline, idCardOutline, briefcaseOutline,
} from 'ionicons/icons';
import { AuthService } from '../services/auth.service';

@Component({
  selector: 'app-perfil',
  templateUrl: './perfil.page.html',
  imports: [
    IonHeader, IonToolbar, IonButtons, IonMenuButton, IonTitle, IonContent,
    IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent,
    IonList, IonItem, IonLabel, IonIcon, IonNote,
  ],
})
export class PerfilPage {
  protected readonly auth = inject(AuthService);

  constructor() {
    addIcons({ personCircleOutline, mailOutline, idCardOutline, briefcaseOutline });
  }
}
