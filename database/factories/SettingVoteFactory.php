<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\SettingVote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SettingVote>
 */
class SettingVoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'setting_id' => Setting::factory(),
            'vote_value' => fake()->randomElement([-1, 1]),
        ];
    }
}
