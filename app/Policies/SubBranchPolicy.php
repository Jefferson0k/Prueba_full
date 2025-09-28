<?php

namespace App\Policies;

use App\Models\SubBranch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubBranchPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view sub branches');
    }

    public function view(User $user, SubBranch $subBranch)
    {
        return $user->hasPermissionTo('view sub branches') || 
               $user->subBranches()->where('sub_branches.id', $subBranch->id)->exists();
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create sub branches');
    }

    public function update(User $user, SubBranch $subBranch)
    {
        return $user->hasPermissionTo('update sub branches') ||
               ($user->hasPermissionTo('update own sub branches') && 
                $user->subBranches()->where('sub_branches.id', $subBranch->id)->exists());
    }

    public function delete(User $user, SubBranch $subBranch)
    {
        return $user->hasPermissionTo('delete sub branches');
    }

    public function manage(User $user, SubBranch $subBranch)
    {
        return $user->hasPermissionTo('manage sub branches') ||
               $user->subBranches()->where('sub_branches.id', $subBranch->id)->exists();
    }
}
