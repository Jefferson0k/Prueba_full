<?php

namespace App\Http\Controllers\Web\Habitaciones;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
class habitacionesWeb extends Controller{
    public function view(): Response{
        return Inertia::render('panel/Habitaciones/indexHabitaciones');
    }
}
