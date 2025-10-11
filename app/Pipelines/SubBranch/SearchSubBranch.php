<?php

namespace App\Pipelines\SubBranch;

use Closure;
use Illuminate\Support\Str;

class SearchSubBranch
{
    public function handle($query, Closure $next)
    {
        $search = request('search');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%")
                  ->orWhere('address', 'ilike', "%{$search}%");
            });
        }

        return $next($query);
    }
}
