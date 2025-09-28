<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\BookingPaymentProcessed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBookingPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function handle()
    {
        // Calcular total con consumos
        $totalConsumptions = $this->booking->consumptions()->sum('total_price');
        $totalAmount = $this->booking->total_amount + $totalConsumptions;
        
        // Verificar si hay pagos pendientes
        $totalPaid = $this->booking->payments()
            ->where('status', Payment::STATUS_COMPLETED)
            ->sum('amount_base_currency');

        $balance = $totalAmount - $totalPaid;

        if ($balance > 0) {
            // Crear notificación de pago pendiente
            $this->booking->client->notify(new BookingPaymentProcessed($this->booking, $balance));
        }

        // Actualizar el monto total de la reserva si hay consumos
        if ($totalConsumptions > 0) {
            $this->booking->total_amount = $totalAmount;
            $this->booking->save();
        }
    }
}
