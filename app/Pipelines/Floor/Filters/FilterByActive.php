<?php

namespace App\Pipelines\Floor\Filters;

use Closure;

class FilterByActive
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function handle($query, Closure $next)
    {
        if (isset($this->filters['is_active'])) {
            $query->where('is_active', $this->filters['is_active']);
        }

        return $next($query);
    }
}
