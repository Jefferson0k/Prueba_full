<?php

namespace App\Http\Controllers\Web\Horario;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class HorarioWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', Horario::class);
        return Inertia::render('panel/Horario/indexHorario');
    }
}
