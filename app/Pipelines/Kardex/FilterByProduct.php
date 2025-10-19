<?php

namespace App\Pipelines\Kardex;

use Closure;
use Illuminate\Support\Str;

class FilterByProduct
{
    public function handle($query, Closure $next)
    {
        $productId = request('product_id');

        if ($productId && Str::isUuid($productId)) {
            $query->where('product_id', $productId);
        }

        return $next($query);
    }
}
