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
        Schema::create('sales_rep_work_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_rep_id')->nullable()->constrained('sales_representatives')->nullOnDelete();
            $table->string('sales_rep_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['sales_rep_id', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_rep_work_histories');
    }
};
