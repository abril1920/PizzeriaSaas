<?php

namespace App\Modules\Identity\Application\Data;

final readonly class LoginCredentials
{
    public function __construct(public string $correo, public string $password) {}

    public static function fromArray(array $data): self
    {
        return new self($data['correo'], $data['password']);
    }
}
