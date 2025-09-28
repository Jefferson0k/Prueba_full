<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view inventory');
    }

    public function view(User $user, Inventory $inventory)
    {
        return $user->hasPermissionTo('view inventory') ||
               $user->branches()->where('branches.id', $inventory->branch_id)->exists();
    }

    public function update(User $user, Inventory $inventory)
    {
        return $user->hasPermissionTo('update inventory') ||
               ($user->hasPermissionTo('update branch inventory') && 
                $user->branches()->where('branches.id', $inventory->branch_id)->exists());
    }

    public function adjustStock(User $user, Inventory $inventory)
    {
        return $user->hasPermissionTo('adjust inventory stock') ||
               $user->branches()->where('branches.id', $inventory->branch_id)->exists();
    }

    public function transfer(User $user, Inventory $inventory)
    {
        return $user->hasPermissionTo('transfer inventory');
    }
}
