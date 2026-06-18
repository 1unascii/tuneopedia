<?php
/**
 * ParserController — Simple, testable ABC parser.
 *
 * Parses raw ABC text into an array of structured tune/setting objects.
 * Rules:
 *   - X: starts a new tune
 *   - X:1 is the first setting, X:2, X:3, etc. are additional settings
 *     of the SAME tune (they share the preceding X:1's title)
 *   - Standard ABC header fields are extracted: T, M, L, K, R, Q, etc.
 *   - Everything after K: until the next header block or end is the ABC body
 *
 * No database calls. No HTML. Pure parsing only.
 */

class ParserController {

    /**
     * Parse raw ABC text into an array of tune groups.
     *
     * Each group represents one tune with one or more settings (transcriptions).
     * A "setting" is an alternate version of the same tune — e.g. a different key
     * or ornamentation. In ABC files, settings are indicated by sequential X: numbers
     * (X:1, X:2, X:3) with the same or no title.
     *
     * Numbered collections (X:001, X:002, X:003 with different titles) are treated
     * as separate tunes, not settings.
     *
     * @param string $abcText Raw ABC text containing one or more tunes
     * @return array Array of tune groups, each containing:
     *   - 'name'     string  The tune title (from T: field)
     *   - 'type'     string  The tune type (from R: field, e.g. "Jig", "Reel")
     *   - 'settings' array   One or more parsed setting arrays
     */
    public static function parse(string $abcText): array {
        $blocks = self::splitIntoBlocks($abcText);
        $tuneGroups = [];
        $currentGroup = null;
        $lastX = 0; // Track the previous block's X: number for sequential detection

        foreach ($blocks as $block) {
            $parsed = self::parseBlock($block);
            if ($parsed === null) continue;

            // Detect additional settings vs new tunes:
            // An additional setting must satisfy ALL of:
            //   1. X: number is > 1
            //   2. X: number is exactly one more than the previous (sequential: 1→2→3)
            //   3. There is a current tune group to attach to
            //   4. Title is empty (inherits from X:1) or matches the current tune's title
            // This distinguishes "X:1 Tune A / X:2 Tune A" (settings) from
            // "X:001 Tune A / X:002 Tune B" (numbered collection with different tunes)
            $isAdditionalSetting = (
                $parsed['x'] > 1
                && $parsed['x'] === $lastX + 1
                && $currentGroup !== null
                && (empty($parsed['name']) || $parsed['name'] === $currentGroup['name'])
            );

            if (!$isAdditionalSetting) {
                // Start a new tune group — flush the previous one first
                if ($currentGroup !== null) {
                    $tuneGroups[] = $currentGroup;
                }
                $currentGroup = [
                    'name'     => $parsed['name'],
                    'type'     => $parsed['type'],
                    'settings' => [$parsed],
                ];
            } else {
                // Attach as an additional setting to the current tune
                // If the setting has no title, inherit from the parent tune
                if (empty($parsed['name'])) {
                    $parsed['name'] = $currentGroup['name'];
                }
                $currentGroup['settings'][] = $parsed;
            }

            $lastX = $parsed['x'];
        }

        // Don't forget the last group
        if ($currentGroup !== null) {
            $tuneGroups[] = $currentGroup;
        }

        return $tuneGroups;
    }

    /**
     * Split raw ABC text into blocks, one per X: field.
     *
     * Uses a lookahead regex to split at each "X:" followed by a number,
     * which is the ABC standard for the start of a new tune/setting.
     * Empty blocks (blank lines between tunes) are filtered out.
     *
     * @param string $abcText Raw ABC text
     * @return array Array of trimmed, non-empty ABC blocks
     */
    public static function splitIntoBlocks(string $abcText): array {
        $blocks = preg_split('/(?=^\s*X:\s*\d+)/m', $abcText, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_filter(array_map('trim', $blocks)));
    }

    /**
     * Parse a single ABC block (one X: section) into a structured array.
     *
     * ABC header fields (before K:) are extracted into named keys.
     * The K: (key) field marks the transition to the tune body.
     * Everything after K: is collected as the ABC transcription,
     * except W: lines (lyrics) and info field lines (letter + colon).
     *
     * Standard ABC header fields:
     *   X: reference number    T: title           R: rhythm/type
     *   M: metre (time sig)    L: default length   K: key signature
     *   Q: tempo               S: source           O: origin
     *   H: history             B: book             D: discography
     *   Z: transcription       A: area             P: parts
     *   W: lyrics (in body)
     *
     * @param string $block A single ABC tune block starting with X:
     * @return array|null Parsed setting data, or null if block has no content
     */
    public static function parseBlock(string $block): ?array {
        $lines = explode("\n", $block);

        // Initialize all fields with defaults
        $x                   = 1;      // X: reference number
        $name                = '';     // T: title (first T: field)
        $alternateTitles     = [];    // T: additional titles (2nd, 3rd, etc.)
        $type                = '';     // R: rhythm/tune type
        $timeSignature       = '4/4'; // M: metre
        $defaultNoteLength   = '1/8'; // L: default note length
        $keySignature        = '';     // K: key signature
        $tempo               = null;  // Q: tempo
        $source              = null;  // S: source
        $origin              = null;  // O: origin
        $history             = null;  // H: history
        $book                = null;  // B: book reference
        $discography         = null;  // D: discography
        $transcriptionCredit = null;  // Z: transcriber
        $area                = null;  // A: area/region
        $parts               = null;  // P: parts structure
        $notes               = [];    // N: notes (multiple N: fields combined)
        $lyrics              = [];    // W: lyrics (collected in body)
        $bodyLines           = [];    // ABC notation lines
        $inBody              = false; // Flipped to true once K: is encountered

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            // Skip ABC comments (lines starting with %)
            // This includes %% directives like %%MIDI, %%staves, etc.
            if ($line[0] === '%') continue;

            // ── Header fields (before K:) ──
            // Each ABC header is a single letter + colon. K: is the last header
            // and flips $inBody to true. Fields after K: are ignored except W: (lyrics).

            // ── Fields that can appear anywhere (header or body) ──
            if (preg_match('/^N:\s*(.+)/', $line, $m)) {
                // N: notes — free-text annotations. Multiple N: fields are combined.
                $notes[] = trim($m[1]);

            // ── Header fields (before K:) ──
            } elseif (!$inBody) {
                if (preg_match('/^X:\s*(\d+)/', $line, $m)) {
                    $x = (int)$m[1];
                } elseif (preg_match('/^T:\s*(.+)/', $line, $m)) {
                    // First T: is the primary name; subsequent T: fields are alternate titles
                    $title = trim($m[1]);
                    if (empty($name)) {
                        $name = $title;
                    } else {
                        $alternateTitles[] = $title;
                    }
                } elseif (preg_match('/^R:\s*(.+)/i', $line, $m)) {
                    // R: rhythm — the tune type (Reel, Jig, Hornpipe, etc.)
                    $type = trim($m[1]);
                } elseif (preg_match('/^M:\s*(.+)/', $line, $m)) {
                    // M: metre — time signature. "C" = common time (4/4), "C|" = cut time (2/2)
                    $ts = trim($m[1]);
                    if ($ts === 'C') $ts = '4/4';
                    elseif ($ts === 'C|') $ts = '2/2';
                    $timeSignature = $ts;
                } elseif (preg_match('/^L:\s*(.+)/', $line, $m)) {
                    // L: default note length — e.g. 1/8 means an undecorated "A" is an eighth note
                    $defaultNoteLength = trim($m[1]);
                } elseif (preg_match('/^Q:\s*(.+)/', $line, $m)) {
                    // Q: tempo — e.g. "1/4=120" means 120 quarter notes per minute
                    $tempo = trim($m[1]);
                } elseif (preg_match('/^S:\s*(.+)/', $line, $m)) {
                    // S: source — where the tune was collected from
                    $source = trim($m[1]);
                } elseif (preg_match('/^O:\s*(.+)/', $line, $m)) {
                    // O: origin — geographic origin (e.g. "Ireland", "Scotland")
                    $origin = trim($m[1]);
                } elseif (preg_match('/^H:\s*(.+)/', $line, $m)) {
                    // H: history — historical notes about the tune
                    $history = trim($m[1]);
                } elseif (preg_match('/^B:\s*(.+)/', $line, $m)) {
                    // B: book — published book reference
                    $book = trim($m[1]);
                } elseif (preg_match('/^D:\s*(.+)/', $line, $m)) {
                    // D: discography — recording references
                    $discography = trim($m[1]);
                } elseif (preg_match('/^Z:\s*(.+)/', $line, $m)) {
                    // Z: transcription credit — who transcribed this ABC
                    $transcriptionCredit = trim($m[1]);
                } elseif (preg_match('/^A:\s*(.+)/', $line, $m)) {
                    // A: area — specific region within the origin country
                    $area = trim($m[1]);
                } elseif (preg_match('/^P:\s*(.+)/', $line, $m)) {
                    // P: parts — part structure (e.g. "AABB", "ABAC")
                    $parts = trim($m[1]);
                } elseif (preg_match('/^K:\s*(.+)/', $line, $m)) {
                    // K: key signature — must be the last header field per ABC standard.
                    // Everything after this line is the tune body.
                    $keySignature = trim($m[1]);
                    $inBody = true;
                }

            // ── Body (after K:) ──
            } else {
                if (preg_match('/^W:\s*(.+)/', $line, $m)) {
                    // W: lyrics — can appear in the body after K:
                    $lyrics[] = trim($m[1]);
                } elseif (!preg_match('/^[A-Za-z]:/', $line)) {
                    // Not a header field — it's ABC notation (notes, barlines, etc.)
                    $bodyLines[] = $line;
                }
            }
        }

        // Skip blocks with no title and no body (e.g. whitespace-only blocks)
        if (empty($name) && empty($bodyLines)) return null;

        return [
            'x'                    => $x,
            'name'                 => $name,
            'alternate_titles'     => $alternateTitles,
            'type'                 => $type,
            'time_signature'       => $timeSignature,
            'default_note_length'  => $defaultNoteLength,
            'key_signature'        => $keySignature,
            'abc_transcription'    => implode("\n", $bodyLines),
            'tempo'                => $tempo,
            'source'               => $source,
            'origin'               => $origin,
            'history'              => $history,
            'book'                 => $book,
            'discography'          => $discography,
            'transcription_credit' => $transcriptionCredit,
            'area'                 => $area,
            'parts'                => $parts,
            'notes'                => !empty($notes) ? implode("\n", $notes) : null,
            'lyrics'               => !empty($lyrics) ? implode("\n", $lyrics) : null,
        ];
    }
}
