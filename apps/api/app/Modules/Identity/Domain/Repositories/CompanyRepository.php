<?php

namespace App\Modules\Identity\Domain\Repositories;

use App\Modules\Identity\Application\Data\RegisterUserData;
use App\Modules\Identity\Domain\Entities\Company;

interface CompanyRepository
{
    public function create(RegisterUserData $data): Company;
}
