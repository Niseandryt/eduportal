<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $inscripciones = Inscripcion::with(['grupo', 'user'])
            ->whereHas('grupo', fn ($query) => $query->visiblePara($request->user()))
            ->when($request->query('grupo_id'), fn ($q, $grupoId) => $q->where('grupo_id', $grupoId))
            ->when($request->query('user_id'), fn ($q, $userId) => $q->where('user_id', $userId))
            ->latest('created_at')
            ->get();
        return response()->json($inscripciones, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless($request->user()->esMaestro(), 403, 'Solo los maestros pueden inscribir usuarios.');
        $datos = $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('inscripciones', 'user_id')->where('grupo_id', $request->grupo_id),
            ],
        ]);
        abort_unless(Grupo::visiblePara($request->user())->whereKey($datos['grupo_id'])->exists(), 403, 'No perteneces a este grupo.');
        $inscripcion = Inscripcion::create($datos);
        return response()->json([
            'mensaje' => 'Inscripcion creada exitosamente',
            'inscripcion' => $inscripcion->load(['grupo', 'user']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Inscripcion $inscripcion)
    {
        abort_unless($inscripcion->grupo->perteneceUsuario($request->user()), 403, 'No perteneces a este grupo.');
        return response()->json($inscripcion->load(['grupo', 'user']), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inscripcion $inscripcion)
    {
        abort_unless($request->user()->esMaestro(), 403, 'Solo los maestros pueden actualizar inscripciones.');
        abort_unless($inscripcion->grupo->perteneceUsuario($request->user()), 403, 'No perteneces a este grupo.');
        $datos = $request->validate([
            'grupo_id' => 'sometimes|required|exists:grupos,id',
            'user_id' => [
                'sometimes',
                'required',
                'exists:users,id',
                Rule::unique('inscripciones', 'user_id')
                    ->where('grupo_id', $request->grupo_id ?? $inscripcion->grupo_id)
                    ->ignore($inscripcion->id),
            ],
        ]);
        if (isset($datos['grupo_id'])) {
            abort_unless(Grupo::visiblePara($request->user())->whereKey($datos['grupo_id'])->exists(), 403, 'No perteneces a este grupo.');
        }
        $inscripcion->update($datos);
        return response()->json([
            'mensaje' => 'Inscripcion actualizada exitosamente',
            'inscripcion' => $inscripcion->load(['grupo', 'user']),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Inscripcion $inscripcion)
    {
        abort_unless($request->user()->esMaestro(), 403, 'Solo los maestros pueden eliminar inscripciones.');
        abort_unless($inscripcion->grupo->perteneceUsuario($request->user()), 403, 'No perteneces a este grupo.');
        $inscripcion->delete();
        return response()->json([
            'mensaje' => 'Inscripcion eliminada exitosamente',
        ], 200);
    }
}
