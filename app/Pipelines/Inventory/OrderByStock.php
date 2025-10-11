<?php

namespace App\Pipelines\Inventory;

use Closure;

class OrderByStock
{
    public function handle($query, Closure $next)
    {
        $query->orderBy('current_stock', 'desc');
        return $next($query);
    }
}
