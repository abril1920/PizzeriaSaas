<?php

namespace App\Modules\Identity\Application\UseCases;

use App\Modules\Identity\Domain\Repositories\UserRepository;

class LogoutUser
{
    public function __construct(private UserRepository $users) {}

    public function handle(string $userId, ?int $tokenId): void
    {
        $this->users->revokeToken($userId, $tokenId);
    }
}
