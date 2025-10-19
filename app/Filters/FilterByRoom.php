<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByRoom
{
    public function handle(Builder $query, Closure $next)
    {
        if (request()->has('room_id') && !empty(request('room_id'))) {
            $query->where('room_id', request('room_id'));
        }

        if (request()->has('branch_id') && !empty(request('branch_id'))) {
            $query->whereHas('room.floor', function($q) {
                $q->where('branch_id', request('branch_id'));
            });
        }

        return $next($query);
    }
}