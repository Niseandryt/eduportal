<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EntregaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $entregas = Entrega::with(['tarea', 'alumno.user'])
            ->whereHas('tarea.grupo', fn ($query) => $query->visiblePara($request->user()))
            ->when($request->query('tarea_id'), fn ($q, $tareaId) => $q->where('tarea_id', $tareaId))
            ->when($request->query('alumno_id'), fn ($q, $alumnoId) => $q->where('alumno_id', $alumnoId))
            ->latest('created_at')
            ->get();
        return response()->json($entregas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->esAlumno()) {
            return response()->json(['mensaje' => 'Solo los alumnos pueden entregar tareas'], 403);
        }
        $alumnoId = $request->user()->alumno->id;
        $datos = $request->validate([
            'tarea_id' => [
                'required',
                'exists:tareas,id',
                Rule::unique('entregas', 'tarea_id')->where('alumno_id', $alumnoId),
            ],
            'contenido' => 'required|string',
        ]);
        $tarea = Tarea::findOrFail($datos['tarea_id']);
        abort_unless($tarea->grupo->perteneceUsuario($request->user()), 403, 'No perteneces a este grupo.');
        $entrega = Entrega::create([...$datos, 'alumno_id' => $alumnoId]);
        return response()->json([
            'mensaje' => 'Entrega creada exitosamente',
            'entrega' => $entrega->load(['tarea', 'alumno.user']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Entrega $entrega)
    {
        abort_unless($entrega->tarea->grupo->perteneceUsuario($request->user()), 403, 'No perteneces a este grupo.');
        return response()->json($entrega->load(['tarea', 'alumno.user']), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entrega $entrega)
    {
        abort_unless($entrega->tarea->grupo->perteneceUsuario($request->user()), 403, 'No perteneces a este grupo.');
        $datos = $request->validate([
            'contenido' => 'sometimes|required|string',
            'calificacion' => 'sometimes|nullable|numeric|min:0|max:10',
        ]);
        if (array_key_exists('calificacion', $datos) && !$request->user()->esMaestro()) {
            return response()->json(['mensaje' => 'Solo los maestros pueden calificar entregas'], 403);
        }
        $entrega->update($datos);
        return response()->json([
            'mensaje' => 'Entrega actualizada exitosamente',
            'entrega' => $entrega->load(['tarea', 'alumno.user']),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Entrega $entrega)
    {
        abort_unless($entrega->tarea->grupo->perteneceUsuario($request->user()), 403, 'No perteneces a este grupo.');
        $entrega->delete();
        return response()->json([
            'mensaje' => 'Entrega eliminada exitosamente',
        ], 200);
    }
}
