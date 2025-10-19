<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByPaymentMethod
{
    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('payment_method_id') && !empty(request('payment_method_id'))) {
            $query->where('payment_method_id', request('payment_method_id'));
        }

        return $next($query);
    }
}