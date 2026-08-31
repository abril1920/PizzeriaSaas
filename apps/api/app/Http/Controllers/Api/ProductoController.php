<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index() { return Producto::query()->latest()->paginate(); }
    public function store(Request $request): Producto
    {
        return Producto::create($request->validate(['categoria_id' => ['nullable', 'uuid'], 'nombre' => ['required', 'string', 'max:150'], 'descripcion' => ['nullable', 'string'], 'precio' => ['required', 'numeric', 'min:0'], 'estado' => ['sometimes', 'in:ACTIVO,INACTIVO']]));
    }
}
