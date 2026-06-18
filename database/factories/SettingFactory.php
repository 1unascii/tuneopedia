<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\Tune;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tune_id' => Tune::factory(),
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'time_signature' => '4/4',
            'default_note_length' => '1/8',
            'key_signature' => fake()->randomElement(['G', 'D', 'A', 'C', 'Em', 'Am']),
            'abc_transcription' => 'ABCD|EFGA|',
        ];
    }
}
