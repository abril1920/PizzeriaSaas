<?php

namespace App\Modules\Identity\Domain\Entities;

final readonly class Company
{
    public function __construct(public string $id, public string $nombre, public string $nit) {}
}
