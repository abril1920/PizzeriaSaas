<?php

namespace App\Modules\Identity\Application\UseCases;

use App\Modules\Identity\Application\Data\AuthenticatedSession;
use App\Modules\Identity\Application\Data\LoginCredentials;
use App\Modules\Identity\Domain\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginUser
{
    public function __construct(private UserRepository $users) {}

    public function handle(LoginCredentials $credentials): AuthenticatedSession
    {
        $user = $this->users->findActiveByCorreo($credentials->correo);

        if (! $user || ! Hash::check($credentials->password, $user->passwordHash)) {
            throw ValidationException::withMessages(['correo' => ['Las credenciales no son válidas.']]);
        }

        return new AuthenticatedSession($this->users->issueToken($user), $user);
    }
}
