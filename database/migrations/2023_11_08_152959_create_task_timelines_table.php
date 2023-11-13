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
        Schema::create('task_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trainee_id')->nullable();
            $table->foreign('trainee_id')->references('id')->on('trainees');     
            $table->string('task_name');
            $table->string('task_status');
            $table->date('task_start_date');
            $table->date('task_end_date');
            $table->text('task_detail')->nullable();
            $table->string('task_priority');
            $table->text('task_overall_comment')->nullable();
            $table->text('timeline')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_timelines');
    }
};
