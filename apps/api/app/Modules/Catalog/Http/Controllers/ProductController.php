<?php

namespace App\Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Application\Data\CreateProductData;
use App\Modules\Catalog\Application\UseCases\CreateProduct;
use App\Modules\Catalog\Application\UseCases\ListProducts;
use App\Modules\Catalog\Http\Requests\StoreProductRequest;
use App\Modules\Catalog\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(ListProducts $useCase)
    {
        return ProductResource::collection($useCase->handle());
    }

    public function store(StoreProductRequest $request, CreateProduct $useCase): JsonResponse
    {
        $product = $useCase->handle(CreateProductData::fromArray($request->validated()));

        return (new ProductResource($product))->response()->setStatusCode(201);
    }
}
