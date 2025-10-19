<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByCustomer
{
    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('customer_id') && !empty(request('customer_id'))) {
            $query->where('customers_id', request('customer_id'));
        }

        if (request()->has('customer_search') && !empty(request('customer_search'))) {
            $search = request('customer_search');
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                  ->orWhere('document_number', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        return $next($query);
    }
}