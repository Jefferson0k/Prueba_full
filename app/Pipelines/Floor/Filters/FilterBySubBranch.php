<?php

namespace App\Pipelines\Floor\Filters;

use Closure;

class FilterBySubBranch{
    protected $filters;
    public function __construct(array $filters = []){
        $this->filters = $filters;
    }
    public function handle($query, Closure $next){
        if (!empty($this->filters['sub_branch_id'])) {
            $query->where('sub_branch_id', $this->filters['sub_branch_id']);
        }
        return $next($query);
    }
}
