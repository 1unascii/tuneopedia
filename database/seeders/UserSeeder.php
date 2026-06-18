<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create the test user (verified)
        User::factory()->create([
            'name' => 'testuser',
            'email' => 'testuser@test.com',
            'password' => bcrypt('asdfasdf'),
            'email_verified_at' => now(),
        ]);

        // Create verified users
        User::factory(3)->create();

        // Create unverified users
        User::factory(2)->unverified()->create();
    }
}
