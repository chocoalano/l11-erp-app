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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->nullable();
            $table->bigInteger('schedule_group_attendances_id')->nullable();
            $table->date('date')->nullable();
            $table->float('lat_in')->nullable();
            $table->float('lng_in')->nullable();
            $table->time('time_in')->nullable();
            $table->string('photo_in')->nullable();
            $table->enum('status_in', ['late', 'unlate'])->nullable();
            $table->float('lat_out')->nullable();
            $table->float('lng_out')->nullable();
            $table->time('time_out')->nullable();
            $table->string('photo_out')->nullable();
            $table->enum('status_out', ['late', 'unlate'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
