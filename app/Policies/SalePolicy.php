<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('view_sales');
    }

    public function view(User $user, Sale $sale)
    {
        return $user->can('view_sales') && 
               ($user->can('view_all_sales') || $sale->created_by === $user->id);
    }

    public function create(User $user)
    {
        return $user->can('create_sales');
    }

    public function update(User $user, Sale $sale)
    {
        return $user->can('update_sales') && 
               $sale->status === 'pending' &&
               ($user->can('update_all_sales') || $sale->created_by === $user->id);
    }

    public function delete(User $user, Sale $sale)
    {
        return $user->can('delete_sales') && 
               $sale->canBeCancelled() &&
               ($user->can('delete_all_sales') || $sale->created_by === $user->id);
    }

    public function cancel(User $user, Sale $sale)
    {
        return $user->can('cancel_sales') && 
               $sale->canBeCancelled() &&
               ($user->can('cancel_all_sales') || $sale->created_by === $user->id);
    }
}
