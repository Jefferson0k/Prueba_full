<?php

namespace App\Policies;

use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SaleDetailPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('view_sales');
    }

    public function view(User $user, SaleDetail $saleDetail)
    {
        return $user->can('view_sales') && 
               ($user->can('view_all_sales') || $saleDetail->sale->created_by === $user->id);
    }

    public function create(User $user)
    {
        return $user->can('create_sales');
    }

    public function update(User $user, SaleDetail $saleDetail)
    {
        return $user->can('update_sales') && 
               $saleDetail->sale->status === 'pending' &&
               ($user->can('update_all_sales') || $saleDetail->sale->created_by === $user->id);
    }

    public function delete(User $user, SaleDetail $saleDetail)
    {
        return $user->can('delete_sales') && 
               $saleDetail->sale->status === 'pending' &&
               ($user->can('delete_all_sales') || $saleDetail->sale->created_by === $user->id);
    }
}
