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
        Schema::create('trainee_supervisors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trainee_id')->nullable();
            $table->foreign('trainee_id')->references('id')->on('alltrainees');            
            $table->unsignedBigInteger('assigned_supervisor_id')->nullable();
            $table->foreign('assigned_supervisor_id')->references('id')->on('supervisors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainee_supervisors');
    }
};
