<?php

namespace App\Pipelines\Provider;

use App\Pipelines\Provider\Filters\FilterBySearch;
use Illuminate\Pipeline\Pipeline;

class ProviderPipeline
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
