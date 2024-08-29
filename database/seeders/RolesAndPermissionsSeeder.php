<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'manage_bookings']);
        Permission::create(['name' => 'create_packages']);
        Permission::create(['name' => 'edit_packages']);
        Permission::create(['name' => 'view_reports']);
        Permission::create(['name' => 'manage_users']);

        // Create roles and assign permissions
        $customerRole = Role::create(['name' => 'customer']);
        // Customers don't need special permissions, they'll have default access

        $agentRole = Role::create(['name' => 'travel_agent']);
        $agentRole->givePermissionTo(['manage_bookings', 'create_packages', 'edit_packages']);

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
