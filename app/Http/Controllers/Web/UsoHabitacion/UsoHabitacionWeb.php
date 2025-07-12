<?php

namespace App\Http\Controllers\Web\UsoHabitacion;

use App\Http\Controllers\Controller;
use App\Models\UsoHabitacion;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class UsoHabitacionWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', UsoHabitacion::class);
        return Inertia::render('panel/UsoHabitacion/indexUsoHabitacion');
    }
}
