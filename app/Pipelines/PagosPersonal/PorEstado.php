<?php

namespace App\Pipelines\PagosPersonal;

use Closure;

class PorEstado
{
    public function handle($query, Closure $next)
    {
        if (request()->has('estado')) {
            $query->where('estado', request('estado'));
        }

        return $next($query);
    }
}
