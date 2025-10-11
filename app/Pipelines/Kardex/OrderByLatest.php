<?php

namespace App\Pipelines\Kardex;

use Closure;

class OrderByLatest
{
    public function handle($query, Closure $next)
    {
        $query->orderByDesc('created_at');
        return $next($query);
    }
}
