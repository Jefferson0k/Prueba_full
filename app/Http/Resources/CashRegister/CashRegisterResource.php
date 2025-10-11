<?php

namespace App\Http\Resources\CashRegister;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashRegisterResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'opening_amount' => $this->opening_amount,
            'closing_amount' => $this->closing_amount,
            'opened_at' => $this->opened_at?->toDateTimeString(),
            'closed_at' => $this->closed_at?->toDateTimeString(),
            'sub_branch' => [
                'id' => $this->subBranch->id,
                'name' => $this->subBranch->name,
            ],
            'opened_by_user' => $this->whenLoaded('openedByUser', function () {
                return [
                    'id' => $this->openedByUser->id,
                    'name' => $this->openedByUser->name,
                    'email' => $this->openedByUser->email,
                ];
            }),
            'closed_by_user' => $this->whenLoaded('closedByUser', function () {
                return [
                    'id' => $this->closedByUser->id,
                    'name' => $this->closedByUser->name,
                    'email' => $this->closedByUser->email,
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            
            // Métodos de estado (opcional)
            'is_open' => $this->isOpen(),
            'is_closed' => $this->isClosed(),
            'is_blocked' => $this->isBlocked(),
        ];
    }
}