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
        Schema::table('discussion_replies', function (Blueprint $table) {
            $table->foreign(['discussion_thread_id'])->references(['id'])->on('discussion_threads')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discussion_replies', function (Blueprint $table) {
            $table->dropForeign('discussion_replies_discussion_thread_id_foreign');
            $table->dropForeign('discussion_replies_user_id_foreign');
        });
    }
};
