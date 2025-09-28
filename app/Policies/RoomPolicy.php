<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view rooms');
    }

    public function view(User $user, Room $room)
    {
        return $user->hasPermissionTo('view rooms') ||
               $user->branches()->where('branches.id', $room->floor->branch_id)->exists();
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create rooms');
    }

    public function update(User $user, Room $room)
    {
        return $user->hasPermissionTo('update rooms') ||
               ($user->hasPermissionTo('update branch rooms') && 
                $user->branches()->where('branches.id', $room->floor->branch_id)->exists());
    }

    public function delete(User $user, Room $room)
    {
        return $user->hasPermissionTo('delete rooms');
    }

    public function changeStatus(User $user, Room $room)
    {
        return $user->hasPermissionTo('change room status') ||
               $user->branches()->where('branches.id', $room->floor->branch_id)->exists();
    }
}
