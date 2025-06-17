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
        Schema::table('targets', function (Blueprint $table) {
            $table->decimal('achieved_percentage', 5, 2)->default(0.00);
        });

    }

    public function down()
    {
        Schema::table('targets', function (Blueprint $table) {
            $table->dropColumn('achieved_percentage');
        });
    }
};
