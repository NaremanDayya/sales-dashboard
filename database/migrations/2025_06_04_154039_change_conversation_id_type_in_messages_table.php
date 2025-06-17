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
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['conversation_id']);
            $table->uuid('conversation_id')->change();
            $table->foreignId('conversation_id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->unsignedBigInteger('conversation_id')->change();
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
        });
    }
};
