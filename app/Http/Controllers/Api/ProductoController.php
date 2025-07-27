<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Productos\StoreProductoRequest;
use App\Http\Requests\Productos\UpdateProductoRequest;
use App\Http\Resources\Producto\ProductoResource;
use App\Models\Producto;
use App\Pipelines\FilterByCategory;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterByState;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;

class ProductoController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Producto::class);
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $state = $request->input('state');
        $category = $request->input('category');

        $query = app(Pipeline::class)
            ->send(Producto::query()->with(['categoria']))
            ->through([
                new FilterByName($search),
                new FilterByState($state),
                new FilterByCategory($category),
            ])
            ->thenReturn();

        return ProductoResource::collection($query->paginate($perPage));
    }
    public function store(StoreProductoRequest $request){
        Gate::authorize('create', Producto::class);
        $validated = $request->validated();
        $product = Producto::create($validated);
        return response()->json([
            'state' => true,
            'message' => 'Producto registrado correctamente.',
            'product' => $product
        ]);
    }
    public function show(Producto $product){
        Gate::authorize('view', $product);
        return response()->json([
            'state' => true,
            'message' => 'Producto encontrado',
            'product' => new ProductoResource($product),
        ], 200);
    }
    public function update(UpdateProductoRequest $request, Producto $product){
        Gate::authorize('update', $product);
        $validated = $request->validated();
        $product->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Producto actualizado correctamente.',
            'product' => $product->refresh()
        ]);
    }
    public function destroy(Producto $product){
        Gate::authorize('delete', $product);
        $product->delete();
        return response()->json([
            'state' => true,
            'message' => 'Producto eliminado correctamente',
        ]);
    }
}
