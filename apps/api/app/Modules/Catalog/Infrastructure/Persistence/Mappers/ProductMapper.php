<?php

namespace App\Modules\Catalog\Infrastructure\Persistence\Mappers;

use App\Models\Product as ProductModel;
use App\Modules\Catalog\Application\Data\CreateProductData;
use App\Modules\Catalog\Domain\Entities\Product;

class ProductMapper
{
    public function toEntity(ProductModel $model): Product
    {
        return new Product($model->id, $model->company_id, $model->category_id, $model->name, $model->description, $model->price, $model->status);
    }

    public function toPersistence(CreateProductData $data): array
    {
        return ['category_id' => $data->categoriaId, 'name' => $data->nombre, 'description' => $data->descripcion, 'price' => $data->precio, 'status' => $data->estado];
    }
}
