<?php

namespace App\Pipelines\Product;

use Closure;

class FilterByNameOrCode
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('q')) {
            $term = request('q');
            $query->whereHas('product', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('code', 'like', "%{$term}%");
            });
        }

        return $next($query);
    }
}
