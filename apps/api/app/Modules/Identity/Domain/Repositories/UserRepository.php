<?php

namespace App\Modules\Identity\Domain\Repositories;

use App\Modules\Identity\Application\Data\RegisterUserData;
use App\Modules\Identity\Domain\Entities\UserIdentity;

interface UserRepository
{
    public function create(string $empresaId, RegisterUserData $data): UserIdentity;
    public function findActiveByCorreo(string $correo): ?UserIdentity;
    public function findById(string $id): UserIdentity;
    public function issueToken(UserIdentity $user): string;
    public function revokeToken(string $userId, ?int $tokenId): void;
}
