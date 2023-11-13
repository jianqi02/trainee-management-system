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
        Schema::create('supervisors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('section')->nullable();
            $table->string('department')->nullable();
            $table->string('personal_email')->nullable()->unique();
            $table->string('sains_email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('password');
            $table->string('trainee_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisors');
    }
};
