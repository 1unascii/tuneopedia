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
        Schema::create('tune_tracks', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('tune_id')->nullable()->index('tune_id');
            $table->integer('track_id')->nullable()->index('track_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tune_tracks');
    }
};
