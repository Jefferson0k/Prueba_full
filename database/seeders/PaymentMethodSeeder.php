<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        $paymentMethods = [
            [
                'name' => 'Efectivo',
                'code' => 'cash',
                'requires_reference' => false,
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Tarjeta Débito',
                'code' => 'debit_card',
                'requires_reference' => true,
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Tarjeta Crédito',
                'code' => 'credit_card',
                'requires_reference' => true,
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Yape',
                'code' => 'yape',
                'requires_reference' => true,
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Plin',
                'code' => 'plin',
                'requires_reference' => true,
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Transferencia',
                'code' => 'transfer',
                'requires_reference' => true,
                'is_active' => true,
                'sort_order' => 6
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}