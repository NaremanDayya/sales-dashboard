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
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('carried_over_amount')->default(0)->after('target_amount');
            $table->decimal('achieved_percentage', 5, 2)->default(0.00);
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('month');
            $table->year('year');
            $table->decimal('target_amount', 12, 2);
            $table->decimal('achieved_amount', 12, 2)->default(0);
            $table->boolean('is_achieved')->default(false);
            $table->boolean('commission_due')->default(false);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
