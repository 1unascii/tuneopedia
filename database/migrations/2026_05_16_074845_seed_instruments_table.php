<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seed the instruments table with 42 instruments ordered by sort position.
     * Each instrument has a MIDI program number for playback.
     */
    public function up(): void
    {
        $instruments = [
            ['name' => 'Fiddle', 'midi_program' => 110],
            ['name' => 'Banjo', 'midi_program' => 105],
            ['name' => 'Mandolin', 'midi_program' => 25],
            ['name' => 'Guitar', 'midi_program' => 25],
            ['name' => 'Nylon Guitar', 'midi_program' => 24],
            ['name' => 'Dobro', 'midi_program' => 25],
            ['name' => 'Pedal Steel', 'midi_program' => 25],
            ['name' => 'Lap Steel', 'midi_program' => 25],
            ['name' => 'Tenor Banjo', 'midi_program' => 105],
            ['name' => 'Tenor Guitar', 'midi_program' => 25],
            ['name' => 'Bouzouki', 'midi_program' => 25],
            ['name' => 'Electric Guitar', 'midi_program' => 27],
            ['name' => 'Violin', 'midi_program' => 40],
            ['name' => 'Viola', 'midi_program' => 41],
            ['name' => 'Cello', 'midi_program' => 42],
            ['name' => 'Harp', 'midi_program' => 46],
            ['name' => 'Dulcimer', 'midi_program' => 15],
            ['name' => 'Tin Whistle', 'midi_program' => 78],
            ['name' => 'Low Whistle', 'midi_program' => 78],
            ['name' => 'Flute', 'midi_program' => 73],
            ['name' => 'Piccolo', 'midi_program' => 72],
            ['name' => 'Recorder', 'midi_program' => 74],
            ['name' => 'Clarinet', 'midi_program' => 71],
            ['name' => 'Ocarina', 'midi_program' => 79],
            ['name' => 'Uilleann Pipes', 'midi_program' => 109],
            ['name' => 'Highland Bagpipes', 'midi_program' => 109],
            ['name' => 'Harmonica', 'midi_program' => 22],
            ['name' => 'Accordion', 'midi_program' => 21],
            ['name' => 'Concertina', 'midi_program' => 21],
            ['name' => 'Piano', 'midi_program' => 0],
            ['name' => 'Harpsichord', 'midi_program' => 6],
            ['name' => 'Reed Organ', 'midi_program' => 20],
            ['name' => 'Tango Accordion', 'midi_program' => 23],
            ['name' => 'Acoustic Bass', 'midi_program' => 32],
            ['name' => 'Contrabass', 'midi_program' => 43],
            ['name' => 'Oboe', 'midi_program' => 68],
            ['name' => 'Bassoon', 'midi_program' => 70],
            ['name' => 'Pan Flute', 'midi_program' => 75],
            ['name' => 'Shakuhachi', 'midi_program' => 77],
            ['name' => 'Sitar', 'midi_program' => 104],
            ['name' => 'Koto', 'midi_program' => 107],
            ['name' => 'Kalimba', 'midi_program' => 108],
        ];

        DB::table('instruments')->insert($instruments);
    }

    /**
     * Remove all seeded instruments.
     */
    public function down(): void
    {
        DB::table('instruments')->truncate();
    }
};
