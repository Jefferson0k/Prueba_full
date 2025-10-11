<?php

namespace App\Pipelines\SubBranch;

use Closure;
use Illuminate\Support\Facades\Auth;

class PrioritizeUserSubBranch
{
    public function handle($query, Closure $next)
    {
        $user = Auth::user();

        // Si el usuario tiene sub_branch_id, ordena para que aparezca primero
        if ($user && $user->sub_branch_id) {
            $query->orderByRaw("CASE WHEN id = ? THEN 0 ELSE 1 END", [$user->sub_branch_id]);
        }

        return $next($query);
    }
}
