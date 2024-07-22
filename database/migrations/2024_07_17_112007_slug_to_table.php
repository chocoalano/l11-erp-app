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
        Schema::table('products', function (Blueprint $table) {
            $table->longText('slug')->after('title')->nullable();
        });
        Schema::table('product_items', function (Blueprint $table) {
            $table->longText('slug')->after('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
