<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait UsesUuid
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function bootUsesUuid(): void
    {
        static::creating(fn ($model) => $model->{$model->getKeyName()} ??= (string) Str::uuid());
    }
}
