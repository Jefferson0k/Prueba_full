<?php

namespace App\Http\Controllers\Web\PagoPersonal;

use App\Http\Controllers\Controller;
use App\Models\PagoPersonal;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class PagoPersonalWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', PagoPersonal::class);
        return Inertia::render('panel/PagoPersonal/indexPagoPersonal');
    }
}
