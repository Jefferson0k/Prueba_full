<?php

namespace App\Pipelines\Provider\Filters;

use Closure;

class FilterBySearch
{
    public function handle($query, Closure $next)
    {
        $search = request()->input('search');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('ruc', 'Ilike', "%{$search}%")
                  ->orWhere('razon_social', 'Ilike', "%{$search}%");
            });
        }

        return $next($query);
    }
}
