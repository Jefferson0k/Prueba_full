<?php

namespace App\Http\Resources\SubBranch;

use Illuminate\Http\Resources\Json\JsonResource;

class SubBranchProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product->name ?? null,
            'category_name' => $this->product->category->name ?? null,
            'current_stock' => $this->current_stock,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'is_active' => $this->is_active,
            'packages_in_stock' => $this->packages_in_stock,
            'units_per_package' => $this->units_per_package,
            'is_low_stock' => $this->current_stock <= $this->min_stock,
            'is_out_of_stock' => $this->current_stock == 0,
            'purchase_price' => $this->product->purchase_price ?? null,
            'sale_price' => $this->product->sale_price ?? null,
        ];
    }
}
