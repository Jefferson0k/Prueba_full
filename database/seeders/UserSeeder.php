<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener roles
        $adminRole = Role::firstOrCreate(['name' => 'administrador']);
        $personalRole = Role::firstOrCreate(['name' => 'personal']);

        // Sincronizar permisos al rol administrador
        $permissions = Permission::all();
        if ($adminRole) {
            $adminRole->syncPermissions($permissions);
        }

        // Crear usuarios evitando duplicados
        $admin_1 = User::firstOrCreate(
            ['dni' => '76393671'],
            [
                'name' => 'Jefferson Grabiel',
                'apellidos' => 'Covenas Roman',
                'nacimiento' => '2003-03-11',
                'email' => 'jefersoncovenas7@gmail.com',
                'username' => 'JCOVENASRO11',
                'password' => Hash::make('12345678'),
                'status' => true,
                'restablecimiento' => 0,
            ]
        );

        // Asignar roles
        if ($adminRole) $admin_1->assignRole($adminRole);
    }
}
