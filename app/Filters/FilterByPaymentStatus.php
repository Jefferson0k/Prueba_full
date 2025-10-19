<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByPaymentStatus
{
    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('payment_status') && !empty(request('payment_status'))) {
            $paymentStatus = request('payment_status');
            
            if ($paymentStatus === 'paid') {
                $query->whereRaw('paid_amount >= total_amount');
            } elseif ($paymentStatus === 'partial') {
                $query->where('paid_amount', '>', 0)
                      ->whereRaw('paid_amount < total_amount');
            } elseif ($paymentStatus === 'unpaid') {
                $query->where('paid_amount', 0);
            }
        }

        return $next($query);
    }
}