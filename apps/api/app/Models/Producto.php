<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use BelongsToEmpresa, UsesUuid;

    protected $table = 'productos';
    protected $fillable = ['categoria_id', 'nombre', 'descripcion', 'precio', 'estado'];
    protected function casts(): array { return ['precio' => 'decimal:2']; }
}
