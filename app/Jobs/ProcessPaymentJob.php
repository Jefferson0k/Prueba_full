<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Events\PaymentProcessed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment;
    protected $userId;

    public function __construct(Payment $payment, $userId = null)
    {
        $this->payment = $payment;
        $this->userId = $userId;
    }

    public function handle()
    {
        try {
            // Procesar el pago (aquí puedes integrar con pasarelas de pago)
            $this->payment->complete();
            
            // Emitir evento de pago procesado
            event(new PaymentProcessed($this->payment));
            
        } catch (\Exception $e) {
            // Marcar pago como fallido
            $this->payment->status = Payment::STATUS_CANCELLED;
            $this->payment->notes = 'Error al procesar: ' . $e->getMessage();
            $this->payment->save();
            
            throw $e;
        }
    }
}