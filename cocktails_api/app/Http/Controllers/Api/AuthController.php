<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'in:admin,user',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = $validated['role'] ?? 'user';
        
        $user = User::create($validated);
        

        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son válidas.'],
            ]);
        }

        $user = Auth::user();

        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'message' => 'Inicio de sesión correcto',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
        {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}
