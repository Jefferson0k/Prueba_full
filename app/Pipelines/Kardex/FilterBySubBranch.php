<?php

namespace App\Pipelines\Kardex;

use Closure;
use Illuminate\Support\Facades\Auth;

class FilterBySubBranch
{
    public function handle($query, Closure $next)
    {
        $user = Auth::user();
        $subBranchId = request('sub_branch_id', $user->sub_branch_id);

        if ($subBranchId) {
            $query->where('sub_branch_id', $subBranchId);
        }

        return $next($query);
    }
}
