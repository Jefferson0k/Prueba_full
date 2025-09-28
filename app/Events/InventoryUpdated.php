<?php

namespace App\Events;

use App\Models\Inventory;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $inventory;
    public $movementType;
    public $quantity;

    public function __construct(Inventory $inventory, $movementType, $quantity)
    {
        $this->inventory = $inventory;
        $this->movementType = $movementType;
        $this->quantity = $quantity;
    }

    public function broadcastOn()
    {
        return new Channel('inventory.' . $this->inventory->branch_id);
    }

    public function broadcastAs()
    {
        return 'inventory.updated';
    }
}