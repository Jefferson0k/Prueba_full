<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubBranch\StoreSubBranchRequest;
use App\Http\Resources\SubBranch\SubBranchResource;
use App\Jobs\CreateSubBranchProducts;
use App\Models\SubBranch;
use App\Pipelines\SubBranch\OrderAlphabetically;
use App\Pipelines\SubBranch\PrioritizeUserSubBranch;
use App\Pipelines\SubBranch\SearchSubBranch;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Throwable;

class SubBranchController extends Controller{
    public function index(Request $request, $branchId){
        try {
            Gate::authorize('viewAny', SubBranch::class);
            $query = SubBranch::where('branch_id', $branchId)
                            ->with(['branch']);
            if ($request->boolean('with_counts')) {
                $query->withCount([
                    'floors',
                    'floors as rooms_count' => function ($floorQuery) {
                        $floorQuery->withCount(['rooms as active_rooms_count' => function ($roomQuery) {
                            $roomQuery->where('is_active', true);
                        }]);
                    },
                    'floors as available_rooms_count' => function ($floorQuery) {
                        $floorQuery->withCount(['rooms as available_rooms_count' => function ($roomQuery) {
                            $roomQuery->where('is_active', true)
                                    ->where('status', 'available');
                        }]);
                    },
                ]);
            }
            $subBranches = $query->orderBy('created_at', 'desc')->get();
            return response()->json([
                'total'   => $subBranches->count(),
                'data'    => SubBranchResource::collection($subBranches),
                'success' => true,
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'No tienes permiso para ver las sub-sucursales.'
            ], 403);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Error al listar las sub-sucursales.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function store(StoreSubBranchRequest $request){
        try {
            Gate::authorize('create', SubBranch::class);
            DB::beginTransaction();
            $data = $request->validated();
            $data['created_by'] = Auth::id();
            $subBranch = SubBranch::create($data);
            CreateSubBranchProducts::dispatchSync($subBranch);
            DB::commit();
            $subBranch->load('subBranchProducts.product');
            return response()->json([
                'state'   => true,
                'message' => 'Sub-sucursal creada correctamente. La asignación de productos se está procesando en segundo plano.',
                'data'    => new SubBranchResource($subBranch)
            ], 201);
        } catch (AuthorizationException $e) {
            DB::rollBack();
            return response()->json([
                'state'   => false,
                'message' => 'No tienes permiso para crear una sub-sucursal.'
            ], 403);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'state'   => false,
                'message' => 'Error al crear la sub-sucursal.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request){
        $user = Auth::user();
        $userSubBranchId = $user?->sub_branch_id;
        $query = app(Pipeline::class)
            ->send(SubBranch::query())
            ->through([SearchSubBranch::class])
            ->thenReturn();
        $query->orderBy('name', 'asc');
        $subBranches = $query->get();
        if ($userSubBranchId) {
            $subBranches = $subBranches->sortByDesc(function ($branch) use ($userSubBranchId) {
                return $branch->id === $userSubBranchId ? 1 : 0;
            })->values();
        }
        return SubBranchResource::collection($subBranches);
    }
    public function show(){}
    public function delete(){}
    public function update(){}
}
