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
        Schema::create('tracks', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('album_id')->nullable()->index('album_id');
            $table->string('name')->nullable();
            $table->integer('track_number')->nullable();
            $table->integer('tune_id')->nullable()->index('tune_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
