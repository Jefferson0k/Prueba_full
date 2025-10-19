<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchBooking
{
    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('search') && !empty(request('search'))) {
            $search = request('search');
            
            $query->where(function($q) use ($search) {
                $q->where('booking_code', 'ilike', "%{$search}%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('full_name', 'ilike', "%{$search}%")
                         ->orWhere('document_number', 'ilike', "%{$search}%");
                  })
                  ->orWhereHas('room', function($q3) use ($search) {
                      $q3->where('name', 'ilike', "%{$search}%")
                         ->orWhere('room_number', 'ilike', "%{$search}%");
                  });
            });
        }

        return $next($query);
    }
}