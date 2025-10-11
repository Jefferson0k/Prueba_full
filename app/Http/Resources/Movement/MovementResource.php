<?php
namespace App\Http\Resources\Movement;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class MovementResource extends JsonResource{
    public function toArray($request): array{
        $totalDetails = $this->details->sum('total_price');
        if ($this->includes_igv) {
            $total = $totalDetails;
            $subtotal = $total / 1.18;
            $igv = $total - $subtotal;
        } else {
            $subtotal = $totalDetails;
            $igv = $subtotal * 0.18;
            $total = $subtotal + $igv;
        }

        return [
            'id' => $this->id,
            'code' => $this->code,
            'date' => $this->date ? Carbon::parse($this->date)->format('d-m-Y') : null,
            'credit_date' => $this->credit_date ? Carbon::parse($this->credit_date)->format('d-m-Y') : null,
            'provider' => [
                'id' => $this->provider->id ?? null,
                'ruc' => $this->provider->ruc ?? null,
                'razon_social' => $this->provider->razon_social ?? null,
            ],
            'sub_branch' => [
                'id' => $this->subBranch->id ?? null,
                'name' => $this->subBranch->name ?? null,
                'code' => $this->subBranch->code ?? null,
            ],
            'payment_type' => $this->payment_type,
            'includes_igv' => $this->includes_igv,
            'voucher_type' => $this->voucher_type,
            'subtotal' => round($subtotal, 2),
            'igv' => round($igv, 2),
            'total' => round($total, 2),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at?->format('d-m-Y H:i:s A'),
            'updated_at' => $this->updated_at?->format('d-m-Y H:i:s A'),
        ];
    }
}