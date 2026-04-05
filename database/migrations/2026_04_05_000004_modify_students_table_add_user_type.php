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
        Schema::table('students', function (Blueprint $table) {
            $table->string('user_type')->default('college')->after('id');
            $table->string('strand')->nullable()->after('course');

            $table->index('user_type');
        });

        // Make course nullable for SHS students
        Schema::table('students', function (Blueprint $table) {
            $table->string('course')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['user_type']);
            $table->dropColumn(['user_type', 'strand']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('course')->nullable(false)->change();
        });
    }
};
