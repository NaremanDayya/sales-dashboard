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
        Schema::table('sales_representatives', function (Blueprint $table) {
            $table->date('stop_work_date')->nullable()->after('start_work_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_representatives', function (Blueprint $table) {
            $table->dropColumn('stop_work_date');
        });
    }
};
