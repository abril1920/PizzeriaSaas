<?php

namespace App\Modules\Catalog\Infrastructure\Persistence\Eloquent;

use App\Models\Product as ProductModel;
use App\Modules\Catalog\Application\Data\CreateProductData;
use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use App\Modules\Catalog\Infrastructure\Persistence\Mappers\ProductMapper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepository
{
    public function __construct(private ProductMapper $mapper) {}

    public function paginateLatest(int $perPage = 15): LengthAwarePaginator
    {
        return ProductModel::query()->latest()->paginate($perPage)->through(fn (ProductModel $model) => $this->mapper->toEntity($model));
    }

    public function create(CreateProductData $data): Product
    {
        return $this->mapper->toEntity(ProductModel::create($this->mapper->toPersistence($data)));
    }
}
