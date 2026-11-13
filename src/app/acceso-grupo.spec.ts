import { signal } from '@angular/core';
import { TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';
import { vi } from 'vitest';

import { ForosPage } from './foros/foros.page';
import { MaterialesPage } from './materiales/materiales.page';
import { TareasPage } from './tareas/tareas.page';
import { ApiService } from './services/api.service';
import { AuthService } from './services/auth.service';

describe('Contenido de los grupos', () => {
  const api = {
    getGrupos: vi.fn(),
    getTareas: vi.fn(),
    getMateriales: vi.fn(),
    getForos: vi.fn(),
    getEntregas: vi.fn(),
  };
  const auth = {
    user: signal({ id: 1, alumno: { id: 1 } }),
    esAlumno: signal(true),
    esMaestro: signal(false),
    mensajeError: () => 'Error al cargar',
  };

  beforeEach(() => {
    api.getGrupos.mockResolvedValue([]);
    api.getTareas.mockResolvedValue([]);
    api.getMateriales.mockResolvedValue([]);
    api.getForos.mockResolvedValue([]);
    api.getEntregas.mockResolvedValue([]);
    TestBed.configureTestingModule({
      imports: [TareasPage, MaterialesPage, ForosPage],
      providers: [
        provideRouter([]),
        { provide: ApiService, useValue: api },
        { provide: AuthService, useValue: auth },
      ],
    });
  });

  afterEach(() => vi.resetAllMocks());

  it('muestra listas vacias sin grupos y recarga al regresar', async () => {
    const tareas = TestBed.createComponent(TareasPage);
    const materiales = TestBed.createComponent(MaterialesPage);
    const foros = TestBed.createComponent(ForosPage);

    await Promise.all([
      tareas.componentInstance.ionViewWillEnter(),
      materiales.componentInstance.ionViewWillEnter(),
      foros.componentInstance.ionViewWillEnter(),
    ]);
    for (const fixture of [tareas, materiales, foros]) {
      fixture.detectChanges();
      await fixture.whenStable();
    }
    expect(tareas.nativeElement.textContent).toContain('No hay tareas creadas.');
    expect(materiales.nativeElement.textContent).toContain('No hay materiales creados.');
    expect(foros.nativeElement.textContent).toContain('No hay foros creados.');

    api.getGrupos.mockResolvedValue([{ id: 1, nombre: 'Grupo' }]);
    api.getTareas.mockResolvedValue([{ id: 1, titulo: 'Tarea visible', grupo_id: 1 }]);
    api.getMateriales.mockResolvedValue([{ id: 1, titulo: 'Material visible', grupo_id: 1 }]);
    api.getForos.mockResolvedValue([{ id: 1, titulo: 'Foro visible', grupo_id: 1 }]);
    await Promise.all([
      tareas.componentInstance.ionViewWillEnter(),
      materiales.componentInstance.ionViewWillEnter(),
      foros.componentInstance.ionViewWillEnter(),
    ]);
    for (const fixture of [tareas, materiales, foros]) {
      fixture.detectChanges();
      await fixture.whenStable();
    }
    expect(tareas.nativeElement.textContent).toContain('Tarea visible');
    expect(materiales.nativeElement.textContent).toContain('Material visible');
    expect(foros.nativeElement.textContent).toContain('Foro visible');
  });
});
