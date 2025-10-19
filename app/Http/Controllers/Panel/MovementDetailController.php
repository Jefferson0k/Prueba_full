<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovementDetail\StoreMovementDetailRequest;
use App\Http\Resources\MovementDetail\MovementDetailResource;
use App\Models\Movement;
use App\Models\MovementDetail;
use App\Pipelines\MovementDetail\OrderByLatest;
use App\Pipelines\MovementDetail\SearchMovementDetail;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Facades\DB;

class MovementDetailController extends Controller{
    use ApiResponse, AuthorizesRequests;
    public function store(StoreMovementDetailRequest $request){
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $movement = Movement::find($validated['movement_id']);
            if (!$movement) {
                return response()->json([
                    'message' => 'El movimiento no existe.'
                ], 404);
            }
            $totalPrice = $validated['unit_price'] * $validated['boxes'] * $validated['units_per_box'];
            $validated['total_price'] = $totalPrice;
            $movementDetail = MovementDetail::create($validated);
            DB::commit();
            return response()->json([
                'message' => 'Detalle de movimiento creado correctamente.',
                'data'    => $movementDetail,
                'movement_type' => $movement->movement_type
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el detalle del movimiento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function index(Request $request, string $movementId){
        $perPage = $request->input('per_page', 15);
        $query = app(Pipeline::class)
            ->send(MovementDetail::with(['movement', 'product'])->where('movement_id', $movementId))
            ->through([
                SearchMovementDetail::class,
                OrderByLatest::class,
            ])
            ->thenReturn();
        $movementDetails = $query->paginate($perPage);
        return MovementDetailResource::collection($movementDetails);
    }
}
