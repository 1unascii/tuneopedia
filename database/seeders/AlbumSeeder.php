<?php

namespace Database\Seeders;

use App\Models\Album;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AlbumSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('albums')->truncate();

        // Clean out old seeded covers
        $coverDir = public_path('images/album_covers');
        if (is_dir($coverDir)) {
            foreach (glob($coverDir . '/seed_*.png') as $file) {
                unlink($file);
            }
        }

        $albums = [
            // Old Time
            'Appalachian Stomp: Bluegrass Classics',
            'Clawhammer Banjo (Volume 1) - Wade Ward',
            'Old Time Music at Clarence Ashley\'s',
            'Hollow Poplar - Bruce Molsky',
            'Say Old Man - Dirk Powell',
            'Sail Away Ladies - Uncle Bunt Stephens',
            'Round Peak Style - Tommy Jarrell',
            'June Apple - Red Clay Ramblers',
            'The Skillet Lickers - Old Time Fiddle Tunes',
            'Foggy Mountain Special - Highwoods Stringband',

            // Bluegrass
            'Bluegrass Breakdown - Bill Monroe',
            'Foggy Mountain Banjo - Earl Scruggs',
            'Will the Circle Be Unbroken - Nitty Gritty Dirt Band',
            'Rounder Records Bluegrass Collection',
            'Old & In the Way - Jerry Garcia',
            'Appalachian Swing - Doc Watson',
            'The Essential Flatt & Scruggs',
            'Hot Rize - Traditional Ties',
            'Tony Rice - Manzanita',
            'Strength in Numbers - Telluride Sessions',

            // Irish Trad
            'The Bothy Band - 1975',
            'Planxty - The Black Album',
            'The Chieftains 4',
            'De Dannan - Selected Jigs Reels & Songs',
            'Martin Hayes - Under the Moon',
        ];

        foreach ($albums as $name) {
            $coverPath = $this->generateCover($name, $coverDir);

            Album::create([
                'name' => $name,
                'cover_art' => $coverPath,
            ]);
        }
    }

    /**
     * Generate a unique placeholder cover image with random background and album name text.
     */
    private function generateCover(string $albumName, string $dir): string
    {
        $width = 300;
        $height = 300;
        $img = imagecreatetruecolor($width, $height);

        // Random dark-ish background color
        $r = rand(30, 150);
        $g = rand(30, 150);
        $b = rand(30, 150);
        $bg = imagecolorallocate($img, $r, $g, $b);
        imagefill($img, 0, 0, $bg);

        // White text
        $textColor = imagecolorallocate($img, 255, 255, 255);

        // Word-wrap the album name to fit
        $lines = wordwrap($albumName, 20, "\n", true);
        $lineArray = explode("\n", $lines);
        $lineHeight = 16;
        $startY = ($height / 2) - (count($lineArray) * $lineHeight / 2);

        foreach ($lineArray as $i => $line) {
            $x = max(10, ($width - strlen($line) * 7) / 2);
            $y = (int) ($startY + ($i * $lineHeight));
            imagestring($img, 4, (int) $x, $y, $line, $textColor);
        }

        $filename = 'seed_' . Str::random(16) . '.png';
        $filepath = $dir . '/' . $filename;
        imagepng($img, $filepath);
        imagedestroy($img);

        return 'images/album_covers/' . $filename;
    }
}
