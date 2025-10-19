<?php

namespace App\Pipelines\PagosPersonal;

use Closure;
use Illuminate\Support\Facades\Auth;

class PorSucursal
{
    public function handle($query, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->sub_branch_id) {
            $query->where('sub_branch_id', $user->sub_branch_id);
        }

        return $next($query);
    }
}
