<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToCompany, UsesUuid;

    protected $table = 'products';

    protected $fillable = ['category_id', 'name', 'description', 'price', 'status'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }
}
