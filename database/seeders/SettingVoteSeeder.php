<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingVoteSeeder extends Seeder
{
    /**
     * Seed random votes on settings from existing users.
     * Creates a few extra users if only one exists.
     */
    public function run(): void
    {
        DB::table('setting_votes')->truncate();

        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Run UserSeeder first.');

            return;
        }

        $settings = Setting::all();
        if ($settings->isEmpty()) {
            $this->command->warn('No settings found. Run TuneSeeder first.');

            return;
        }

        $votesInserted = 0;

        foreach ($settings as $setting) {
            // Each setting gets votes from a random subset of users
            $voters = $users->random(rand(1, min(5, $users->count())));

            foreach ($voters as $user) {
                DB::table('setting_votes')->insert([
                    'user_id' => $user->id,
                    'setting_id' => $setting->id,
                    'vote_value' => rand(0, 1) ? 1 : -1,
                    'created_at' => now()->subDays(rand(0, 90)),
                ]);
                $votesInserted++;
            }
        }

        $this->command->info("Seeded {$votesInserted} votes across {$settings->count()} settings.");
    }
}
