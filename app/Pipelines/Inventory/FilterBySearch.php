<?php

namespace App\Pipelines\Inventory;

use Closure;

class FilterBySearch
{
    public function handle($query, Closure $next)
    {
        $search = request('search');
        if ($search) {
            $term = strtolower($search);
            $query->whereHas('product', function ($q) use ($term) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]);
            });
        }
        return $next($query);
    }
}
