<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_representatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->date('start_work_date');
            $table->integer('work_duration');
            $table->integer('target_customers')->default(0);
            $table->integer('late_customers')->default(0);
            $table->integer('total_orders')->default(0);
            $table->integer('pending_orders')->default(0);
            $table->integer('interested_customers')->default(0);
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_representatives');
    }
};
