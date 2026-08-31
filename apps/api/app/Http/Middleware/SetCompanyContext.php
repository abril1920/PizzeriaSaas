<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\CompanyContext;
use Closure;
use Illuminate\Http\Request;

class SetCompanyContext
{
    public function handle(Request $request, Closure $next): mixed
    {
        app(CompanyContext::class)->set($request->user()->company_id);

        return $next($request);
    }
}
