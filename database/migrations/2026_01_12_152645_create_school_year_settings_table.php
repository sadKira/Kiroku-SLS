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
        Schema::create('school_year_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_year')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index for performance
            $table->index('school_year');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_year_settings');
    }
};
