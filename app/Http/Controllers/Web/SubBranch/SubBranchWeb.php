<?php

namespace App\Http\Controllers\Web\SubBranch;

use App\Http\Controllers\Controller;
use App\Http\Resources\Branch\BranchResource;
use App\Models\Branch;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SubBranchWeb extends Controller{
    public function view($id): Response{
        $branch = Branch::with('subBranches')->findOrFail($id);
        Gate::authorize('view', $branch);
        return Inertia::render('panel/SubBranch/indexSubBranch', [
            'branch' => (new BranchResource($branch))->resolve(),
            'subBranches' => $branch->subBranches,
        ]);
    }
}
