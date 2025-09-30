<?php

namespace App\Pipelines\Movement;

use App\Pipelines\Movement\Filters\FilterBySearch;
use Illuminate\Pipeline\Pipeline;

class MovementPipeline
{
    protected $pipes = [
        FilterBySearch::class,
    ];

    public function handle($query)
    {
        return app(Pipeline::class)
            ->send($query)
            ->through($this->pipes)
            ->thenReturn();
    }
}
