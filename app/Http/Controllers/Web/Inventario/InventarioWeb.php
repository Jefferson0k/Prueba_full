<?php

namespace App\Http\Controllers\Web\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class InventarioWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', Inventory::class);
        return Inertia::render('panel/Inventario/indexInventario');
    }
}
