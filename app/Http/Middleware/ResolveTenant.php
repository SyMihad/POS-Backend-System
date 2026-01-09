<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->header('X-Tenant-ID');

        abort_if(!$tenantId, 400, 'X-Tenant-ID header missing');

        $tenant = Tenant::findOrFail($tenantId);

        app()->instance('tenant', $tenant);

        $user = auth()->user();

        if ($request->hasHeader('x-tenant-id')) {
            if ($request->header('x-tenant-id') != $user->tenant_id) {
                abort(403, 'Unauthorized tenant access');
            }
        }

        return $next($request);
    }
}
