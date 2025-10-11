<?php

namespace App\Pipelines\Kardex;

use Closure;

class FilterByProduct
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('product_id')) {
            $query->where('product_id', request('product_id'));
        }

        return $next($query);
    }
}
