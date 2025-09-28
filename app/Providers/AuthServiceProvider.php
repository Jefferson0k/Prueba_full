<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\Room;
use App\Models\SystemSetting;
use App\Policies\BookingPolicy;
use App\Policies\BranchPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\PaymentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RoomPolicy;
use App\Policies\SystemSettingPolicy;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use PDOException;

class AuthServiceProvider extends ServiceProvider {
    protected $policies = [
        //Role::class => RolePolicy::class,
        //Permission::class => PermissionPolicy::class,
        //'reporte' => ReportePolicy::class,
    ];
    public function boot(): void {
        $this->registerPolicies();
        try {
            DB::connection()->getPdo();
            if (Schema::hasTable('permissions') && Schema::hasTable('roles')) {
                Gate::policy(Role::class, RolePolicy::class);
                Gate::policy(Permission::class, PermissionPolicy::class);
                Gate::policy(Branch::class, BranchPolicy::class);
                Gate::policy(Room::class, RoomPolicy::class);
                Gate::policy(Booking::class, BookingPolicy::class);
                Gate::policy(Payment::class, PaymentPolicy::class);
                Gate::policy(Inventory::class, InventoryPolicy::class);
                Gate::policy(SystemSetting::class, SystemSettingPolicy::class);
            }
        } catch (PDOException $e) {
            
        }
    }
}