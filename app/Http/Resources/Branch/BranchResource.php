<?php

namespace App\Http\Resources\Branch;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource{
    public function toArray($request): array{
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_active' => $this->is_active,
            'creacion' => Carbon::parse($this->created_at)
                ->locale('es')
                ->translatedFormat('l d \d\e F \d\e Y h:i:s A'),
            'update' => Carbon::parse($this->updated_at)
                ->locale('es')
                ->translatedFormat('l d \d\e F \d\e Y h:i:s A'),
        ];
    }
}
