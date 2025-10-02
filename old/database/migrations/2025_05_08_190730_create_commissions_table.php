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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_rep_id')->constrained('sales_representatives')->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('month');
            $table->unsignedInteger('year');
            $table->foreignId('target_id')->constrained()->cascadeOnDelete();
            $table->decimal('achieved_percentage', 5, 2)->default(0.00);
            $table->decimal('total_achieved_amount', 12, 2)->default(0.00);
            $table->decimal('commission_rate', 5, 2)->default(0.00); // example: 0.5
            $table->decimal('commission_amount', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
