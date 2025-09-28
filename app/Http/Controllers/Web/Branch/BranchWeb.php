<?php

namespace App\Http\Controllers\Web\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class BranchWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', Branch::class);
        return Inertia::render('panel/Branch/indexBranch');
    }
}
