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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_number')->unique();
            $table->string('model_name', 50);
            $table->integer('id_form');
            $table->timestamps();
        });
        Schema::create('cutis', function (Blueprint $table) {
            $table->id();
            $table->string('event_number')->unique()->nullable();
            $table->foreignId('user_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('category', ['half', 'full', 'suddenly']);
            $table->longText('description');
            $table->enum('user_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('line_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('hrga_approved', ['y', 'n', 'w'])->default('w');
            $table->timestamps();
        });
        Schema::create('izins_in_out', function (Blueprint $table) {
            $table->id();
            $table->string('event_number')->unique()->nullable();
            $table->foreignId('user_id')->nullable();
            $table->date('date')->nullable();
            $table->time('out_time')->nullable();
            $table->time('in_time')->nullable();
            $table->longText('description');
            $table->enum('user_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('line_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('hrga_approved', ['y', 'n', 'w'])->default('w');
            $table->timestamps();
        });
        Schema::create('work_overtime', function (Blueprint $table) {
            $table->id();
            $table->string('event_number')->unique()->nullable();
            $table->foreignId('userid_created')->nullable();
            $table->date('date_spl')->nullable();
            $table->foreignId('organization_id')->nullable();
            $table->foreignId('job_position_id')->nullable();
            $table->boolean('overtime_day_status')->nullable();
            $table->date('date_overtime_at')->nullable();
            $table->enum('admin_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('line_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('gm_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('hrga_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('director_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('fat_approved', ['y', 'n', 'w'])->default('w');
            $table->timestamps();
        });
        Schema::create('user_overtime', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_overtime_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });
        Schema::create('dispens', function (Blueprint $table) {
            $table->id();
            $table->string('event_number')->unique()->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('category')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('total_day')->nullable();
            $table->longText('description');
            $table->enum('user_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('line_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('hrga_approved', ['y', 'n', 'w'])->default('w');
            $table->timestamps();
        });
        Schema::create('adjust_attendance', function (Blueprint $table) {
            $table->id();
            $table->string('event_number')->unique()->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('problem')->nullable();
            $table->date('date')->nullable();
            $table->longText('description');
            $table->enum('user_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('line_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('hrga_approved', ['y', 'n', 'w'])->default('w');
            $table->timestamps();
        });
        Schema::create('izin_or_sick', function (Blueprint $table) {
            $table->id();
            $table->string('event_number')->unique()->nullable();
            $table->foreignId('user_id')->nullable();
            $table->boolean('type')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('total_day')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->longText('description');
            $table->enum('user_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('line_approved', ['y', 'n', 'w'])->default('w');
            $table->enum('hrga_approved', ['y', 'n', 'w'])->default('w');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('cutis');
        Schema::dropIfExists('izins_in_out');
        Schema::dropIfExists('work_overtime');
        Schema::dropIfExists('user_overtime');
        Schema::dropIfExists('dispens');
        Schema::dropIfExists('adjust_attendance');
        Schema::dropIfExists('izin_or_sick');
    }
};
