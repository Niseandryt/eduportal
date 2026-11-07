<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'carnet' => 'required_without:departamento|prohibits:departamento|string|max:20|unique:alumnos,carnet',
            'departamento' => 'required_without:carnet|prohibits:carnet|string|max:100',
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            if (isset($data['carnet'])) {
                $user->alumno()->create(['carnet' => $data['carnet']]);
            } else {
                $user->maestro()->create(['departamento' => $data['departamento']]);
            }

            return $user;
        });

        return response()->json([
            'mensaje' => 'Usuario registrado exitosamente',
            'token' => $user->createToken('clase-api')->plainTextToken,
            'user' => $user->load(['alumno', 'maestro']),
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::isHashed($user->password) || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }
        return response()->json([
            'token' => $user->createToken('clase-api')->plainTextToken,
            'user' => $user->load(['alumno', 'maestro']),
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message'=>'Sesion cerrada']);
    }
}