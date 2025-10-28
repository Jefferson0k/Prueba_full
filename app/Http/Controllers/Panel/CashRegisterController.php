<?php

namespace App\Http\Controllers\Panel;

use App\Http\Resources\CashRegister\CashRegisterResource;
use App\Models\CashRegister;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CashRegisterController extends Controller{
    public function index(Request $request){
        try {
            Gate::authorize('viewAny', CashRegister::class);
            $user = Auth::user();
            $query = CashRegister::with(['subBranch', 'openedByUser', 'closedByUser'])
                ->where('sub_branch_id', $user->sub_branch_id);
            if ($request->has('status') && in_array($request->status, ['abierta', 'cerrada', 'bloqueada'])) {
                $query->where('status', $request->status);
            }
            if ($request->has('is_active')) {
                $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
            }
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            $sortField = $request->get('sort_field', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $allowedSortFields = ['name', 'status', 'created_at', 'opened_at'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortOrder);
            }
            $perPage = $request->get('per_page', 15);
            $cashRegisters = $query->paginate($perPage);
            return response()->json([
                'success' => true,
                'data' => CashRegisterResource::collection($cashRegisters),
                'meta' => [
                    'current_page' => $cashRegisters->currentPage(),
                    'per_page' => $cashRegisters->perPage(),
                    'total' => $cashRegisters->total(),
                    'last_page' => $cashRegisters->lastPage(),
                ]
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver las cajas registradoras'
            ], 403);
        } catch (Exception $e) {
            Log::error('Error listing cash registers: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la lista de cajas registradoras'
            ], 500);
        }
    }
    public function store(Request $request){
        try {
            Gate::authorize('create', CashRegister::class);
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            $user = Auth::user();
            $cashRegister = CashRegister::create([
                'name' => $request->name,
                'description' => $request->description,
                'sub_branch_id' => $user->sub_branch_id,
                'status' => 'cerrada',
                'is_active' => true,
                'initial_balance' => 0,
                'current_balance' => 0
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Caja registradora creada exitosamente',
                'data' => new CashRegisterResource($cashRegister)
            ], 201);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear cajas registradoras'
            ], 403);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Error creating cash register: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la caja registradora'
            ], 500);
        }
    }
    public function show(string $id){
        try {
            $cashRegister = CashRegister::with(['subBranch', 'openedByUser', 'closedByUser'])
                ->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => new CashRegisterResource($cashRegister)
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver esta caja'
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Caja registradora no encontrada'
            ], 404);
        } catch (Exception $e) {
            Log::error('Error showing cash register: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la caja registradora'
            ], 500);
        }
    }
    public function createMultiple(Request $request){
        try {
            Gate::authorize('create', CashRegister::class);
            $request->validate([
                'quantity' => 'required|integer|min:1|max:20'
            ]);
            $quantity = $request->quantity;
            $cashRegisters = CashRegister::createMultipleCashRegisters($quantity);
            
            if ($cashRegisters) {
                return response()->json([
                    'success' => true,
                    'message' => "Se crearon {$quantity} cajas exitosamente",
                    'data' => CashRegisterResource::collection($cashRegisters)
                ], 201);
            }
            return response()->json([
                'success' => false,
                'message' => 'Error al crear las cajas'
            ], 500);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear cajas registradoras'
            ], 403);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Error creating multiple cash registers: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear las cajas registradoras'
            ], 500);
        }
    }
    public function open(Request $request, $id){
        try {
            $cashRegister = CashRegister::findOrFail($id);
            Gate::authorize('open', $cashRegister);
            $request->validate([
                'opening_amount' => 'required|numeric|min:0',
            ]);
            $result = $cashRegister->openCashRegister($request->opening_amount);
            if (!$result['success']) {
                return response()->json($result, 400);
            }
            return response()->json($result, 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para abrir esta caja'
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Caja registradora no encontrada'
            ], 404);
        } catch (Exception $e) {
            Log::error('Error opening cash register: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al abrir la caja registradora'
            ], 500);
        }
    }
}