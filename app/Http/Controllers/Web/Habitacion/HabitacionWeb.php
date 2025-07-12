<?php

namespace App\Http\Controllers\Web\Habitacion;

use App\Http\Controllers\Controller;
use App\Models\Habitacion;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class HabitacionWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', Habitacion::class);
        return Inertia::render('panel/Habitacion/indexHabitacion');
    }
}
