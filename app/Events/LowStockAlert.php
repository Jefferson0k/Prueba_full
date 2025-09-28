<?php

namespace App\Events;

use App\Models\Inventory;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $inventory;

    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

    public function broadcastOn()
    {
        return new Channel('inventory.' . $this->inventory->branch_id);
    }

    public function broadcastAs()
    {
        return 'inventory.low.stock';
    }

    public function broadcastWith()
    {
        return [
            'product_name' => $this->inventory->product->name,
            'current_stock' => $this->inventory->current_stock,
            'min_stock' => $this->inventory->min_stock,
            'branch_name' => $this->inventory->branch->name,
        ];
    }
}
