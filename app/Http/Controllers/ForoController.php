<?php

namespace App\Http\Controllers;

use App\Models\Foro;
use App\Models\Grupo;
use Illuminate\Http\Request;

class ForoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $foros = Foro::with(['grupo', 'user'])
            ->whereHas('grupo', fn ($query) => $query->visiblePara($request->user()))
            ->when($request->query('grupo_id'), fn ($q, $grupoId) => $q->where('grupo_id', $grupoId))
            ->latest('created_at')
            ->get();
        return response()->json($foros, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
        ]);
        abort_unless(Grupo::visiblePara($request->user())->whereKey($datos['grupo_id'])->exists(), 403, 'No perteneces a este grupo.');
        $foro = Foro::create([...$datos, 'user_id' => $request->user()->id]);
        return response()->json([
            'mensaje' => 'Foro creado exitosamente',
            'foro' => $foro->load(['grupo', 'user']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Foro $foro)
    {
        abort_unless($foro->grupo->perteneceUsuario($request->user()), 403, 'No perteneces a este grupo.');
        return response()->json($foro->load(['grupo', 'user', 'respuestas.user']), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Foro $foro)
    {
        abort_unless($foro->grupo->perteneceUsuario($request->user()), 403, 'No perteneces a este grupo.');
        if ($foro->user_id !== $request->user()->id) {
            return response()->json(['mensaje' => 'Solo el autor puede editar este foro'], 403);
        }
        $datos = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'contenido' => 'sometimes|required|string',
        ]);
        $foro->update($datos);
        return response()->json([
            'mensaje' => 'Foro actualizado exitosamente',
            'foro' => $foro->load(['grupo', 'user']),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Foro $foro)
    {
        abort_unless($foro->grupo->perteneceUsuario($request->user()), 403, 'No perteneces a este grupo.');
        if ($foro->user_id !== $request->user()->id) {
            return response()->json(['mensaje' => 'Solo el autor puede eliminar este foro'], 403);
        }
        $foro->delete();
        return response()->json([
            'mensaje' => 'Foro eliminado exitosamente',
        ], 200);
    }
}
