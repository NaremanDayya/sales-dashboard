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
        Schema::table('users', function (Blueprint $table) {
            $table->date('birthday')->nullable();
            $table->integer('age')->nullable();
            $table->string('id_card', 50)->nullable();
            $table->string('nationality', 100)->nullable(); 
            $table->enum('gender', ['male', 'female'])->nullable(); 
            $table->string('personal_image', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'birthday',
                'age',
                'id_card',
                'nationality',
                'gender',
                'personal_image',
            ]);
        });
    }
};
