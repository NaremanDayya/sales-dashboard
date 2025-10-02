<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AssignSalesRepPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Get the salesRep role
        $role = Role::where('name', 'salesRep')->first();

        if (!$role) {
            $this->command->warn('Role "salesRep" not found.');
            return;
        }

        // Get all permissions that start with "sales_rep"
        $permissions = Permission::where('name', 'like', 'sales_rep%')->get();

        if ($permissions->isEmpty()) {
            $this->command->warn('No permissions found starting with "sales_rep".');
            return;
        }

        // Assign permissions to the role
        $role->syncPermissions($permissions);

        // Assign role to users who have the role name (in DB or by checking)
        $users = User::role('salesRep')->get();

        foreach ($users as $user) {
            $user->assignRole($role);
        }

        $this->command->info("Assigned " . $permissions->count() . " sales_rep permissions to role 'salesRep' and synced users.");
    }
}
