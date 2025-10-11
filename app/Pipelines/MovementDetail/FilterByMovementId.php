<?php

namespace App\Pipelines\MovementDetail;

use Closure;

class FilterByMovementId
{
    public function handle($query, Closure $next)
    {
        $movementId = request('movement_id');
        if ($movementId) {
            $query->where('movement_id', $movementId);
        }
        return $next($query);
    }
}
