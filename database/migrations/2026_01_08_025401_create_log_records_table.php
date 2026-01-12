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
        Schema::create('log_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('log_session_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamp('time_in')->nullable();
            $table->timestamp('time_out')->nullable();

            $table->timestamps();

            // Attendance-optimized indexes
            $table->index(['student_id', 'time_in']);
            $table->index(['log_session_id', 'time_in']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_records');
    }
};
