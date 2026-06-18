<?php

namespace Database\Factories;

use App\Models\Tune;
use App\Models\TuneType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tune>
 */
class TuneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'tune_type_id' => TuneType::factory(),
        ];
    }
}
