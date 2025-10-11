<?php

namespace App\Pipelines\Kardex;

use Closure;
use Carbon\Carbon;

class FilterByDateRange
{
    public function handle($query, Closure $next)
    {
        $fechaInicio = request('fecha_inicio');
        $fechaFin = request('fecha_fin');

        if ($fechaInicio && $fechaFin) {
            $inicio = Carbon::parse($fechaInicio)->startOfDay();
            $fin = Carbon::parse($fechaFin)->endOfDay();

            $query->whereBetween('created_at', [$inicio, $fin]);
        }

        return $next($query);
    }
}
