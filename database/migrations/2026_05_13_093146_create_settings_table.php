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
        Schema::create('settings', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('tune_id')->nullable()->index('tune_id');
            $table->integer('user_id')->nullable()->index('user_id');
            $table->string('name')->nullable();
            $table->string('default_note_length', 10)->nullable()->default('1/8');
            $table->string('time_signature', 7)->default('4/4');
            $table->string('key_signature', 50)->nullable();
            $table->text('abc_transcription')->nullable();
            $table->longText('notes')->nullable();
            $table->text('source')->nullable();
            $table->string('origin')->nullable();
            $table->text('history')->nullable();
            $table->string('book')->nullable();
            $table->string('discography')->nullable();
            $table->string('transcription_credit')->nullable();
            $table->string('area')->nullable();
            $table->string('parts', 100)->nullable();
            $table->smallInteger('tempo')->nullable();
            $table->integer('instrument_id')->nullable()->index('setting_instrument_fk');
            $table->text('lyrics')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
