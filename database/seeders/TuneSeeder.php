<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TuneSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('settings')->truncate();
        DB::table('tunes')->truncate();
        DB::table('tune_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $files = [
            base_path('abc_files/nhcountrydance.abc'),
            base_path('abc_files/cocks-northumberland-1.abc'),
            base_path('abc_files/cocks-northumberland-2.abc'),
        ];

        $blocks = [];
        foreach ($files as $filePath) {
            $content = file_get_contents($filePath);
            $fileBlocks = preg_split('/(?=^X:\s*\d+)/m', $content);
            $fileBlocks = array_filter(
                $fileBlocks,
                fn ($b) => preg_match('/^X:\s*\d+/m', $b)
            );
            $blocks = array_merge($blocks, $fileBlocks);
        }

        $tuneTypeCache = [];
        $tuneCache = [];
        $tunesInserted = 0;
        $settingsInserted = 0;

        foreach ($blocks as $block) {
            if (! preg_match('/^X:\s*\d+/m', $block)) {
                continue;
            }

            $tune = $this->parseAbcBlock($block);
            if (! $tune || ! $tune['title']) {
                continue;
            }

            // Default rhythm to Reel if not specified
            $rhythm = $tune['rhythm']
                ? ucfirst(strtolower(trim($tune['rhythm'])))
                : 'Reel';

            if (! isset($tuneTypeCache[$rhythm])) {
                $id = DB::table('tune_types')->insertGetId(['name' => $rhythm]);
                $tuneTypeCache[$rhythm] = $id;
            }
            $tuneTypeId = $tuneTypeCache[$rhythm];

            $tuneCacheKey = $tune['title'] . '|' . $tuneTypeId;
            if (isset($tuneCache[$tuneCacheKey])) {
                $tuneId = $tuneCache[$tuneCacheKey];
            } else {
                $tuneId = DB::table('tunes')->insertGetId([
                    'name' => $tune['title'],
                    'tune_type_id' => $tuneTypeId,
                    'origin' => $tune['source'] ?? null,
                    'source' => $tune['book'] ?? null,
                ]);
                $tuneCache[$tuneCacheKey] = $tuneId;
                $tunesInserted++;
            }

            DB::table('settings')->insert([
                'tune_id' => $tuneId,
                'user_id' => null,
                'name' => $tune['title'],
                'time_signature' => $tune['meter'] ?? '4/4',
                'default_note_length' => $tune['note_length'] ?? '1/8',
                'key_signature' => $tune['key'] ?? null,
                'abc_transcription' => $tune['abc_body'],
                'source' => $tune['source'] ?? null,
                'book' => $tune['book'] ?? null,
                'transcription_credit' => $tune['transcription'] ?? null,
            ]);
            $settingsInserted++;
        }

        $this->command->info(
            "Seeded {$tunesInserted} tunes with {$settingsInserted} settings "
            . 'across ' . count($tuneTypeCache) . ' tune types.'
        );
    }

    private function parseAbcBlock(string $block): array
    {
        $lines = explode("\n", $block);
        $tune = [
            'title' => null,
            'rhythm' => null,
            'meter' => null,
            'note_length' => null,
            'key' => null,
            'composer' => null,
            'source' => null,
            'book' => null,
            'transcription' => null,
            'abc_body' => '',
        ];

        $inBody = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (preg_match('/^T:\s*(.+)/', $line, $m) && ! $tune['title']) {
                $tune['title'] = trim($m[1]);
            } elseif (preg_match('/^R:\s*(.+)/', $line, $m)) {
                $tune['rhythm'] = trim($m[1]);
            } elseif (preg_match('/^M:\s*(.+)/', $line, $m)) {
                $tune['meter'] = trim($m[1]);
            } elseif (preg_match('/^L:\s*(.+)/', $line, $m)) {
                $tune['note_length'] = trim($m[1]);
            } elseif (preg_match('/^K:\s*(.+)/', $line, $m)) {
                $tune['key'] = trim($m[1]);
                $inBody = true;
            } elseif (preg_match('/^C:\s*(.+)/', $line, $m)) {
                $tune['composer'] = trim($m[1]);
            } elseif (preg_match('/^S:\s*(.+)/', $line, $m)) {
                $tune['source'] = trim($m[1]);
            } elseif (preg_match('/^B:\s*(.+)/', $line, $m)) {
                $tune['book'] = trim($m[1]);
            } elseif (preg_match('/^Z:\s*(.+)/', $line, $m)) {
                $tune['transcription'] = trim($m[1]);
            } elseif (preg_match('/^X:\s*/', $line)) {
                // Skip index line
            } elseif ($inBody && ! preg_match('/^[A-Za-z]:\s*/', $line)) {
                $tune['abc_body'] .= $line . "\n";
            }
        }

        $tune['abc_body'] = trim($tune['abc_body']);

        return $tune;
    }
}
