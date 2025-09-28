<?php

namespace App\Http\Controllers\Web\Producto;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
class ProductoWeb extends Controller{
    public function view(): Response{
        Gate::authorize('viewAny', Product::class);
        return Inertia::render('panel/Producto/indexProducto');
    }
}
