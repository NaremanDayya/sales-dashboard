<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['sales_rep_id']);

            // Add the correct foreign key constraint
            $table->foreign('sales_rep_id')
                  ->references('id')
                  ->on('sales_representatives')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Drop the corrected foreign key
            $table->dropForeign(['sales_rep_id']);

            // Restore the old (incorrect) foreign key
            $table->foreign('sales_rep_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
