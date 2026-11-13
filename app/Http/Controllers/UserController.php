<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(User::with(['alumno', 'maestro'])->latest('created_at')->get(), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user->load(['alumno', 'maestro', 'grupos']), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $datos = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'sometimes|required|string|min:8',
        ]);
        $user->update($datos);
        return response()->json([
            'mensaje' => 'Usuario actualizado exitosamente',
            'user' => $user->load(['alumno', 'maestro']),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'mensaje' => 'Usuario eliminado exitosamente',
        ], 200);
    }
}
