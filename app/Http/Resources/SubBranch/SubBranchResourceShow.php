<?php

namespace App\Http\Resources\SubBranch;

use Illuminate\Http\Resources\Json\JsonResource;

class SubBranchResourceShow extends JsonResource{
    public function toArray($request): array{
        return [
            'id'         => $this->id,
            'branch_id'  => $this->branch_id,
            'name'       => $this->name,
            'code'       => $this->code,
            'address'    => $this->address,
            'phone'      => $this->phone,
            'is_active'  => $this->is_active,
        ];
    }
}