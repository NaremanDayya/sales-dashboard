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
        Schema::create('sales_reps_login_ips', function (Blueprint $table) {
	 $table->id();
            $table->foreignId('sales_rep_id')->constrained('sales_representatives')->onDelete('cascade');
            $table->ipAddress('ip_address');
            $table->boolean('is_allowed')->default(true);
            $table->boolean('is_temporary')->default(false);
            $table->timestamp('allowed_until')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_reps_login_ips');
    }
};
