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
        Schema::create('collection_tunes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('collection_id')->index('collection_id');
            $table->integer('tune_id')->index('tune_id');
            $table->integer('position')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_tunes');
    }
};
