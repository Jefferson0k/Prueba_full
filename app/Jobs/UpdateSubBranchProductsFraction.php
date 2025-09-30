<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateSubBranchProductsFraction implements ShouldQueue{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected Product $product;
    public function __construct(Product $product){
        $this->product = $product;
    }
    public function handle(): void{
        foreach ($this->product->subBranchProducts as $subBranchProduct) {
            $subBranchProduct->update([
                'is_fractionable'   => $this->product->is_fractionable,
                'units_per_package' => $this->product->getFractionUnits(),
            ]);
        }
    }
}
