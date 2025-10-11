<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovementDetail\StoreMovementDetailRequest;
use App\Http\Resources\MovementDetail\MovementDetailResource;
use App\Models\MovementDetail;
use App\Pipelines\MovementDetail\OrderByLatest;
use App\Pipelines\MovementDetail\SearchMovementDetail;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;

class MovementDetailController extends Controller{
    use ApiResponse, AuthorizesRequests;
    public function store(StoreMovementDetailRequest $request){
        $validated = $request->validated();
        $movementDetail = MovementDetail::create($validated);
        return response()->json([
            'message' => 'Detalle de movimiento creado correctamente.',
            'data'    => $movementDetail
        ], 201);
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
