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
        Schema::create('tunes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name')->nullable();
            $table->integer('tune_type_id')->nullable()->index('tune_type_id');
            $table->integer('composer_id')->nullable()->index('tune_ibfk_composer');
            $table->integer('composer')->nullable();
            $table->string('origin')->nullable();
            $table->text('source')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunes');
    }
};
