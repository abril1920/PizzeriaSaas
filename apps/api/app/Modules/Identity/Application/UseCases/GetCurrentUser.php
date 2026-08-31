<?php

namespace App\Modules\Identity\Application\UseCases;

use App\Modules\Identity\Domain\Entities\UserIdentity;
use App\Modules\Identity\Domain\Repositories\UserRepository;

class GetCurrentUser
{
    public function __construct(private UserRepository $users) {}

    public function handle(string $id): UserIdentity
    {
        return $this->users->findById($id);
    }
}
