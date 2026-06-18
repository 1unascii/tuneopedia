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
        Schema::create('tune_aliases', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('tune_id')->index('tune_id');
            $table->string('alias_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tune_aliases');
    }
};
