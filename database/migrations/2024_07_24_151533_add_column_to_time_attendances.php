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
        Schema::table('time_attendances', function (Blueprint $table) {
            $table->enum('pattern_name', ['production', 'warehouse', 'maintenance', 'office', 'customs'])->default('production')->after('out')->nullable();
            $table->integer('rules')->after('pattern_name')->nullable();
        });
        Schema::table('group_attendances', function (Blueprint $table) {
            $table->enum('pattern_name', ['production', 'warehouse', 'maintenance', 'office', 'customs'])->default('production')->after('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_attendances', function (Blueprint $table) {
            $table->dropColumn('pattern_name');
            $table->dropColumn('rules');
        });
        Schema::table('group_attendances', function (Blueprint $table) {
            $table->dropColumn('pattern_name');
        });
    }
};
