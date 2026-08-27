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
        Schema::table('targets', function (Blueprint $table) {
            // How much of this month's over-achievement (beyond target_amount)
            // is banked to auto-fill future months' achieved_amount, one month's
            // target at a time, until exhausted.
            $table->decimal('surplus_carried_amount', 12, 2)->default(0)->after('carried_over_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('targets', function (Blueprint $table) {
            $table->dropColumn('surplus_carried_amount');
        });
    }
};
