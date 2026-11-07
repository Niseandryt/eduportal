<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Grupo::with('maestro.user')->latest('created_at')->get(), 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->esMaestro()) {
            return response()->json(['mensaje' => 'Solo los maestros pueden crear grupos'], 403);
        }
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);
        $grupo = Grupo::create([...$datos, 'maestro_id' => $request->user()->maestro->id]);
        return response()->json([
            'mensaje' => 'Grupo creado exitosamente',
            'grupo' => $grupo->load('maestro.user'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Grupo $grupo)
    {
        return response()->json(
            $grupo->load(['maestro.user', 'users', 'tareas', 'materiales', 'foros']),
            200
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grupo $grupo)
    {
        if (!$request->user()->esMaestro()) {
            return response()->json(['mensaje' => 'Solo los maestros pueden actualizar grupos'], 403);
        }
        $datos = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);
        $grupo->update($datos);
        return response()->json([
            'mensaje' => 'Grupo actualizado exitosamente',
            'grupo' => $grupo->load('maestro.user'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Grupo $grupo)
    {
        if (!$request->user()->esMaestro()) {
            return response()->json(['mensaje' => 'Solo los maestros pueden eliminar grupos'], 403);
        }
        $grupo->delete();
        return response()->json([
            'mensaje' => 'Grupo eliminado exitosamente',
        ], 200);
    }
}
