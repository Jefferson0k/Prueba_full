<?php

namespace App\Pipelines\PagosPersonal;

use Closure;
use Illuminate\Support\Facades\Auth;

class PorSucursal
{
    public function handle($query, Closure $next)
    {
        $user = Auth::user();
        $sucursalFiltro = request('sub_branch_id');

        // Si se envía un sub_branch_id específico, usar ese
        if ($sucursalFiltro) {
            $query->where('sub_branch_id', $sucursalFiltro);
        }
        // Si no, usar la del usuario autenticado
        elseif ($user && $user->sub_branch_id) {
            $query->where('sub_branch_id', $user->sub_branch_id);
        }

        return $next($query);
    }
}