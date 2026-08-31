<?php

namespace App\Modules\Identity\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use App\Modules\Identity\Application\Data\RegisterUserData;
use App\Modules\Identity\Domain\Entities\UserIdentity;
use App\Modules\Identity\Domain\Repositories\UserRepository;
use App\Modules\Identity\Infrastructure\Persistence\Mappers\UserIdentityMapper;
use Laravel\Sanctum\PersonalAccessToken;

class EloquentUserRepository implements UserRepository
{
    public function __construct(private UserIdentityMapper $mapper) {}

    public function create(string $companyId, RegisterUserData $data): UserIdentity
    {
        $model = User::create([
            'company_id' => $companyId,
            'first_name' => $data->nombre,
            'last_name' => $data->apellido,
            'email' => $data->correo,
            'password' => $data->password,
        ]);

        return $this->mapper->toEntity($model);
    }

    public function findActiveByCorreo(string $correo): ?UserIdentity
    {
        $model = User::query()->where('email', $correo)->where('status', 'ACTIVE')->first();

        return $model ? $this->mapper->toEntity($model) : null;
    }

    public function findById(string $id): UserIdentity
    {
        return $this->mapper->toEntity(User::query()->findOrFail($id));
    }

    public function issueToken(UserIdentity $user): string
    {
        return User::query()->findOrFail($user->id)->createToken('web', ['api'])->plainTextToken;
    }

    public function revokeToken(string $userId, ?int $tokenId): void
    {
        if ($tokenId) {
            PersonalAccessToken::query()->whereKey($tokenId)->where('tokenable_id', $userId)->delete();
        }
    }
}
