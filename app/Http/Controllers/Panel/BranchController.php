<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Http\Resources\Branch\BranchResource;
use App\Models\Branch;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Throwable;

class BranchController extends Controller{
    public function index(Request $request){
        try {
            Gate::authorize('viewAny', Branch::class);
            $branches = Branch::withActiveRooms()->get();
            return response()->json([
                'total' => $branches->count(),
                'data' => BranchResource::collection($branches),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'No tienes permiso para ver sucursales.'], 403);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Error al listar sucursales.'], 500);
        }
    }
    public function store(StoreBranchRequest $request){
        try {
            Gate::authorize('create', Branch::class);
            $data = $request->validated();
            $data['created_by'] = Auth::id();
            $branch = Branch::create($data);
            return response()->json([
                'message' => 'Sucursal creada correctamente.',
                'data' => new BranchResource($branch)
            ], 201);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'No tienes permiso para crear una sucursal.'], 403);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Error al crear la sucursal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id){
        try {
            $branch = Branch::findOrFail($id);
            Gate::authorize('view', $branch);
            return new BranchResource($branch);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Sucursal no encontrada.'], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'No tienes permiso para ver esta sucursal.'], 403);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Error al mostrar la sucursal.'], 500);
        }
    }
    public function update(UpdateBranchRequest $request, Branch $branch) {
        try {
            Gate::authorize('update', $branch);
            $data = $request->validated();
            $data['updated_by'] = Auth::id();
            $branch->update($data);
            
            return response()->json([
                'message' => 'Sucursal actualizada correctamente.',
                'data' => new BranchResource($branch)
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'No tienes permiso para editar esta sucursal.'], 403);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Error al actualizar la sucursal.'], 500);
        }
    }
    public function delete($id){
        try {
            $branch = Branch::findOrFail($id);
            Gate::authorize('delete', $branch);
            $branch->deleted_by = Auth::id();
            $branch->save();
            $branch->delete();
            return response()->json(['message' => 'Sucursal eliminada correctamente.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Sucursal no encontrada.'], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'No tienes permiso para eliminar esta sucursal.'], 403);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Error al eliminar la sucursal.'], 500);
        }
    }
}
