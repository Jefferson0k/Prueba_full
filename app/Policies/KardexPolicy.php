<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Kardex;
use Illuminate\Auth\Access\HandlesAuthorization;

class KardexPolicy
{
    use HandlesAuthorization;

    // Permite ver el kardex por producto
    public function viewProduct(User $user)
    {
        return $user->can('view kardex producto');
    }

    // Permite ver el kardex general
    public function viewGeneral(User $user)
    {
        return $user->can('view kardex general');
    }

    // Permite ver el kardex valorizado
    public function viewValorizado(User $user)
    {
        return $user->can('view kardex valorizado');
    }

    // Permite exportar cualquiera
    public function export(User $user)
    {
        return $user->can('export kardex');
    }
}
