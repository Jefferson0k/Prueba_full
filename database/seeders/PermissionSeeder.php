<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder{
    public function run(): void{
        
        #===========================
        # CATEGORIAS
        #===========================
        Permission::create(['name' => 'ver categorias']);
        Permission::create(['name' => 'crear categorias']);
        Permission::create(['name' => 'editar categorias']);
        Permission::create(['name' => 'eliminar categorias']);
        
        # ============================
        # PRODUCTS
        # ============================
        Permission::create(['name' => 'create products']);
        Permission::create(['name' => 'update products']);
        Permission::create(['name' => 'update own products']);
        Permission::create(['name' => 'delete products']);
        Permission::create(['name' => 'delete own products']);
        Permission::create(['name' => 'view products']);
        Permission::create(['name' => 'restore products']);
        Permission::create(['name' => 'force delete products']);
        Permission::create(['name' => 'manage products']);

        # ============================
        # USERS
        # ============================
        Permission::create(['name' => 'crear usuarios']);
        Permission::create(['name' => 'editar usuarios']);
        Permission::create(['name' => 'eliminar usuarios']);
        Permission::create(['name' => 'ver usuarios']);

        # ============================
        # ROLES
        # ============================
        Permission::create(['name' => 'crear roles']);
        Permission::create(['name' => 'editar roles']);
        Permission::create(['name' => 'eliminar roles']);
        Permission::create(['name' => 'ver roles']);

        # ============================
        # PERMISSIONS
        # ============================
        Permission::create(['name' => 'crear permisos']);
        Permission::create(['name' => 'editar permisos']);
        Permission::create(['name' => 'eliminar permisos']);
        Permission::create(['name' => 'ver permisos']);
    }
}
