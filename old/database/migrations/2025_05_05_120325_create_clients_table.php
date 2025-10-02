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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_logo')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_person');
            $table->string('contact_position')->nullable();
            $table->string('phone');
            $table->string('whatsapp_link')->nullable();
            $table->enum('interest_status', ['interested', 'not interested','neutral'])->default('neutral');
            $table->foreignId('sales_rep_id')->constrained('users')->onDelete('cascade');
            $table->date('last_contact_date')->nullable();
            $table->integer('contact_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
};
