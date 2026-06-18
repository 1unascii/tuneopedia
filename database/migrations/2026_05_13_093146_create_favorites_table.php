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
        Schema::create('favorites', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id');
            $table->integer('tune_id')->index('tune_id');
            $table->dateTime('date_added')->nullable()->useCurrent();

            $table->unique(['user_id', 'tune_id'], 'unique_user_tune');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
