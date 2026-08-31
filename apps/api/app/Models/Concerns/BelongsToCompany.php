<?php

namespace App\Models\Concerns;

use App\Support\Tenancy\CompanyContext;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $query): void {
            if ($id = app(CompanyContext::class)->id()) {
                $query->where('company_id', $id);
            }
        });

        static::creating(fn ($model) => $model->company_id ??= app(CompanyContext::class)->id());
    }
}
