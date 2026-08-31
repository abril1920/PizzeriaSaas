<?php

namespace App\Modules\Identity\Application\UseCases;

use App\Modules\Identity\Application\Data\AuthenticatedSession;
use App\Modules\Identity\Application\Data\RegisterUserData;
use App\Modules\Identity\Domain\Repositories\CompanyRepository;
use App\Modules\Identity\Domain\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class RegisterUser
{
    public function __construct(private CompanyRepository $companies, private UserRepository $users) {}

    public function handle(RegisterUserData $data): AuthenticatedSession
    {
        return DB::transaction(function () use ($data): AuthenticatedSession {
            $company = $this->companies->create($data);
            $user = $this->users->create($company->id, $data);

            return new AuthenticatedSession($this->users->issueToken($user), $user);
        });
    }
}
