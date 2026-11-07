<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $materiales = Material::with(['grupo', 'maestro.user'])
            ->when($request->query('grupo_id'), fn ($q, $grupoId) => $q->where('grupo_id', $grupoId))
            ->latest('created_at')
            ->get();
        return response()->json($materiales, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->esMaestro()) {
            return response()->json(['mensaje' => 'Solo los maestros pueden crear materiales'], 403);
        }
        $datos = $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'enlace' => 'nullable|url|max:255',
        ]);
        $material = Material::create([...$datos, 'maestro_id' => $request->user()->maestro->id]);
        return response()->json([
            'mensaje' => 'Material creado exitosamente',
            'material' => $material->load(['grupo', 'maestro.user']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        return response()->json($material->load(['grupo', 'maestro.user']), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        if (!$request->user()->esMaestro()) {
            return response()->json(['mensaje' => 'Solo los maestros pueden actualizar materiales'], 403);
        }
        $datos = $request->validate([
            'grupo_id' => 'sometimes|required|exists:grupos,id',
            'titulo' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'enlace' => 'nullable|url|max:255',
        ]);
        $material->update($datos);
        return response()->json([
            'mensaje' => 'Material actualizado exitosamente',
            'material' => $material->load(['grupo', 'maestro.user']),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Material $material)
    {
        if (!$request->user()->esMaestro()) {
            return response()->json(['mensaje' => 'Solo los maestros pueden eliminar materiales'], 403);
        }
        $material->delete();
        return response()->json([
            'mensaje' => 'Material eliminado exitosamente',
        ], 200);
    }
}
