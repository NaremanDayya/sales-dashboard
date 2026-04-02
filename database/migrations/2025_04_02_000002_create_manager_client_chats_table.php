<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_client_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('sales_rep_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['client_id', 'sales_rep_id', 'manager_id'], 'unique_client_rep_manager');
            $table->index(['client_id', 'manager_id']);
            $table->index(['sales_rep_id', 'manager_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_client_chats');
    }
};
