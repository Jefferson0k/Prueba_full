<?php

namespace App\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Branch;
use App\Events\InventoryUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateInventoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $branchId;
    protected $productId;
    protected $quantity;
    protected $type;
    protected $reason;
    protected $userId;

    public function __construct($branchId, $productId, $quantity, $type, $reason = null, $userId = null)
    {
        $this->branchId = $branchId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->type = $type;
        $this->reason = $reason;
        $this->userId = $userId;
    }

    public function handle()
    {
        $inventory = Inventory::where('branch_id', $this->branchId)
            ->where('product_id', $this->productId)
            ->first();

        if (!$inventory) {
            $inventory = Inventory::create([
                'branch_id' => $this->branchId,
                'product_id' => $this->productId,
                'current_stock' => 0,
                'created_by' => $this->userId,
            ]);
        }

        $inventory->updateStock($this->quantity, $this->type, $this->reason, $this->userId);
        
        event(new InventoryUpdated($inventory, $this->type, $this->quantity));
    }
}
