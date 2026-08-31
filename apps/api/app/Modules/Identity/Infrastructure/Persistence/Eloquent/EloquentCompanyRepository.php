<?php

namespace App\Modules\Identity\Infrastructure\Persistence\Eloquent;

use App\Models\Company as CompanyModel;
use App\Modules\Identity\Application\Data\RegisterUserData;
use App\Modules\Identity\Domain\Entities\Company;
use App\Modules\Identity\Domain\Repositories\CompanyRepository;
use App\Modules\Identity\Infrastructure\Persistence\Mappers\CompanyMapper;

class EloquentCompanyRepository implements CompanyRepository
{
    public function __construct(private CompanyMapper $mapper) {}

    public function create(RegisterUserData $data): Company
    {
        $model = CompanyModel::create(['name' => $data->empresaNombre, 'tax_id' => $data->nit]);

        return $this->mapper->toEntity($model);
    }
}
