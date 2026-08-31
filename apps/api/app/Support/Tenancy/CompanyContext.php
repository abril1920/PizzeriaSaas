<?php

namespace App\Support\Tenancy;

class CompanyContext
{
    private ?string $companyId = null;

    public function set(string $companyId): void { $this->companyId = $companyId; }
    public function id(): ?string { return $this->companyId; }
}
