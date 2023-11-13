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
        Schema::create('trainees', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('personal_email')->nullable()->unique();
            $table->string('sains_email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('password');
            $table->string('graduate_date')->nullable();
            $table->string('expertise')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('supervisor_status')->nullable();
            $table->string('resume_path')->nullable();
            $table->string('acc_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainees');
    }
};
