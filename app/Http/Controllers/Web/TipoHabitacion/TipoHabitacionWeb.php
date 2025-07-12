<?php

namespace App\Http\Controllers\Web\TipoHabitacion;

use App\Http\Controllers\Controller;
use App\Models\TipoHabitacion;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class TipoHabitacionWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', TipoHabitacion::class);
        return Inertia::render('panel/TipoHabitacion/indexTipoHabitacion');
    }
}
