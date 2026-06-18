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
        Schema::create('collections', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable()->index('user_id');
            $table->string('name');
            $table->string('author')->nullable();
            $table->text('description')->nullable();
            $table->string('publisher')->nullable();
            $table->date('published_date')->nullable();
            $table->string('cover_image')->nullable();
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->boolean('is_shared')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
