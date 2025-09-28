<?php

namespace App\Http\Controllers\Web\Floor;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\SubBranch;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class FloorWeb extends Controller{
    public function view($subBranchId){
        Gate::authorize('viewAny', Floor::class);
        $subBranch = SubBranch::with('branch')->findOrFail($subBranchId);
        $floors = Floor::where('sub_branch_id', $subBranchId)
                    ->withCount(['rooms', 'availableRooms'])
                    ->orderBy('floor_number')
                    ->get();
        return Inertia::render('panel/Floor/indexFloor', [
            'subBranch' => $subBranch,
            'floors' => $floors
        ]);
    }
}

