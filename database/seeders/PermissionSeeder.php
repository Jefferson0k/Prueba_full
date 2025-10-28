<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder{
    public function run(): void{

        #=============================
        #Pagos Personal
        #=============================
        Permission::create(['name' => 'view pagos personal']);
        Permission::create(['name' => 'create pagos personal']);
        Permission::create(['name' => 'update pagos personal']);
        Permission::create(['name' => 'delete pagos personal']);
        Permission::create(['name' => 'approve pagos personal']);
        Permission::create(['name' => 'cancel pagos personal']);
        Permission::create(['name' => 'view reportes pagos personal']);
        Permission::create(['name' => 'process payroll']);

        #=============================
        #Habitacion
        #=============================
        Permission::create(['name' => 'create habitacion']);
        Permission::create(['name' => 'update habitacion']);
        Permission::create(['name' => 'delete habitacion']);
        Permission::create(['name' => 'view habitacion']);

        #=============================
        #CAJAS
        #=============================
        Permission::create(['name' => 'crear caja']);
        Permission::create(['name' => 'editar caja']);
        Permission::create(['name' => 'eliminar caja']);
        Permission::create(['name' => 'view caja']);
        Permission::create(['name' => 'abrir caja']);
        #=============================
        #MOVEMENT DETAIL
        #=============================
        Permission::create(['name' => 'create movimiento detalle']);
        Permission::create(['name' => 'update movimiento detalle']);
        Permission::create(['name' => 'delete movimiento detalle']);
        Permission::create(['name' => 'view movimiento detalle']);

        #=============================
        #MOVEMENT
        #=============================
        Permission::create(['name' => 'create movimiento']);
        Permission::create(['name' => 'update movimiento']);
        Permission::create(['name' => 'delete movimiento']);
        Permission::create(['name' => 'view movimiento']);

        #=============================
        #FLOOR
        #=============================
        Permission::create(['name' => 'create floors']);
        Permission::create(['name' => 'update floors']);
        Permission::create(['name' => 'delete floors']);
        Permission::create(['name' => 'view floors']);
        
        # ============================
        # CUSTOMERS
        # ============================
        Permission::create(['name' => 'create customers']);
        Permission::create(['name' => 'update customers']);
        Permission::create(['name' => 'delete customers']);
        Permission::create(['name' => 'view customers']);
        Permission::create(['name' => 'restore customers']);
        Permission::create(['name' => 'force delete customers']);

        # ============================
        # INVENTORY (SUB_BRANCH_PRODUCTS)
        # ============================
        Permission::create(['name' => 'create inventory']);
        Permission::create(['name' => 'update inventory']);
        Permission::create(['name' => 'delete inventory']);
        Permission::create(['name' => 'view inventory']);
        Permission::create(['name' => 'adjust stock']);
        Permission::create(['name' => 'transfer stock']);
        Permission::create(['name' => 'view low stock']);

        # ============================
        # SALES
        # ============================
        Permission::create(['name' => 'create sales']);
        Permission::create(['name' => 'update sales']);
        Permission::create(['name' => 'delete sales']);
        Permission::create(['name' => 'view sales']);
        Permission::create(['name' => 'cancel sales']);
        Permission::create(['name' => 'view all sales']);
        Permission::create(['name' => 'update all sales']);
        Permission::create(['name' => 'delete all sales']);
        Permission::create(['name' => 'cancel all sales']);
        Permission::create(['name' => 'print sales']);

        # ============================
        # SALE DETAILS
        # ============================
        Permission::create(['name' => 'create sale details']);
        Permission::create(['name' => 'update sale details']);
        Permission::create(['name' => 'delete sale details']);
        Permission::create(['name' => 'view sale details']);

        # ============================
        # KARDEX
        # ============================
        Permission::create(['name' => 'view kardex producto']);
        Permission::create(['name' => 'view kardex general']);
        Permission::create(['name' => 'view kardex valorizado']);
        Permission::create(['name' => 'view kardex reports']);

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
        # ROOMS
        # ============================
        Permission::create(['name' => 'create rooms']);
        Permission::create(['name' => 'update rooms']);
        Permission::create(['name' => 'delete rooms']);
        Permission::create(['name' => 'view rooms']);
        Permission::create(['name' => 'change room status']);
        Permission::create(['name' => 'update branch rooms']);

        # ============================
        # BOOKINGS
        # ============================
        Permission::create(['name' => 'create bookings']);
        Permission::create(['name' => 'update bookings']);
        Permission::create(['name' => 'delete bookings']);
        Permission::create(['name' => 'view bookings']);
        Permission::create(['name' => 'update own bookings']);
        Permission::create(['name' => 'cancel bookings']);
        Permission::create(['name' => 'cancel own bookings']);
        Permission::create(['name' => 'checkin bookings']);
        Permission::create(['name' => 'checkout bookings']);
        Permission::create(['name' => 'add booking consumptions']);

        # ============================
        # PAYMENTS
        # ============================
        Permission::create(['name' => 'create payments']);
        Permission::create(['name' => 'update payments']);
        Permission::create(['name' => 'delete payments']);
        Permission::create(['name' => 'view payments']);
        Permission::create(['name' => 'refund payments']);
        Permission::create(['name' => 'process payments']);

        # ============================
        # BRANCHES
        # ============================
        Permission::create(['name' => 'create branches']);
        Permission::create(['name' => 'update branches']);
        Permission::create(['name' => 'update own branches']);
        Permission::create(['name' => 'delete branches']);
        Permission::create(['name' => 'view branches']);
        Permission::create(['name' => 'manage branches']);

        # ============================
        # SUB BRANCHES
        # ============================
        Permission::create(['name' => 'create sub branches']);
        Permission::create(['name' => 'update sub branches']);
        Permission::create(['name' => 'update own sub branches']);
        Permission::create(['name' => 'delete sub branches']);
        Permission::create(['name' => 'view sub branches']);
        Permission::create(['name' => 'manage sub branches']);

        # ============================
        # SYSTEM SETTINGS
        # ============================
        Permission::create(['name' => 'view system settings']);
        Permission::create(['name' => 'view public settings']);
        Permission::create(['name' => 'create system settings']);
        Permission::create(['name' => 'update system settings']);
        Permission::create(['name' => 'delete system settings']);

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
