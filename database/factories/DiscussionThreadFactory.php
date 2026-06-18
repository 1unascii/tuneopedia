<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DiscussionThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiscussionThread>
 */
class DiscussionThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $topics = [
            'What are your favorite fiddle tunes for a jam?',
            'Best way to learn clawhammer banjo?',
            'Scruggs style vs melodic banjo - which do you prefer?',
            'Tips for learning breakdowns on the fiddle?',
            'Anyone else getting into oldtime music?',
            'What tunings do you use for oldtime banjo?',
            'Favorite Bill Monroe recordings?',
            'How do you work out harmony vocals for bluegrass?',
            'Recommended tune books for bluegrass flatpicking?',
            'Does anyone play upright bass at jams?',
            'Thoughts on playing fiddle tunes on mandolin?',
            'How do you handle playing in different keys at a jam?',
            'Best recordings for learning oldtime fiddle style?',
            'Favorite waltzes to play?',
            'Tips for your first bluegrass jam?',
            'What tunes does every picker need to know?',
            'How do you keep track of all the tunes you know?',
            'Differences between contest fiddling and jam fiddling?',
            'Anybody working on crosspicking right now?',
            'What\'s your go-to kickoff tune at a jam?',
        ];

        $bodies = [
            'I\'ve been picking for a few years now and I\'m curious what others think about this.',
            'Just getting into bluegrass and looking for some guidance from more experienced pickers.',
            'I heard a great version of this at a jam last weekend and it got me thinking.',
            'Been woodshedding on this for a while and wanted to share some thoughts and hear your opinions.',
            'I know there are different approaches to this. What works best for you all?',
            'This came up around the campfire at a festival recently and I thought it would make a good discussion.',
            'I\'ve tried a few different approaches but haven\'t found one that really clicks yet.',
            'Would love to hear from fiddle, banjo, and guitar players on this topic.',
            'I think this is something a lot of us struggle with, especially when starting out at jams.',
            'Curious to hear how different pickers approach this in their own playing.',
        ];

        return [
            'user_id' => User::factory(),
            'tune_id' => null,
            'title' => fake()->randomElement($topics),
            'body' => fake()->randomElement($bodies),
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
