<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_representatives', function (Blueprint $table) {
            $table->foreignId('manager_id')->nullable()->after('user_id')->constrained('sales_representatives')->onDelete('set null');
            $table->index('manager_id');
        });
    }

    public function down(): void
    {
        Schema::table('sales_representatives', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn('manager_id');
        });
    }
};
