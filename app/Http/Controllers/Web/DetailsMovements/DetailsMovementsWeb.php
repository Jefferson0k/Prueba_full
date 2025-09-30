<?php

namespace App\Http\Controllers\Web\DetailsMovements;

use App\Http\Controllers\Controller;
use App\Http\Resources\Movement\MovementResource;
use App\Models\Movement;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DetailsMovementsWeb extends Controller{
    public function view($movement_id){
        Gate::authorize('viewAny', Movement::class);
        $movement = Movement::with(['provider', 'subBranch'])
            ->findOrFail($movement_id);
        return Inertia::render('panel/DetailisMovement/indexDetailisMovement', [
            'movement' => new MovementResource($movement),
        ]);
    }
}
