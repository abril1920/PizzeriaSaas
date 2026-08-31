<?php

namespace App\Modules\Identity\Domain\Entities;

final readonly class UserIdentity
{
    public function __construct(
        public string $id,
        public string $empresaId,
        public string $nombre,
        public string $correo,
        public string $passwordHash,
        public string $estado,
    ) {}
}
