<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message'=>'Sesion cerrada']);
    }
}