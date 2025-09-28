<?php

namespace App\Policies;

use App\Models\Kardex;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class KardexPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('view_kardex');
    }

    public function view(User $user, Kardex $kardex)
    {
        return $user->can('view_kardex');
    }

    public function create(User $user)
    {
        return $user->can('create_kardex_entries');
    }

    public function update(User $user, Kardex $kardex)
    {
        // Generalmente los movimientos de kardex no se actualizan
        return $user->can('update_kardex_entries') && $user->hasRole('super-admin');
    }

    public function delete(User $user, Kardex $kardex)
    {
        // Solo super admin puede eliminar entradas de kardex
        return $user->can('delete_kardex_entries') && $user->hasRole('super-admin');
    }
}