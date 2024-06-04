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
        Schema::create('in_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->nullable();
            $table->bigInteger('schedule_group_attendances_id')->nullable();
            $table->float('lat')->nullable();
            $table->float('lng')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['late', 'unlate'])->nullable();
            $table->timestamps();
        });
        Schema::create('out_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('in_attendance_id')->foreign('in_attendance_id')->references('id')->on('in_attendances')->onDelete('cascade');
            $table->string('nik')->nullable();
            $table->bigInteger('schedule_group_attendances_id')->nullable();
            $table->float('lat')->nullable();
            $table->float('lng')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['late', 'unlate'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_attendances');
        Schema::dropIfExists('out_attendances');
    }
};
