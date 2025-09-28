<?php

namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByState
{
    public function __construct(private ?bool $state) {}

    public function __invoke(Builder $builder, Closure $next)
    {
        if (!is_null($this->state)) {
            $builder->where('is_active', $this->state);
        }

        return $next($builder);
    }
}
