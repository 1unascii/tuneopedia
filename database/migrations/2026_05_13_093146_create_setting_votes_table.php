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
        Schema::create('setting_votes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id');
            $table->integer('setting_id')->index('setting_id');
            $table->tinyInteger('vote_value');
            $table->dateTime('created_at')->nullable()->useCurrent();

            $table->unique(['user_id', 'setting_id'], 'unique_user_vote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_votes');
    }
};
