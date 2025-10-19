<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\BookingConsumption;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingConsumptionController extends Controller{
    // En BookingConsumptionController.php
    public function index(Request $request){
        try {
            $query = BookingConsumption::with(['product', 'booking']);
            
            // Filtros
            if ($request->has('month') && $request->has('year')) {
                $startDate = Carbon::create($request->year, $request->month, 1)->startOfMonth();
                $endDate = Carbon::create($request->year, $request->month, 1)->endOfMonth();
                $query->whereBetween('consumed_at', [$startDate, $endDate]);
            }
            
            if ($request->has('with_product')) {
                $query->with('product');
            }
            
            if ($request->has('with_booking')) {
                $query->with('booking');
            }
            
            $perPage = $request->input('per_page', 15);
            $consumos = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $consumos->items(),
                'meta' => [
                    'current_page' => $consumos->currentPage(),
                    'per_page' => $consumos->perPage(),
                    'total' => $consumos->total(),
                    'last_page' => $consumos->lastPage(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cargando consumos: ' . $e->getMessage()
            ], 500);
        }
    }
}
