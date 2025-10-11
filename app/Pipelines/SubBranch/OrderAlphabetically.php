<?php

namespace App\Pipelines\SubBranch;

use Closure;

class OrderAlphabetically
{
    public function handle($query, Closure $next)
    {
        // Asegura orden alfabético luego de priorizar la del usuario
        $query->orderBy('name', 'asc');
        return $next($query);
    }
}
