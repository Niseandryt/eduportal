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
        return response()->json(Grupo::latest('created_at')->get(), 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        $grupo = Grupo::create($datos);
        return response()->json([
            'mensaje' => 'Grupo creado exitosamente',
            'grupo' => $grupo,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Grupo $grupo)
    {
        return response()->json($grupo, 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grupo $grupo)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        $grupo->update($datos);
        return response()->json([
            'mensaje' => 'Grupo actualizado exitosamente',
            'grupo' => $grupo,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grupo $grupo)
    {
        $grupo->delete();
        return response()->json([
            'mensaje' => 'Grupo eliminado exitosamente',
        ], 204);
    }
}
