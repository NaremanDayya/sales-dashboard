<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AdminPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'update_client_request',
            'update_agreement_request',
            'add_sales_rep',
            'edit_sales_rep',
            'delete_sales_rep',
            'view_sales_rep',
            'view_clients',
            'give_permissions',
            'add_targets',
            'generate_pdf',
            'show_agreements',
            'show_targets',
            'show_commissions',
        ];

        // Create or get 'admin' role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create permissions and sync to role
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole->syncPermissions($permissions);

        // Assign role to first admin user (adjust as needed)
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser) {
            $adminUser->syncRoles(['admin']);
        }
    }
}
