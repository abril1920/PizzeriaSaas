<?php

namespace App\Modules\Identity\Infrastructure\Persistence\Mappers;

use App\Models\User;
use App\Modules\Identity\Domain\Entities\UserIdentity;

class UserIdentityMapper
{
    public function toEntity(User $model): UserIdentity
    {
        return new UserIdentity(
            $model->id,
            $model->company_id,
            $model->first_name,
            $model->email,
            $model->password,
            $model->status,
        );
    }
}
