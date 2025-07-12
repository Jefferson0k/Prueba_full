<?php

namespace App\Policies;

use App\Models\Piso;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PisoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver piso');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Piso $piso): bool
    {
        return $user->can('ver piso');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear piso');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Piso $piso): bool
    {
        return $user->can('editar piso');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Piso $piso): bool
    {
        return $user->can('eliminar piso');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Piso $piso): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Piso $piso): bool
    {
        return false;
    }
}
