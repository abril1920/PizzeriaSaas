<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): array
    {
        $data = $request->validate(['correo' => ['required', 'email'], 'password' => ['required', 'string']]);
        $user = User::where('correo', $data['correo'])->where('estado', 'ACTIVO')->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) throw ValidationException::withMessages(['correo' => ['Las credenciales no son validas.']]);
        return ['token' => $user->createToken('web')->plainTextToken, 'user' => $this->profile($user)];
    }
    public function me(Request $request): array { return ['user' => $this->profile($request->user())]; }
    private function profile(User $user): array { return ['id' => $user->id, 'nombre' => $user->nombre, 'correo' => $user->correo, 'empresa_id' => $user->empresa_id]; }
}
