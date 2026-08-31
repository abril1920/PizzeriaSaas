<?php

namespace App\Modules\Catalog\Application\Data;

final readonly class CreateProductData
{
    public function __construct(public ?string $categoriaId, public string $nombre, public ?string $descripcion, public string $precio, public string $estado) {}

    public static function fromArray(array $data): self
    {
        return new self($data['categoria_id'] ?? null, $data['nombre'], $data['descripcion'] ?? null, $data['precio'], $data['estado'] ?? 'ACTIVO');
    }
}
