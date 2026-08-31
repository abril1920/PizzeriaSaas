<?php

namespace App\Modules\Catalog\Application\UseCases;

use App\Modules\Catalog\Domain\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProducts
{
    public function __construct(private ProductRepository $products) {}

    public function handle(): LengthAwarePaginator
    {
        return $this->products->paginateLatest();
    }
}
