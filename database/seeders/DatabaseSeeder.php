<?php


namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles y permisos primero
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class
        ]);

        // Configuración base
        $this->call([
            SystemSettingSeeder::class, // Configuraciones generales
            CurrencySeeder::class,      // Monedas disponibles
            RateTypeSeeder::class,      // Tipos de tarifa (hora, día, noche)
            ProviderSeeder::class,    // Proveedores
        ]);

        // Estructura de hotel
        $this->call([
            BranchSeeder::class,        // Sucursales
            RoomTypeSeeder::class
            #SubBranchSeeder::class,
            #FloorSeeder::class,         // Pisos
            #RoomSeeder::class,          // Habitaciones
        ]);

        // Inventario y productos
        $this->call([
            ProductCategorySeeder::class, // Categorías de productos
            ProductSeeder::class,         // Productos
            //InventorySeeder::class,       // Inventario por sucursal
        ]);

        // Clientes y reservas
        $this->call([
            //ClientSeeder::class,      // Clientes
            //BookingSeeder::class,     // Reservas
            //PaymentSeeder::class,     // Pagos
        ]);
    }
}