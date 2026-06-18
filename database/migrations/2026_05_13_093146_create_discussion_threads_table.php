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
        Schema::create('discussion_threads', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('tune_id')->nullable();
            $table->unsignedBigInteger('user_id')->index('discussion_thread_user_id_foreign');
            $table->string('title')->default('');
            $table->text('body');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_threads');
    }
};
