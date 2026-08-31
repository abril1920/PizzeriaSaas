<?php

namespace App\Modules\Identity\Application\Data;

final readonly class RegisterUserData
{
    public function __construct(
        public string $empresaNombre,
        public string $nit,
        public string $nombre,
        public ?string $apellido,
        public string $correo,
        public string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['empresa_nombre'],
            $data['nit'],
            $data['nombre'],
            $data['apellido'] ?? null,
            $data['correo'],
            $data['password'],
        );
    }
}
