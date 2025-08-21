<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
   $table->json('contact_info')->nullable()->after('role');
            $table->json('privileges')->nullable()->after('contact_info');
                             $table->enum('account_status', ['active', 'inactive'])->default('active');

       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
$table->dropColumn([
               
                'contact_info',
                'privileges',
'account_status',
                
            ]);            
        });
    }
};
