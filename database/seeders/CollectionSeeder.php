<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Tune;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        $collections = [
            [
                'file' => 'nhcountrydance.abc',
                'name' => 'NH Country Dance Tunes',
                'author' => 'Lamprey River Band',
                'description' => 'Fiddle tunes from the New Hampshire '
                    . 'old-time and contra dance tradition.',
            ],
            [
                'file' => 'cocks-northumberland-1.abc',
                'name' => 'Cocks Northumberland 1',
                'author' => 'Village Music Project',
                'description' => 'Tunes from an anonymous early 19th century '
                    . 'manuscript in the Cocks Collection, Northumbria.',
            ],
            [
                'file' => 'cocks-northumberland-2.abc',
                'name' => 'Cocks Northumberland 2',
                'author' => 'Village Music Project',
                'description' => 'Second volume of tunes from the Cocks '
                    . 'Collection manuscript, Northumbria.',
            ],
        ];

        foreach ($collections as $data) {
            $filePath = base_path('abc_files/' . $data['file']);
            $titles = $this->getTitlesFromFile($filePath);

            if (empty($titles)) {
                $this->command->warn(
                    "No tunes found in {$data['file']}. Skipping."
                );
                continue;
            }

            $tunes = Tune::whereIn('name', $titles)->get();

            $collection = Collection::create([
                'name' => $data['name'],
                'author' => $data['author'],
                'description' => $data['description'],
                'is_shared' => true,
                'user_id' => $user->id,
            ]);

            foreach ($tunes->values() as $position => $tune) {
                $collection->tunes()->attach(
                    $tune->id,
                    ['position' => $position + 1]
                );
            }

            $this->command->info(
                "Seeded '{$collection->name}' with {$tunes->count()} tunes."
            );
        }
    }

    private function getTitlesFromFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        $titles = [];
        preg_match_all('/^T:\s*(.+)/m', $content, $matches);
        foreach ($matches[1] as $title) {
            $titles[] = trim($title);
        }

        return array_unique($titles);
    }
}
