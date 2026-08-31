<?php

namespace App\Models\Concerns;

use App\Support\Tenancy\EmpresaContext;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToEmpresa
{
    protected static function bootBelongsToEmpresa(): void
    {
        static::addGlobalScope('empresa', function (Builder $query): void {
            if ($id = app(EmpresaContext::class)->id()) $query->where('empresa_id', $id);
        });
        static::creating(fn ($model) => $model->empresa_id ??= app(EmpresaContext::class)->id());
    }
}
