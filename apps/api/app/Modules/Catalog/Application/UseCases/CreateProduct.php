<?php

namespace App\Modules\Catalog\Application\UseCases;

use App\Modules\Catalog\Application\Data\CreateProductData;
use App\Modules\Catalog\Domain\Entities\Product;
use App\Modules\Catalog\Domain\Repositories\ProductRepository;

class CreateProduct
{
    public function __construct(private ProductRepository $products) {}

    public function handle(CreateProductData $data): Product
    {
        return $this->products->create($data);
    }
}
