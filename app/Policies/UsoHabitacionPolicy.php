<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UsoHabitacion;
use Illuminate\Auth\Access\Response;

class UsoHabitacionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver uso habitacion');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UsoHabitacion $usoHabitacion): bool
    {
        return $user->can('ver uso habitacion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear uso habitacion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UsoHabitacion $usoHabitacion): bool
    {
        return $user->can('editar uso habitacion');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UsoHabitacion $usoHabitacion): bool
    {
        return $user->can('eliminar uso habitacion');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UsoHabitacion $usoHabitacion): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UsoHabitacion $usoHabitacion): bool
    {
        return false;
    }
}
