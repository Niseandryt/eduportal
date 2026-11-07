<?php

namespace App\Http\Controllers;

use App\Models\Respuesta;
use Illuminate\Http\Request;

class RespuestaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $respuestas = Respuesta::with(['foro', 'user'])
            ->when($request->query('foro_id'), fn ($q, $foroId) => $q->where('foro_id', $foroId))
            ->latest('created_at')
            ->get();
        return response()->json($respuestas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'foro_id' => 'required|exists:foros,id',
            'contenido' => 'required|string',
        ]);
        $respuesta = Respuesta::create([...$datos, 'user_id' => $request->user()->id]);
        return response()->json([
            'mensaje' => 'Respuesta creada exitosamente',
            'respuesta' => $respuesta->load(['foro', 'user']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Respuesta $respuesta)
    {
        return response()->json($respuesta->load(['foro', 'user']), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Respuesta $respuesta)
    {
        $datos = $request->validate([
            'contenido' => 'sometimes|required|string',
        ]);
        $respuesta->update($datos);
        return response()->json([
            'mensaje' => 'Respuesta actualizada exitosamente',
            'respuesta' => $respuesta->load(['foro', 'user']),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Respuesta $respuesta)
    {
        $respuesta->delete();
        return response()->json([
            'mensaje' => 'Respuesta eliminada exitosamente',
        ], 200);
    }
}
