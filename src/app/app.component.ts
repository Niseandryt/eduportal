import { Component, computed, inject } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import {
  IonApp, IonSplitPane, IonMenu, IonContent, IonList, IonListHeader, IonNote,
  IonMenuToggle, IonItem, IonIcon, IonLabel, IonRouterOutlet, IonRouterLink,
} from '@ionic/angular';
import { addIcons } from 'ionicons';
import {
  personCircleOutline, personCircleSharp, peopleOutline, peopleSharp,
  clipboardOutline, clipboardSharp, libraryOutline, librarySharp,
  chatbubblesOutline, chatbubblesSharp, logOutOutline, logOutSharp,
} from 'ionicons/icons';
import { AuthService } from './services/auth.service';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
  imports: [
    RouterLink, RouterLinkActive, IonApp, IonSplitPane, IonMenu, IonContent,
    IonList, IonListHeader, IonNote, IonMenuToggle, IonItem, IonIcon, IonLabel,
    IonRouterLink, IonRouterOutlet,
  ],
})
export class AppComponent {
  protected readonly auth = inject(AuthService);

  protected readonly appPages = computed(() => [
    { title: 'Perfil', url: '/perfil', icon: 'person-circle' },
    {
      title: this.auth.esMaestro() ? 'Grupos' : 'Mis Grupos',
      url: '/grupos',
      icon: 'people',
    },
    { title: 'Tareas', url: '/tareas', icon: 'clipboard' },
    { title: 'Materiales', url: '/materiales', icon: 'library' },
    { title: 'Foros', url: '/foros', icon: 'chatbubbles' },
  ]);

  constructor() {
    addIcons({
      personCircleOutline, personCircleSharp, peopleOutline, peopleSharp,
      clipboardOutline, clipboardSharp, libraryOutline, librarySharp,
      chatbubblesOutline, chatbubblesSharp, logOutOutline, logOutSharp,
    });
  }

  protected cerrarSesion(): void {
    this.auth.logout();
  }
}
