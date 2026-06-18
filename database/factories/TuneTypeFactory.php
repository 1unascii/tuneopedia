<?php

namespace Database\Factories;

use App\Models\TuneType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TuneType>
 */
class TuneTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Reel', 'Jig', 'Hornpipe', 'Polka', 'Waltz', 'Strathspey',
                'Slip Jig', 'March', 'Mazurka', 'Schottische',
            ]),
        ];
    }
}
