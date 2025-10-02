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
              
$table->dropColumn(['user_id']);
$table->foreignId('sales_rep_id')->references('id')->on('sales_representatives')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('targets', function (Blueprint $table) {
 $table->dropForeign(['sales_rep_id']);
 $table->dropColumn('sales_rep_id');
                        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }
};
