<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tareas = Tarea::with(['grupo', 'maestro.user'])
            ->when($request->query('grupo_id'), fn ($q, $grupoId) => $q->where('grupo_id', $grupoId))
            ->latest('created_at')
            ->get();
        return response()->json($tareas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->esMaestro()) {
            return response()->json(['mensaje' => 'Solo los maestros pueden crear tareas'], 403);
        }
        $datos = $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_entrega' => 'nullable|date',
        ]);
        $tarea = Tarea::create([...$datos, 'maestro_id' => $request->user()->maestro->id]);
        return response()->json([
            'mensaje' => 'Tarea creada exitosamente',
            'tarea' => $tarea->load(['grupo', 'maestro.user']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tarea $tarea)
    {
        return response()->json($tarea->load(['grupo', 'maestro.user', 'entregas.alumno.user']), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tarea $tarea)
    {
        if (!$request->user()->esMaestro()) {
            return response()->json(['mensaje' => 'Solo los maestros pueden actualizar tareas'], 403);
        }
        $datos = $request->validate([
            'grupo_id' => 'sometimes|required|exists:grupos,id',
            'titulo' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_entrega' => 'nullable|date',
        ]);
        $tarea->update($datos);
        return response()->json([
            'mensaje' => 'Tarea actualizada exitosamente',
            'tarea' => $tarea->load(['grupo', 'maestro.user']),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Tarea $tarea)
    {
        if (!$request->user()->esMaestro()) {
            return response()->json(['mensaje' => 'Solo los maestros pueden eliminar tareas'], 403);
        }
        $tarea->delete();
        return response()->json([
            'mensaje' => 'Tarea eliminada exitosamente',
        ], 200);
    }
}
