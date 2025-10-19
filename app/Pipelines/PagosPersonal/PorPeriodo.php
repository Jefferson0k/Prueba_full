<?php

namespace App\Pipelines\PagosPersonal;

use Closure;

class PorPeriodo
{
    public function handle($query, Closure $next)
    {
        if (request()->has('periodo')) {
            $query->where('periodo', request('periodo'));
        }

        return $next($query);
    }
}
