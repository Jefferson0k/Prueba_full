<?php

namespace App\Pipelines\Floor;

use App\Pipelines\Floor\Filters\FilterByActive;
use App\Pipelines\Floor\Filters\FilterBySubBranch;
use App\Pipelines\Floor\Filters\SearchByText;
use App\Pipelines\Floor\Filters\SortResults;
use App\Pipelines\Floor\Filters\WithRoomCounts;
use Illuminate\Pipeline\Pipeline;

class FloorPipeline {
    protected $pipes = [
        FilterBySubBranch::class,
        FilterByActive::class,
        SearchByText::class,
        SortResults::class,
        WithRoomCounts::class,
    ];
    protected $filters;
    public function handle($query, $filters){
        $this->filters = $filters;
        return app(Pipeline::class)
            ->send($query)
            ->through(array_map(fn ($pipe) => new $pipe($this->filters), $this->pipes))
            ->thenReturn();
    }
}
