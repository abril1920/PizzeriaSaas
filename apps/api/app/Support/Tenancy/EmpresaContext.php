<?php

namespace App\Support\Tenancy;

class EmpresaContext
{
    private ?string $empresaId = null;

    public function set(string $empresaId): void { $this->empresaId = $empresaId; }
    public function id(): ?string { return $this->empresaId; }
}
