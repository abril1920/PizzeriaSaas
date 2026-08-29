<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\EmpresaContext;
use Closure;
use Illuminate\Http\Request;

class SetEmpresaContext
{
    public function handle(Request $request, Closure $next): mixed
    {
        app(EmpresaContext::class)->set($request->user()->empresa_id);
        return $next($request);
    }
}
