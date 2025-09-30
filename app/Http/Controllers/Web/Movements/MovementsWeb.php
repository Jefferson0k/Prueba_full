<?php

namespace App\Http\Controllers\Web\Movements;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class MovementsWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', Movement::class);
        return Inertia::render('panel/Movement/indexMovement');
    }
}
