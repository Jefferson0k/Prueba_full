<?php

namespace App\Services;

use App\Models\Floor;
use App\Pipelines\Floor\FloorPipeline;

class FloorService{
    protected $floorPipeline;
    public function __construct(FloorPipeline $floorPipeline){
        $this->floorPipeline = $floorPipeline;
    }
    public function search(array $filters = []){
        $query = Floor::with(['subBranch']);
        return $this->floorPipeline->handle($query, $filters);
    }
    public function getBySubBranch($subBranchId, array $filters = []){
        $filters['sub_branch_id'] = $subBranchId;
        return $this->search($filters);
    }
    public function getBySubBranchWithRoomCounts($subBranchId, array $filters = []){
        $query = Floor::with(['subBranch'])
                    ->where('sub_branch_id', $subBranchId)
                    ->withRoomCounts();
        return $this->floorPipeline->handle($query, $filters);
    }
    public function searchWithRoomCounts(array $filters = []){
        $filters['with_room_counts'] = true;
        return $this->search($filters);
    }
}