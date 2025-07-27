<?php

namespace App\Http\Controllers\Web\Categoria;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class CategoriaWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', Categoria::class);
        return Inertia::render('panel/Categoria/indexCategoria');
    }
}
