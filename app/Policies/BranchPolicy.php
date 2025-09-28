<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BranchPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view branches');
    }

    public function view(User $user, Branch $branch)
    {
        return $user->hasPermissionTo('view branches') || 
               $user->branches()->where('branches.id', $branch->id)->exists();
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create branches');
    }

    public function update(User $user, Branch $branch)
    {
        return $user->hasPermissionTo('update branches') ||
               ($user->hasPermissionTo('update own branches') && 
                $user->branches()->where('branches.id', $branch->id)->exists());
    }

    public function delete(User $user, Branch $branch)
    {
        return $user->hasPermissionTo('delete branches');
    }

    public function manage(User $user, Branch $branch)
    {
        return $user->hasPermissionTo('manage branches') ||
               $user->branches()->where('branches.id', $branch->id)->exists();
    }
}
