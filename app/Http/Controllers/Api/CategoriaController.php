<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Categoria\StoreCategoriaRequest;
use App\Http\Requests\Categoria\UpdateCategoriaRequest;
use App\Http\Resources\Categoria\CategoriaResource;
use App\Models\Categoria;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterByState;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;

class CategoriaController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Categoria::class);
        $perPage = $request->input('per_page', 15);
        $categories = app(Pipeline::class)
            ->send(Categoria::query())
            ->through([
                new FilterByName($request->input('search')),
                new FilterByState($request->input('state')),
            ])
            ->thenReturn()
            ->paginate($perPage);
        return CategoriaResource::collection($categories);
    }
    public function store(StoreCategoriaRequest $request){
        Gate::authorize('create', Categoria::class);
        $validated = $request->validated();
        $exists = Categoria::whereRaw('LOWER(nombre) = ?', [$validated['nombre']])->exists();
        if ($exists) {
            return response()->json([
                'errors' => ['nombre' => ['Este nombre ya está registrado.']]
            ], 422);
        }
        $category = Categoria::create($validated);
        return response()->json([
            'state' => true,
            'message' => 'Almacén registrado correctamente.',
            'category' => $category
        ]);
    }
    public function show(Categoria $category){
        Gate::authorize('view', $category);
        return response()->json([
            'state' => true,
            'message' => 'Categoría encontrada',
            'category' => new CategoriaResource($category),
        ], 200);
    }
    public function update(UpdateCategoriaRequest $request, Categoria $category){
        Gate::authorize('update', $category);
        $validated = $request->validated();
        $nameExists = Categoria::whereRaw('LOWER(nombre) = ?', [strtolower($validated['nombre'])])
            ->where('id', '!=', $category->id)
            ->exists();
        if ($nameExists) {
            return response()->json([
                'errors' => ['nombre' => ['Este nombre ya está registrado.']]
            ], 422);
        }
        $category->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Tipo de cliente actualizado de manera correcta',
            'category' => new CategoriaResource($category->refresh()),
        ]);
    }
    public function destroy(Categoria $category){
        Gate::authorize('delete', $category);
        $category->delete();
        return response()->json([
            'state' => true,
            'message' => 'Categoría eliminada correctamente',
        ]);
    }
}
