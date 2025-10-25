<?php

namespace App\Http\Controllers\Panel;

use App\Filters\FilterByDateRange;
use App\Filters\FilterByPaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Resources\PaymentList\PaymentListResource;
use App\Http\Resources\PaymentMethodTotal\PaymentMethodTotalResource;
use App\Models\Booking;
use App\Models\Room;
use App\Models\PaymentMethod;
use App\Models\CashRegister;
use App\Models\Payment;
use App\Models\BookingConsumption;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            
            // Fechas por defecto (hoy)
            $startDate = $request->get('start_date', now()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            // Query base para pagos
            $query = Payment::with([
                'booking.customer',
                'booking.room',
                'paymentMethod'
            ])
            ->where('status', 'completed')
            ->latest('payment_date');

            // Aplicar pipeline de filtros
            $payments = app(Pipeline::class)
                ->send($query)
                ->through([
                    FilterByPaymentMethod::class,
                    FilterByDateRange::class,
                ])
                ->thenReturn();

            // Obtener totales por método de pago
            $totalsByMethod = $this->getTotalsByPaymentMethod($startDate, $endDate, $request->payment_method_id);

            // Paginación
            $paginator = $payments->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => PaymentListResource::collection($paginator),
                'totals' => $totalsByMethod,
                'filters' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'payment_method_id' => $request->payment_method_id,
                ],
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los pagos',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener totales agrupados por método de pago - CORREGIDO
     */
    private function getTotalsByPaymentMethod($startDate, $endDate, $specificMethodId = null)
    {
        // Primero obtener el total general como float
        $totalGeneralQuery = Payment::where('status', 'completed')
            ->whereDate('payment_date', '>=', $startDate)
            ->whereDate('payment_date', '<=', $endDate);

        if ($specificMethodId) {
            $totalGeneralQuery->where('payment_method_id', $specificMethodId);
        }

        $totalGeneral = (float) $totalGeneralQuery->sum('amount');

        // Ahora la consulta agrupada sin parámetros nombrados problemáticos
        $query = Payment::join('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->where('payments.status', 'completed')
            ->whereDate('payments.payment_date', '>=', $startDate)
            ->whereDate('payments.payment_date', '<=', $endDate);

        // Si se especifica un método de pago, filtrar por él
        if ($specificMethodId) {
            $query->where('payment_methods.id', $specificMethodId);
        }

        // Usar DB::raw para evitar problemas con parámetros
        $totals = $query->select([
                'payment_methods.id as payment_method_id',
                'payment_methods.name as payment_method_name',
                'payment_methods.code as payment_method_code',
                DB::raw('SUM(payments.amount) as total_amount'),
                DB::raw('COUNT(payments.id) as payment_count'),
                DB::raw("CASE 
                    WHEN {$totalGeneral} > 0 THEN (SUM(payments.amount) / {$totalGeneral}) * 100 
                    ELSE 0 
                END as percentage")
            ])
            ->groupBy('payment_methods.id', 'payment_methods.name', 'payment_methods.code')
            ->orderBy('total_amount', 'desc')
            ->get();

        return PaymentMethodTotalResource::collection($totals);
    }

    /**
     * Alternativa más segura usando Eloquent
     */
    private function getTotalsByPaymentMethodSafe($startDate, $endDate, $specificMethodId = null)
    {
        // Obtener todos los métodos de pago activos
        $paymentMethods = PaymentMethod::active()->get();
        
        $totals = [];

        foreach ($paymentMethods as $method) {
            $query = Payment::where('payment_method_id', $method->id)
                ->where('status', 'completed')
                ->whereDate('payment_date', '>=', $startDate)
                ->whereDate('payment_date', '<=', $endDate);

            // Si se especifica un método, solo procesar ese
            if ($specificMethodId && $method->id !== $specificMethodId) {
                continue;
            }

            $totalAmount = (float) $query->sum('amount');
            $paymentCount = $query->count();

            if ($totalAmount > 0) {
                $totals[] = [
                    'payment_method_id' => $method->id,
                    'payment_method_name' => $method->name,
                    'payment_method_code' => $method->code,
                    'total_amount' => $totalAmount,
                    'payment_count' => $paymentCount,
                    'percentage' => 0, // Se calculará después
                ];
            }
        }

        // Calcular total general y porcentajes
        $totalGeneral = array_sum(array_column($totals, 'total_amount'));
        
        foreach ($totals as &$total) {
            $total['percentage'] = $totalGeneral > 0 ? ($total['total_amount'] / $totalGeneral) * 100 : 0;
        }

        usort($totals, function($a, $b) {
            return $b['total_amount'] <=> $a['total_amount'];
        });

        return PaymentMethodTotalResource::collection(collect($totals));
    }
    public function summary(Request $request){
        try {
            $startDate = $request->get('start_date', now()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            $generalTotals = Payment::where('status', 'completed')
                ->whereDate('payment_date', '>=', $startDate)
                ->whereDate('payment_date', '<=', $endDate)
                ->selectRaw('
                    COUNT(*) as total_payments,
                    SUM(amount) as total_amount,
                    AVG(amount) as average_payment
                ')
                ->first();
            $totalsByMethod = $this->getTotalsByPaymentMethodSafe($startDate, $endDate);
            $paymentMethods = PaymentMethod::active()
                ->ordered()
                ->get(['id', 'name', 'code', 'is_active']);

            return response()->json([
                'success' => true,
                'data' => [
                    'general_totals' => [
                        'total_payments' => (int) $generalTotals->total_payments,
                        'total_amount' => (float) $generalTotals->total_amount,
                        'average_payment' => (float) $generalTotals->average_payment,
                    ],
                    'totals_by_method' => $totalsByMethod,
                    'payment_methods' => $paymentMethods,
                    'filters' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el resumen de pagos',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    public function store(StoreBookingRequest $request){
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            // Obtener sub_branch del usuario autenticado
            $subBranchId = Auth::user()->sub_branch_id;
            if (!$subBranchId) {
                throw new Exception('El usuario no tiene una sub_sucursal asignada');
            }

            // Generar código del booking
            $bookingCode = $this->generateBookingCode();

            // Calcular check_out y subtotales
            $checkIn = Carbon::parse($validated['check_in']);
            $checkOut = $checkIn->copy()->addHours($validated['total_hours']);

            $roomSubtotal = $validated['rate_per_hour'] * $validated['total_hours'];
            $productsSubtotal = 0;

            // Crear el booking
            $booking = Booking::create([
                'id' => Str::uuid(),
                'booking_code' => $bookingCode,
                'room_id' => $validated['room_id'],
                'customers_id' => $validated['customers_id'],
                'rate_type_id' => $validated['rate_type_id'],
                'currency_id' => $validated['currency_id'],
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'total_hours' => $validated['total_hours'],
                'rate_per_hour' => $validated['rate_per_hour'],
                'room_subtotal' => $roomSubtotal,
                'products_subtotal' => $productsSubtotal,
                'total_amount' => $roomSubtotal + $productsSubtotal,
                'voucher_type' => $validated['voucher_type'] ?? 'ticket',
                'status' => 'active',
                'created_by' => Auth::id(),
                'sub_branch_id' => $subBranchId,
            ]);

            // Procesar productos opcionales
            if (isset($validated['consumptions'])) {
                foreach ($validated['consumptions'] as $consumption) {
                    $totalPrice = $consumption['quantity'] * $consumption['unit_price'];

                    BookingConsumption::create([
                        'id' => Str::uuid(),
                        'booking_id' => $booking->id,
                        'product_id' => $consumption['product_id'],
                        'quantity' => $consumption['quantity'],
                        'unit_price' => $consumption['unit_price'],
                        'total_price' => $totalPrice,
                        'consumed_at' => now(),
                        'created_by' => Auth::id(),
                    ]);

                    $productsSubtotal += $totalPrice;
                }

                // Actualizar subtotal de productos y total general
                $booking->update([
                    'products_subtotal' => $productsSubtotal,
                    'total_amount' => $roomSubtotal + $productsSubtotal,
                ]);
            }

            // Procesar pagos
            $totalPaid = 0;
            foreach ($validated['payments'] as $paymentData) {
                if (isset($paymentData['cash_register_id'])) {
                    $cashRegister = CashRegister::find($paymentData['cash_register_id']);
                    if (!$cashRegister || !$cashRegister->isOpen()) {
                        throw new Exception('La caja especificada no está abierta');
                    }
                }

                $paymentMethod = PaymentMethod::find($paymentData['payment_method_id']);
                if ($paymentMethod && $paymentMethod->requires_reference && empty($paymentData['operation_number'])) {
                    throw new Exception("El método de pago {$paymentMethod->name} requiere un número de operación");
                }

                Payment::create([
                    'id' => Str::uuid(),
                    'payment_code' => $this->generatePaymentCode(),
                    'booking_id' => $booking->id,
                    'currency_id' => $validated['currency_id'],
                    'amount' => $paymentData['amount'],
                    'amount_base_currency' => $paymentData['amount'],
                    'payment_method' => $paymentMethod->code,
                    'payment_method_id' => $paymentData['payment_method_id'],
                    'cash_register_id' => $paymentData['cash_register_id'] ?? null,
                    'operation_number' => $paymentData['operation_number'] ?? null,
                    'payment_date' => now(),
                    'status' => 'completed',
                    'created_by' => Auth::id(),
                ]);

                $totalPaid += $paymentData['amount'];
            }

            // Actualizar monto pagado y estado de la habitación
            $booking->update(['paid_amount' => $totalPaid]);
            Room::where('id', $validated['room_id'])->update(['status' => 'occupied']);

            DB::commit();

            return response()->json([
                'message' => 'Booking creado exitosamente',
                'booking' => $booking->load([
                    'customer',
                    'room',
                    'subBranch',
                    'payments.paymentMethod',
                    'consumptions.product'
                ])
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al crear booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $booking = Booking::with([
            'customer', 
            'room', 
            'payments.paymentMethod',
            'payments.cashRegister',
            'consumptions.product'
        ])->findOrFail($id);
        
        return response()->json($booking);
    }

    public function addPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0',
            'cash_register_id' => 'nullable|exists:cash_registers,id',
            'operation_number' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $booking = Booking::findOrFail($id);
            
            // Validar que la caja esté abierta si se especifica
            if (isset($validated['cash_register_id'])) {
                $cashRegister = CashRegister::find($validated['cash_register_id']);
                if (!$cashRegister || !$cashRegister->isOpened()) {
                    throw new \Exception('La caja especificada no está abierta');
                }
            }

            // Validar número de operación si el método lo requiere
            $paymentMethod = PaymentMethod::find($validated['payment_method_id']);
            if ($paymentMethod && $paymentMethod->requires_reference && empty($validated['operation_number'])) {
                throw new \Exception("El método de pago {$paymentMethod->name} requiere un número de operación");
            }

            // Crear pago
            $payment = Payment::create([
                'id' => Str::uuid(),
                'payment_code' => $this->generatePaymentCode(),
                'booking_id' => $booking->id,
                'currency_id' => $booking->currency_id,
                'amount' => $validated['amount'],
                'amount_base_currency' => $validated['amount'], // Asumiendo misma moneda
                'payment_method' => $paymentMethod->code,
                'payment_method_id' => $validated['payment_method_id'],
                'cash_register_id' => $validated['cash_register_id'] ?? null,
                'operation_number' => $validated['operation_number'] ?? null,
                'payment_date' => now(),
                'status' => 'completed',
                'created_by' => Auth::id(),
            ]);

            // Actualizar monto pagado en booking
            $booking->increment('paid_amount', $validated['amount']);

            DB::commit();

            return response()->json([
                'message' => 'Pago agregado exitosamente',
                'payment' => $payment->load('paymentMethod', 'cashRegister')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al agregar pago',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addConsumption(Request $request, $id)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $booking = Booking::findOrFail($id);
            $product = Product::findOrFail($validated['product_id']);
            
            $totalPrice = $validated['quantity'] * $validated['unit_price'];
            
            // Crear consumo
            $consumption = BookingConsumption::create([
                'id' => Str::uuid(),
                'booking_id' => $booking->id,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'total_price' => $totalPrice,
                'consumed_at' => now(),
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Actualizar subtotales
            $booking->increment('products_subtotal', $totalPrice);
            $booking->increment('total_amount', $totalPrice);

            DB::commit();

            return response()->json([
                'message' => 'Consumo agregado exitosamente',
                'consumption' => $consumption->load('product')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al agregar consumo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function finishBooking(Request $request, $id)
    {
        $validated = $request->validate([
            'finish_type' => 'required|in:manual,automatic',
            'actual_hours' => 'nullable|integer|min:1',
            'actual_check_out' => 'nullable|date',
        ]);

        DB::beginTransaction();
        
        try {
            $booking = Booking::findOrFail($id);
            
            if ($booking->status !== 'active') {
                throw new \Exception('Solo se pueden finalizar bookings activos');
            }

            $booking->update([
                'status' => 'finished',
                'finish_type' => $validated['finish_type'],
                'actual_hours' => $validated['actual_hours'] ?? $booking->total_hours,
                'actual_check_out' => $validated['actual_check_out'] ?? now(),
                'finished_by' => Auth::id(),
            ]);

            // Liberar la habitación
            Room::where('id', $booking->room_id)->update(['status' => 'disponible']);

            DB::commit();

            return response()->json([
                'message' => 'Booking finalizado exitosamente',
                'booking' => $booking->load(['customer', 'room'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al finalizar booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cancelBooking(Request $request, $id)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        
        try {
            $booking = Booking::findOrFail($id);
            
            if ($booking->status === 'cancelled') {
                throw new \Exception('El booking ya está cancelado');
            }

            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_by' => Auth::id(),
            ]);

            // Liberar la habitación si estaba activa
            if ($booking->status === 'active') {
                Room::where('id', $booking->room_id)->update(['status' => 'disponible']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Booking cancelado exitosamente',
                'booking' => $booking
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al cancelar booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBookingPayments($id)
    {
        $booking = Booking::findOrFail($id);
        $payments = $booking->payments()
            ->with(['paymentMethod', 'cashRegister'])
            ->latest()
            ->get();
            
        return response()->json($payments);
    }

    public function getBookingConsumptions($id)
    {
        $booking = Booking::findOrFail($id);
        $consumptions = $booking->consumptions()
            ->with('product')
            ->latest()
            ->get();
            
        return response()->json($consumptions);
    }

    private function generateBookingCode()
    {
        $date = now()->format('Y');
        $lastBooking = Booking::whereYear('created_at', now()->year)
            ->orderBy('created_at', 'desc')
            ->first();

        $sequence = $lastBooking ? 
            (int) substr($lastBooking->booking_code, -4) + 1 : 1;

        return 'HAB-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    private function generatePaymentCode()
    {
        $date = now()->format('Ymd');
        $lastPayment = Payment::whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->first();

        $sequence = $lastPayment ? 
            (int) substr($lastPayment->payment_code, -4) + 1 : 1;

        return 'PAY-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}