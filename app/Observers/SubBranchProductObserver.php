<?php

namespace App\Observers;

use App\Models\SubBranchProduct;

class SubBranchProductObserver{
    public function creating(SubBranchProduct $subBranchProduct): void{
        $subBranchProduct->current_stock = $subBranchProduct->packages_in_stock * $subBranchProduct->units_per_package;
    }
    public function updating(SubBranchProduct $subBranchProduct): void{
        if ($subBranchProduct->isDirty(['packages_in_stock', 'units_per_package'])) {
            $subBranchProduct->current_stock = $subBranchProduct->packages_in_stock * $subBranchProduct->units_per_package;
        }
    }
}
