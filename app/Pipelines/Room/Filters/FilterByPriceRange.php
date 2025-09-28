<?php

namespace App\Pipelines\Room\Filters;

use Closure;

class FilterByPriceRange{
    public function handle($query, Closure $next, $filters = []){
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $priceType = $filters['price_type'] ?? 'base_price_per_hour';
            
            $query->whereHas('roomType', function ($q) use ($filters, $priceType) {
                if (isset($filters['min_price'])) {
                    $q->where($priceType, '>=', $filters['min_price']);
                }
                
                if (isset($filters['max_price'])) {
                    $q->where($priceType, '<=', $filters['max_price']);
                }
            });
        }

        return $next($query);
    }
}
