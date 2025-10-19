<?php

namespace App\Pipelines\Product;

use Closure;

class FilterByStock
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('stock_status')) {
            $status = request('stock_status');
            if ($status === 'low') {
                $query->whereColumn('current_stock', '<=', 'min_stock');
            } elseif ($status === 'out') {
                $query->where('current_stock', 0);
            }
        }

        return $next($query);
    }
}
