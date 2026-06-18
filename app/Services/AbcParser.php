<?php

namespace App\Services;

/**
 * ABC Notation Parser
 * ===================
 * Parses raw ABC notation text (from file or paste) into structured tune data.
 *
 * ABC format reference:
 *   X: — reference number (tune index)
 *   T: — title (multiple T: lines = alternate titles)
 *   R: — rhythm / tune type (e.g. Reel, Jig)
 *   M: — time signature (e.g. 4/4, 6/8)
 *   L: — default note length (e.g. 1/8)
 *   K: — key signature (marks end of header, body follows)
 *   Q: — tempo
 *   S: — source
 *   O: — origin / geographic area
 *   B: — book
 *   Z: — transcription credit
 *   N: — notes
 *   H: — history
 *   C: — composer
 *   D: — discography
 *   A: — area
 *   P: — parts
 *   W: — lyrics (in body section)
 *
 * Usage:
 *   $parser = new AbcParser();
 *   $tunes = $parser->parse($rawAbcText);
 *   // Returns array of tune arrays, each with header fields and abc_body
 */
class AbcParser
{
    /**
     * Parse raw ABC text into an array of tune data.
     *
     * Splits the text on X: fields, parses each block's headers,
     * and returns structured data for each tune.
     *
     * @return array<int, array{
     *   x: int,
     *   name: string|null,
     *   alternate_titles: string[],
     *   type: string|null,
     *   time_signature: string|null,
     *   default_note_length: string|null,
     *   key_signature: string|null,
     *   abc_body: string,
     *   tempo: string|null,
     *   composer: string|null,
     *   source: string|null,
     *   origin: string|null,
     *   book: string|null,
     *   transcription_credit: string|null,
     *   notes: string|null,
     *   history: string|null,
     *   discography: string|null,
     *   area: string|null,
     *   parts: string|null,
     *   lyrics: string|null,
     * }>
     */
    public function parse(string $rawAbc): array
    {
        $blocks = $this->splitIntoBlocks($rawAbc);
        $tunes = [];

        foreach ($blocks as $block) {
            $parsed = $this->parseBlock($block);
            if ($parsed && $parsed['name'] && $parsed['abc_body']) {
                $tunes[] = $parsed;
            }
        }

        return $tunes;
    }

    /**
     * Split raw ABC text into individual tune blocks.
     *
     * Each block starts with an X: line. Uses regex lookahead
     * to split without consuming the X: line itself.
     */
    private function splitIntoBlocks(string $rawAbc): array
    {
        // Split on X: lines (each tune starts with X: followed by a number)
        $blocks = preg_split('/(?=^X:\s*\d+)/m', $rawAbc);

        // Filter out blocks that don't start with X:
        return array_values(array_filter($blocks, function ($block) {
            return preg_match('/^X:\s*\d+/m', $block);
        }));
    }

    /**
     * Parse a single ABC block into structured tune data.
     *
     * Reads header fields line by line until K: is found (key signature),
     * then everything after K: is treated as the ABC body (notation).
     * Lines starting with % are comments and are skipped.
     * W: lines in the body are extracted as lyrics.
     */
    private function parseBlock(string $block): ?array
    {
        $lines = explode("\n", $block);
        $tune = [
            'x' => null,
            'name' => null,
            'alternate_titles' => [],
            'type' => null,
            'time_signature' => null,
            'default_note_length' => null,
            'key_signature' => null,
            'abc_body' => '',
            'tempo' => null,
            'composer' => null,
            'source' => null,
            'origin' => null,
            'book' => null,
            'transcription_credit' => null,
            'notes' => null,
            'history' => null,
            'discography' => null,
            'area' => null,
            'parts' => null,
            'lyrics' => null,
        ];

        $inBody = false;
        $notes = [];
        $lyrics = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and comments
            if ($line === '' || str_starts_with($line, '%')) {
                continue;
            }

            if ($inBody) {
                // In the body: collect notation, extract W: lyrics
                if (preg_match('/^W:\s*(.+)/', $line, $m)) {
                    $lyrics[] = trim($m[1]);
                } elseif (! preg_match('/^[A-Za-z]:\s*/', $line)) {
                    $tune['abc_body'] .= $line . "\n";
                }
                continue;
            }

            // Header parsing
            if (preg_match('/^X:\s*(\d+)/', $line, $m)) {
                $tune['x'] = (int) $m[1];
            } elseif (preg_match('/^T:\s*(.+)/', $line, $m)) {
                $title = trim($m[1]);
                if (! $tune['name']) {
                    $tune['name'] = $title;
                } else {
                    $tune['alternate_titles'][] = $title;
                }
            } elseif (preg_match('/^R:\s*(.+)/', $line, $m)) {
                $tune['type'] = trim($m[1]);
            } elseif (preg_match('/^M:\s*(.+)/', $line, $m)) {
                $tune['time_signature'] = trim($m[1]);
            } elseif (preg_match('/^L:\s*(.+)/', $line, $m)) {
                $tune['default_note_length'] = trim($m[1]);
            } elseif (preg_match('/^K:\s*(.+)/', $line, $m)) {
                $tune['key_signature'] = trim($m[1]);
                $inBody = true; // Everything after K: is notation
            } elseif (preg_match('/^Q:\s*(.+)/', $line, $m)) {
                $tune['tempo'] = trim($m[1]);
            } elseif (preg_match('/^C:\s*(.+)/', $line, $m)) {
                $tune['composer'] = trim($m[1]);
            } elseif (preg_match('/^S:\s*(.+)/', $line, $m)) {
                $tune['source'] = trim($m[1]);
            } elseif (preg_match('/^O:\s*(.+)/', $line, $m)) {
                $tune['origin'] = trim($m[1]);
            } elseif (preg_match('/^B:\s*(.+)/', $line, $m)) {
                $tune['book'] = trim($m[1]);
            } elseif (preg_match('/^Z:\s*(.+)/', $line, $m)) {
                $tune['transcription_credit'] = trim($m[1]);
            } elseif (preg_match('/^N:\s*(.+)/', $line, $m)) {
                $notes[] = trim($m[1]);
            } elseif (preg_match('/^H:\s*(.+)/', $line, $m)) {
                $tune['history'] = trim($m[1]);
            } elseif (preg_match('/^D:\s*(.+)/', $line, $m)) {
                $tune['discography'] = trim($m[1]);
            } elseif (preg_match('/^A:\s*(.+)/', $line, $m)) {
                $tune['area'] = trim($m[1]);
            } elseif (preg_match('/^P:\s*(.+)/', $line, $m)) {
                $tune['parts'] = trim($m[1]);
            }
        }

        $tune['abc_body'] = trim($tune['abc_body']);
        $tune['notes'] = $notes ? implode("\n", $notes) : null;
        $tune['lyrics'] = $lyrics ? implode("\n", $lyrics) : null;

        return $tune;
    }
}
