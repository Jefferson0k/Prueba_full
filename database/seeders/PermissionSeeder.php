<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        #Cliente
        Permission::create(['name' => 'crear cliente']);
        Permission::create(['name' => 'editar cliente']);
        Permission::create(['name' => 'eliminar cliente']);
        Permission::create(['name' => 'ver cliente']);
        #Habitacion
        Permission::create(['name' => 'crear habitacion']);
        Permission::create(['name' => 'editar habitacion']);
        Permission::create(['name' => 'eliminar habitacion']);
        Permission::create(['name' => 'ver habitacion']);
        #Horario
        Permission::create(['name' => 'crear horario']);
        Permission::create(['name' => 'editar horario']);
        Permission::create(['name' => 'eliminar horario']);
        Permission::create(['name' => 'ver horario']);
        #Pisos
        Permission::create(['name' => 'crear piso']);
        Permission::create(['name' => 'editar piso']);
        Permission::create(['name' => 'eliminar piso']);
        Permission::create(['name' => 'ver piso']);
        #Tipo de habitacion
        Permission::create(['name' => 'crear tipo habitacion']);
        Permission::create(['name' => 'editar tipo habitacion']);
        Permission::create(['name' => 'eliminar tipo habitacion']);
        Permission::create(['name' => 'ver tipo habitacion']);
        #Uso de la habitacion
        Permission::create(['name' => 'crear uso habitacion']);
        Permission::create(['name' => 'editar uso habitacion']);
        Permission::create(['name' => 'eliminar uso habitacion']);
        Permission::create(['name' => 'ver uso habitacion']);
        #User
        Permission::create(['name' => 'crear usuarios']);
        Permission::create(['name' => 'editar usuarios']);
        Permission::create(['name' => 'eliminar usuarios']);
        Permission::create(['name' => 'ver usuarios']);
        # Roles
        Permission::create(['name' =>'crear roles']);
        Permission::create(['name' =>'editar roles']);
        Permission::create(['name' =>'eliminar roles']);
        Permission::create(['name' =>'ver roles']);
        # Permisos
        Permission::create(['name' =>'crear permisos']);
        Permission::create(['name' =>'editar permisos']);
        Permission::create(['name' =>'eliminar permisos']);
        Permission::create(['name' =>'ver permisos']);
    }
}
