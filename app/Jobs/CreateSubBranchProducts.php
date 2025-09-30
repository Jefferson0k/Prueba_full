<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\SubBranch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateSubBranchProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected SubBranch $subBranch;

    public function __construct(SubBranch $subBranch)
    {
        $this->subBranch = $subBranch;
    }

    public function handle(): void
    {
        $allProducts = Product::all();

        foreach ($allProducts as $product) {
            $this->subBranch->subBranchProducts()->create([
                'product_id'        => $product->id,
                'packages_in_stock' => 0,
                'units_per_package' => $product->is_fractionable ? $product->fraction_units : 0,
                'current_stock'     => 0,
                'min_stock'         => 0,
                'max_stock'         => 100,
                'is_fractionable'   => $product->is_fractionable,
                'is_active'         => $product->is_active,
                'created_by'        => $this->subBranch->created_by,
            ]);
        }
    }
}
