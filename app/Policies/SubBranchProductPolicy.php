<?php

namespace App\Policies;

use App\Models\SubBranchProduct;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubBranchProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('view_inventory');
    }

    public function view(User $user, SubBranchProduct $subBranchProduct)
    {
        return $user->can('view_inventory');
    }

    public function create(User $user)
    {
        return $user->can('create_inventory');
    }

    public function update(User $user, SubBranchProduct $subBranchProduct)
    {
        return $user->can('update_inventory');
    }

    public function delete(User $user, SubBranchProduct $subBranchProduct)
    {
        return $user->can('delete_inventory');
    }

    public function adjustStock(User $user, SubBranchProduct $subBranchProduct)
    {
        return $user->can('adjust_stock');
    }
}
