<?php

namespace App\Http\Controllers\Web\Kardex;

use App\Http\Controllers\Controller;
use App\Models\Kardex;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class kardexWeb extends Controller{
    public function view(): Response{
        Gate::authorize('view kardex producto', Kardex::class);
        return Inertia::render('panel/Kardex/indexKardex');
    }
    public function viewGeneral(): Response{
        Gate::authorize('view kardex general', Kardex::class);
        return Inertia::render('panel/Kardex/indexKardexGeneral');
    }
    public function viewValorizado(): Response{
        Gate::authorize('view kardex valorizado', Kardex::class);
        return Inertia::render('panel/Kardex/indexKardexValorizado');
    }
}
