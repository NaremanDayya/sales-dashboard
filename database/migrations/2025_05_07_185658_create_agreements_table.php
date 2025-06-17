<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->string('agreement_id')->unique(); // For display (e.g., AG-2025-001)
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_rep_id')->constrained('sales_representatives')->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->date('implementation_date');
            $table->integer('return_value')->nullable();
            $table->date('signing_date');
            $table->integer('duration_years');
            $table->date('end_date')->nullable();
            $table->enum('termination_type', ['returnable', 'non_returnable']);
            $table->integer('notice_months')->default(0);
            $table->enum('notice_status', ['not_sent', 'sent'])->default('not_sent');
            $table->integer('product_quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->enum('agreement_status', ['active', 'terminated', 'expired'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};
