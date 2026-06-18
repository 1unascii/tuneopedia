<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Tune;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds collections using tunes already in the database (from TuneSeeder).
 * Creates real, meaningful collections grouped by tune type, key, or origin.
 * Assigns random tunes from the DB to each collection through the pivot table.
 */
class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('collection_tunes')->truncate();
        DB::table('collections')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $user = User::first();
        if (! $user) {
            $this->command->warn('No users found. Run UserSeeder first.');
            return;
        }

        $allTunes = Tune::with('tuneType')->get();
        if ($allTunes->isEmpty()) {
            $this->command->warn('No tunes found. Run TuneSeeder first.');
            return;
        }

        // Group tunes by tune type for type-based collections
        $byType = $allTunes->groupBy(fn ($t) => $t->tuneType?->name);

        $collections = [
            // Type-based collections — pick tunes of that type
            [
                'name' => 'Session Reels',
                'author' => 'Tuneopedia',
                'description' => 'A collection of reels commonly played at sessions.',
                'is_shared' => true,
                'type' => 'Reel',
                'limit' => 20,
            ],
            [
                'name' => 'Jig Set',
                'author' => 'Tuneopedia',
                'description' => 'Jigs for all occasions.',
                'is_shared' => true,
                'type' => 'Jig',
                'limit' => 15,
            ],
            [
                'name' => 'Hornpipe Collection',
                'author' => 'Tuneopedia',
                'description' => 'A selection of hornpipes from various traditions.',
                'is_shared' => true,
                'type' => 'Hornpipe',
                'limit' => 10,
            ],
            [
                'name' => 'Waltzes & Airs',
                'author' => 'Tuneopedia',
                'description' => 'Slow tunes for listening.',
                'is_shared' => true,
                'type' => 'Waltz',
                'limit' => 10,
            ],
            [
                'name' => 'Polka Party',
                'author' => 'Tuneopedia',
                'description' => 'Polkas from Sliabh Luachra and beyond.',
                'is_shared' => true,
                'type' => 'Polka',
                'limit' => 10,
            ],
            // Mixed collections
            [
                'name' => 'Beginner\'s Tunebook',
                'author' => 'Tuneopedia',
                'description' => 'Essential tunes every picker should know.',
                'is_shared' => true,
                'type' => null,
                'limit' => 25,
            ],
            [
                'name' => 'My Practice List',
                'author' => null,
                'description' => 'Tunes I\'m currently working on.',
                'is_shared' => false,
                'type' => null,
                'limit' => 10,
            ],
            [
                'name' => 'Festival Favorites',
                'author' => 'Tuneopedia',
                'description' => 'Crowd-pleasers for festivals and campfire jams.',
                'is_shared' => true,
                'type' => null,
                'limit' => 20,
            ],
        ];

        foreach ($collections as $data) {
            $type = $data['type'];
            $limit = $data['limit'];
            unset($data['type'], $data['limit']);

            $collection = Collection::create([
                ...$data,
                'user_id' => $user->id,
            ]);

            // Pick tunes: filter by type if specified, otherwise random from all
            if ($type && $byType->has($type)) {
                $tunes = $byType[$type]->shuffle()->take($limit);
            } else {
                $tunes = $allTunes->shuffle()->take($limit);
            }

            // Attach tunes with position for ordering
            foreach ($tunes->values() as $position => $tune) {
                $collection->tunes()->attach($tune->id, ['position' => $position + 1]);
            }

            $this->command->info("Seeded '{$collection->name}' with {$tunes->count()} tunes.");
        }
    }
}
