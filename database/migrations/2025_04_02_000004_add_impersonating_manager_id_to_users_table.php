<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('impersonating_manager_id')->nullable()->after('role')->constrained('sales_representatives')->onDelete('set null');
            $table->index('impersonating_manager_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['impersonating_manager_id']);
            $table->dropColumn('impersonating_manager_id');
        });
    }
};
