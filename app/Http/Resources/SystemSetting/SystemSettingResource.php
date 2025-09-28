<?php

namespace App\Http\Resources\SystemSetting;

use Illuminate\Http\Resources\Json\JsonResource;

class SystemSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'key'       => $this->key,
            'value'     => $this->value,
            'type'      => $this->type,
            'group'     => $this->group,
            'description'=> $this->description,
            'is_public' => $this->is_public,
            'is_active' => $this->is_active,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }
}
