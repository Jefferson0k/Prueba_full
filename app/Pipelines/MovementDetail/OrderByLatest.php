<?php

namespace App\Pipelines\MovementDetail;

use Closure;

class OrderByLatest
{
    public function handle($request, Closure $next)
    {
        return $next($request)->orderBy('created_at', 'desc');
    }
}
