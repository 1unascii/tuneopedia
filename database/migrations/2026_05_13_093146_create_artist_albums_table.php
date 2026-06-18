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
        Schema::create('artist_albums', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('artist_id')->nullable()->index('artist_id');
            $table->integer('album_id')->nullable()->index('album_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_albums');
    }
};
