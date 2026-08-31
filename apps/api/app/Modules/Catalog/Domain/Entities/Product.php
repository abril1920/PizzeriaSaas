<?php

namespace App\Modules\Catalog\Domain\Entities;

final readonly class Product
{
    public function __construct(
        public string $id,
        public string $empresaId,
        public ?string $categoriaId,
        public string $nombre,
        public ?string $descripcion,
        public string $precio,
        public string $estado,
    ) {}
}
