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
        Schema::create('asset_m_model', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100)->unique();
            $table->enum('types_of_goods', ['physique', 'unphysique']);
            $table->string('name', 100)->unique();
            $table->timestamps();
        });
        Schema::create('asset_m_status', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });
        Schema::create('asset_m_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });
        Schema::create('asset_management', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->string('asset_tag', 100)->unique();
            $table->unsignedBigInteger('model_id')->foreign('model_id')->references('id')->on('asset_m_model')->onDelete('cascade');
            $table->unsignedBigInteger('status_id')->foreign('status_id')->references('id')->on('asset_m_status')->onDelete('cascade');
            $table->unsignedBigInteger('room_id')->foreign('room_id')->references('id')->on('asset_m_rooms')->onDelete('cascade');
            $table->unsignedBigInteger('pic')->foreign('pic')->references('id')->on('users')->onDelete('cascade');
            $table->longText('notes')->nullable();
            $table->string('image')->nullable();
            $table->date('purchase_at')->nullable();
            $table->float('purchase_price')->nullable();
            $table->string('suppliers')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_m_model');
        Schema::dropIfExists('asset_m_status');
        Schema::dropIfExists('asset_m_rooms');
        Schema::dropIfExists('asset_management');
    }
};
