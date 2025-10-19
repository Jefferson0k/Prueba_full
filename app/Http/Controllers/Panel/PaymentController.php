<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller{
    public function getUserCashRegister(){
        $user = Auth::user();
        $cashRegister = CashRegister::with(['subBranch', 'openedByUser'])
            ->where('opened_by', $user->id)
            ->where('status', 'abierta')
            ->where('is_active', true)
            ->first();
        if (!$cashRegister) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes una caja abierta'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $cashRegister
        ]);
    }
    public function getPaymentMethods(){
        $paymentMethods = PaymentMethod::active()->ordered()->get();
        return response()->json([
            'success' => true,
            'data' => $paymentMethods
        ]);
    }
}