<?php

namespace App\Http\Controllers\Web\Piso;

use App\Http\Controllers\Controller;
use App\Models\Piso;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class PisoWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', Piso::class);
        return Inertia::render('panel/Piso/idexPiso');
    }
}
