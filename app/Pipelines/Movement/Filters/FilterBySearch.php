<?php

namespace App\Pipelines\Movement\Filters;

use Closure;

class FilterBySearch
{
    public function handle($query, Closure $next)
    {
        $search = request()->input('search');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ILIKE', "%{$search}%")
                  ->orWhereHas('provider', function ($q2) use ($search) {
                      $q2->where('ruc', 'ILIKE', "%{$search}%")
                         ->orWhere('razon_social', 'ILIKE', "%{$search}%");
                  });
            });
        }

        return $next($query);
    }
}
