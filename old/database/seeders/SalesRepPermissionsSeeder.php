<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SalesRepPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'sales_rep_show_his_clients',
            'sales_rep_add_new_client',
            'sales_rep_add_last_contact_date',
            'sales_rep_add_client_request',
            'sales_rep_show_his_target_achievement',
            'sales_rep_chat_with_admin',
            'sales_rep_generate_his_client_pdf',
            'sales_rep_edit_last_contact_date',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
