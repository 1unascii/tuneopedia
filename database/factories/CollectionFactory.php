<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Collection>
 */
class CollectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'author' => fake()->name(),
            'description' => fake()->sentence(),
            'is_shared' => fake()->boolean(70),
        ];
    }

    /** Mark the collection as shared (public) */
    public function shared(): static
    {
        return $this->state(['is_shared' => true]);
    }

    /** Mark the collection as private */
    public function private(): static
    {
        return $this->state(['is_shared' => false]);
    }
}
