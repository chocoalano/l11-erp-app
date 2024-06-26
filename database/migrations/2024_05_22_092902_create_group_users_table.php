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
        Schema::create('group_users', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->unsignedBigInteger('group_attendance_id');
            $table->timestamps();

            $table->foreign('nik')->references('nik')->on('users')->onDelete('cascade');
            $table->foreign('group_attendance_id')->references('id')->on('group_attendances')->onDelete('cascade');

            $table->unique(['nik', 'group_attendance_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_users');
    }
};
