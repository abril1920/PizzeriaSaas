<?php

namespace App\Modules\Catalog\Domain\Repositories;

use App\Modules\Catalog\Application\Data\CreateProductData;
use App\Modules\Catalog\Domain\Entities\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepository
{
    public function paginateLatest(int $perPage = 15): LengthAwarePaginator;
    public function create(CreateProductData $data): Product;
}
