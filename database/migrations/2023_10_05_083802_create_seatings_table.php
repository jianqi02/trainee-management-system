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
        Schema::create('seatings', function (Blueprint $table) {
            $table->id();
            $table->json('seat_detail')->nullable();
            $table->string('week')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seatings');
    }
};
