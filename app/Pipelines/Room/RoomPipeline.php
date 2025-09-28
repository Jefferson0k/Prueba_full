<?php

namespace App\Pipelines\Room;

use App\Pipelines\Room\Filters\FilterByActive;
use App\Pipelines\Room\Filters\FilterByFloor;
use App\Pipelines\Room\Filters\FilterByRoomType;
use App\Pipelines\Room\Filters\FilterByStatus;
use App\Pipelines\Room\Filters\FilterBySubBranch;
use App\Pipelines\Room\Filters\SearchByText;
use App\Pipelines\Room\Filters\SortResults;
use Illuminate\Pipeline\Pipeline;

class RoomPipeline
{
    protected $pipes = [
        FilterBySubBranch::class,
        FilterByFloor::class,
        FilterByStatus::class,
        FilterByRoomType::class,
        FilterByActive::class,
        SearchByText::class,
        SortResults::class,
    ];

    public function handle($query, $filters)
    {
        return app(Pipeline::class)
            ->send($query)
            ->through($this->pipes)
            ->with($filters)
            ->thenReturn();
    }
}
