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
        Schema::table('log_records', function (Blueprint $table) {
            $table->string('loggable_type')->default('student')->after('id');
            $table->foreignId('faculty_id')->nullable()->after('student_id')
                ->constrained()->cascadeOnDelete();

            $table->index(['faculty_id', 'time_in']);
            $table->index('loggable_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_records', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->dropIndex(['faculty_id', 'time_in']);
            $table->dropIndex(['loggable_type']);
            $table->dropColumn(['loggable_type', 'faculty_id']);
        });
    }
};
