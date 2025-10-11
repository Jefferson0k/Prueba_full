<?php

namespace App\Pipelines\MovementDetail;

use Closure;

class SearchMovementDetail
{
    public function handle($request, Closure $next)
    {
        if ($search = request('search')) {
            return $next($request)->whereHas('product', function ($query) use ($search) {
                $query->where('name', 'ILIKE', "%{$search}%");
            });
        }

        return $next($request);
    }
}
