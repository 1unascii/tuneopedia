<?php

namespace Database\Seeders;

use App\Models\DiscussionThread;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscussionThreadSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DiscussionThread::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $users = User::all();

        DiscussionThread::factory()->count(10)->create([
            'user_id' => fn () => $users->random()->id,
        ]);
    }
}
