<?php

namespace App\Modules\Identity\Infrastructure\Persistence\Mappers;

use App\Models\Company as CompanyModel;
use App\Modules\Identity\Domain\Entities\Company;

class CompanyMapper
{
    public function toEntity(CompanyModel $model): Company
    {
        return new Company($model->id, $model->name, $model->tax_id);
    }
}
