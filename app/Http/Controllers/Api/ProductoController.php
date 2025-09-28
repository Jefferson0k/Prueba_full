<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Resources\Producto\ProductoResource;
use App\Models\Product;
use App\Models\SubBranch;
use App\Models\SubBranchProduct;
use App\Pipelines\FilterByCategory;
use App\Pipelines\FilterByName;
use App\Pipelines\FilterByState;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Throwable;

class ProductoController extends Controller{
    public function index(Request $request){
        Gate::authorize('viewAny', Product::class);
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $state = $request->input('state');
        $category = $request->input('category');
        $query = app(Pipeline::class)
            ->send(Product::query()->with('category'))
            ->through([
                new FilterByName($search),
                new FilterByState($state),
                new FilterByCategory($category),
            ])
            ->thenReturn();
        return ProductoResource::collection($query->paginate($perPage));
    }
    public function store(StoreProductRequest $request){
        try {
            Gate::authorize('create', Product::class);
            DB::beginTransaction();
            $validated = $request->validated();
            $validated['created_by'] = Auth::id();
            $product = Product::create($validated);
            $subBranches = SubBranch::all();
            $subBranchProductsData = [];
            foreach ($subBranches as $subBranch) {
                $subBranchProductsData[] = [
                    'id' => (string) Str::uuid(),
                    'sub_branch_id' => $subBranch->id,
                    'product_id' => $product->id,
                    'current_stock' => 0,
                    'min_stock' => 0,
                    'max_stock' => 100,
                    'custom_sale_price' => null,
                    'is_active' => $product->is_active,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($subBranchProductsData)) {
                SubBranchProduct::insert($subBranchProductsData);
            }
            DB::commit();
            return response()->json([
                'state' => true,
                'message' => 'Producto registrado exitosamente y asignado a ' . count($subBranches) . ' sub-sucursales.',
                'product' => $product
            ]);
            
        } catch (AuthorizationException $e) {
            DB::rollBack();
            return response()->json([
                'state' => false,
                'message' => 'No tienes permiso para crear un producto.'
            ], 403);
            
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'state' => false,
                'message' => 'Error al crear el producto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show(Product $product){
        Gate::authorize('view', $product);
        return response()->json([
            'state' => true,
            'message' => 'Producto encontrado',
            'product' => new ProductoResource($product),
        ], 200);
    }
    public function update(UpdateProductRequest $request, Product $product){
        Gate::authorize('update', $product);
        $validated = $request->validated();
        $validated['updated_by'] = Auth::id();
        $product->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Product updated successfully.',
            'product' => $product->refresh()
        ]);
    }
    public function destroy(Product $product){
        Gate::authorize('delete', $product);
        $product->deleted_by = Auth::id();
        $product->save();
        $product->delete();
        return response()->json([
            'state' => true,
            'message' => 'Producto eliminado correctamente',
        ]);
    }
}
