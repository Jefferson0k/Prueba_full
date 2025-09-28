<?php

namespace App\Policies;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SystemSettingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view system settings');
    }

    public function view(User $user, SystemSetting $setting)
    {
        return $user->hasPermissionTo('view system settings') ||
               ($setting->is_public && $user->hasPermissionTo('view public settings'));
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create system settings');
    }

    public function update(User $user, SystemSetting $setting)
    {
        return $user->hasPermissionTo('update system settings');
    }

    public function delete(User $user, SystemSetting $setting)
    {
        return $user->hasPermissionTo('delete system settings');
    }
}