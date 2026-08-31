<?php

namespace App\Modules\Identity\Application\Data;

use App\Modules\Identity\Domain\Entities\UserIdentity;

final readonly class AuthenticatedSession
{
    public function __construct(public string $token, public UserIdentity $user) {}
}
