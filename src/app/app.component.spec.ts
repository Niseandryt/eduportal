import { TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';

import { AppComponent } from './app.component';
import { TOKEN_KEY, USER_KEY } from './services/api.client';
import { User } from './models';

const maestro: User = {
  id: 1,
  name: 'Denis Lue',
  email: 'maestro@edutech.com',
  alumno: null,
  maestro: { id: 1, user_id: 1, departamento: 'Informatica' },
};

const alumno: User = {
  id: 3,
  name: 'Daniel Angel',
  email: 'alumno@edutech.com',
  alumno: { id: 1, user_id: 3, carnet: '20260001' },
  maestro: null,
};

function simularSesion(user: User | null): void {
  sessionStorage.clear();
  if (user) {
    sessionStorage.setItem(TOKEN_KEY, 'token-de-prueba');
    sessionStorage.setItem(USER_KEY, JSON.stringify(user));
  }
}

describe('AppComponent', () => {
  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AppComponent],
      providers: [provideRouter([])],
    }).compileComponents();
  });

  afterEach(() => sessionStorage.clear());

  it('should create the app', () => {
    simularSesion(null);
    const fixture = TestBed.createComponent(AppComponent);
    expect(fixture.componentInstance).toBeTruthy();
  });

  it('should not show the menu when there is no session', async () => {
    simularSesion(null);
    const fixture = TestBed.createComponent(AppComponent);
    fixture.detectChanges();
    await fixture.whenStable();
    expect(fixture.nativeElement.querySelector('ion-menu')).toBeNull();
  });

  it('should show all menu items for maestros', async () => {
    simularSesion(maestro);
    const fixture = TestBed.createComponent(AppComponent);
    fixture.detectChanges();
    // ion-label es un componente Stencil cuyo contenido se reubica de forma
    // asincrona; esperar a la hidratacion antes de leer textContent (ROU-10799).
    await fixture.whenStable();
    const textos = Array.from(
      fixture.nativeElement.querySelectorAll('ion-label') as NodeListOf<HTMLElement>,
    ).map((el) => el.innerHTML);
    expect(textos.some((t) => t.includes('Grupos'))).toBe(true);
    expect(textos.some((t) => t.includes('Tareas'))).toBe(true);
    expect(textos.some((t) => t.includes('Materiales'))).toBe(true);
    expect(textos.some((t) => t.includes('Perfil'))).toBe(true);
  });

  it('should show tareas and materiales to alumnos too', async () => {
    simularSesion(alumno);
    const fixture = TestBed.createComponent(AppComponent);
    fixture.detectChanges();
    await fixture.whenStable();
    const textos = Array.from(
      fixture.nativeElement.querySelectorAll('ion-label') as NodeListOf<HTMLElement>,
    ).map((el) => el.innerHTML);
    expect(textos.some((t) => t.includes('Mis Grupos'))).toBe(true);
    expect(textos.some((t) => t.includes('Tareas'))).toBe(true);
    expect(textos.some((t) => t.includes('Materiales'))).toBe(true);
  });
});
