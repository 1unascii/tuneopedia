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
        Schema::create('tune_videos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('tune_id')->index('tune_id');
            $table->string('youtube_id', 20);
            $table->string('title')->nullable();
            $table->dateTime('submitted_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tune_videos');
    }
};
