<?php

/**
 * Fiddler's Companion Fixer — processes ALL .abc files in this directory.
 * Strips annotation blocks, extracts titles, AKA names, and notes.
 * Overwrites each file in place with the fixed output.
 */
header('Content-Type: application/json; charset=utf-8');

$dir = __DIR__;
$files = glob($dir.'/*.abc');

if (empty($files)) {
    echo json_encode(['error' => 'No .abc files found in directory.']);
    exit;
}

$results = [];
$totalTunes = 0;

foreach ($files as $filePath) {
    $fileName = basename($filePath);

    // Skip already-fixed files
    if (preg_match('/_fixed\.abc$/', $fileName)) {
        continue;
    }

    $raw = file_get_contents($filePath);
    if (! mb_check_encoding($raw, 'UTF-8')) {
        $raw = mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
    }

    $content = fix($raw);
    $tuneCount = preg_match_all('/^\s*X:\s*\d+/m', $content);
    $totalTunes += $tuneCount;

    // Save to fixed/ subdirectory — never modify the original
    $fixedDir = $dir.'/fixed';
    if (! is_dir($fixedDir)) {
        mkdir($fixedDir, 0755, true);
    }
    $outputName = preg_replace('/\.abc$/', '_fixed.abc', $fileName);
    if ($tuneCount > 0) {
        file_put_contents($fixedDir.'/'.$outputName, $content);
        $results[] = ['file' => 'fixed/'.$outputName, 'tune_count' => $tuneCount, 'status' => 'fixed'];
    } else {
        $results[] = ['file' => $fileName, 'tune_count' => 0, 'status' => 'skipped (0 tunes)'];
    }
}

echo json_encode([
    'message' => 'Fixed '.count($results).' file(s).',
    'total_tunes' => $totalTunes,
    'files' => $results,
], JSON_INVALID_UTF8_SUBSTITUTE);

// ═══════════════════════════════════════════════════════════════════════════
// Functions
// ═══════════════════════════════════════════════════════════════════════════

/**
 * Main fixer — normalize blank lines, then process each chunk.
 */
function fix(string $raw): string
{
    $raw = preg_replace('/\r\n?/', "\n", $raw);
    // Normalize smart quotes and dashes
    $raw = str_replace(["\xe2\x80\x9c", "\xe2\x80\x9d", "\xc2\xab", "\xc2\xbb"], '"', $raw);
    $raw = str_replace(["\xe2\x80\x98", "\xe2\x80\x99"], "'", $raw);
    $raw = str_replace(["\xe2\x80\x93", "\xe2\x80\x94"], '-', $raw);
    // Collapse 2+ blank lines into one
    $raw = preg_replace('/\n{3,}/', "\n\n", $raw);

    // Split into chunks by blank lines
    $chunks = preg_split('/\n\n+/', $raw);
    $output = [];

    foreach ($chunks as $chunk) {
        $chunk = trim($chunk);
        if ($chunk === '') {
            continue;
        }

        $title = findAllCapsTitle($chunk);
        if ($title !== null) {
            $output[] = $title['output'];
        } else {
            // ABC content — keep as-is but strip empty lines
            $lines = array_filter(array_map('trim', explode("\n", $chunk)), fn ($l) => $l !== '');
            $output[] = implode("\n", $lines);
        }
    }

    // Join with single blank line between chunks, then clean up:
    // Only allow a blank line before X: or T: lines (tune starts)
    $result = implode("\n\n", $output)."\n";

    // Remove all blank lines
    $result = preg_replace('/\n{2,}/', "\n", $result);

    // Add a blank line before each X: line (tune boundary)
    $result = preg_replace('/\n(X:\d+)/', "\n\n$1", $result);

    return ltrim($result, "\n");
}

/**
 * Look for an ALL CAPS title at the start of a chunk.
 * Returns null if not an annotation block.
 *
 * Processes sequentially:
 *   1. Find the all-caps title
 *   2. Check remaining text for a parenthetical subtitle
 *   3. Check remaining text for AKA alternate titles
 *   4. Whatever's left becomes N:
 */
function findAllCapsTitle(string $chunk): ?array
{
    if (preg_match('/^\s*X:\s*\d+/m', $chunk)) {
        return null;
    }
    if (preg_match('/^[A-Za-z]:/m', $chunk)) {
        return null;
    }
    if (strpos($chunk, '|') !== false) {
        return null;
    }
    if (! preg_match('/^[A-ZÀ-Ý][A-ZÀ-Ý.\s\']/u', $chunk)) {
        return null;
    }

    $lines = [];
    $text = $chunk;

    // Title chars: uppercase letters, digits, spaces, commas, apostrophes, hyphens,
    // brackets, ampersands. Periods are allowed only as initials (e.g. "A. A.")
    // — a period stays in the title only when followed by space + uppercase + another uppercase
    // (i.e. it's an initial like "A." not the end-of-title period before "Scottish, Reel").
    // Periods allowed as initials only: followed by uppercase+uppercase, but NOT " AKA"
    if (! preg_match('/^([A-ZÀ-Ý](?:[A-ZÀ-Ý0-9\s,\'\-\[\]&\/]|\.(?!\s*AKA\b)(?=\s*[A-ZÀ-Ý][A-ZÀ-Ý.]))*(?:[!?](?=\s))?)/u', $text, $m)) {
        return ['output' => 'N:'.$chunk, 'rest' => ''];
    }

    $rawTitle = rtrim(trim($m[1]), ' ,');
    $text = trim(substr($text, strlen($m[0])));
    $text = preg_replace('/^[.\s]+/', '', $text);

    // Strip [n] bracketed numbers from title
    $rawTitle = preg_replace('/\s*\[\d+\]\s*/', '', $rawTitle);
    $rawTitle = trim($rawTitle, ' ,');

    $title = cleanTitle(mb_convert_case(mb_strtolower($rawTitle), MB_CASE_TITLE));
    $lines[] = 'X:1';
    $lines[] = 'T:'.$title;

    $text = findParentheticalTitle($text, $lines);
    $text = preg_replace('/^[.\s]+/', '', $text);
    $text = findAlternateTitle($text, $lines);

    // Step 4: Extract O:, R:, K: fields from the remaining text
    $text = extractFields($text, $lines);

    // Step 5: Whatever's left becomes N:
    if (! empty($text)) {
        $lines[] = 'N:'.$text;
    }

    return ['output' => implode("\n", $lines), 'rest' => $text];
}

/**
 * Check if the text starts with a parenthetical subtitle.
 */
function findParentheticalTitle(string $text, array &$lines): string
{
    if (! preg_match('/^\(([^)]+)\)\s*(.*)$/s', $text, $m)) {
        return $text;
    }

    $subtitle = trim($m[1]);
    $rest = trim($m[2]);

    if (preg_match('/^[A-Z]/', $subtitle)) {
        $lines[] = 'T:'.cleanTitle($subtitle);

        return $rest;
    }

    return $text;
}

/**
 * Clean a title string — strip trailing commas and orphaned closing brackets.
 */
function cleanTitle(string $title): string
{
    // Strip trailing commas
    $title = rtrim($title, ', ');
    // Remove closing bracket only if there's no matching opening bracket
    if (substr_count($title, ']') > substr_count($title, '[')) {
        $title = preg_replace('/\](?=[^\[]*$)/', '', $title);
        $title = rtrim($title, ', ');
    }
    if (substr_count($title, ')') > substr_count($title, '(')) {
        $title = preg_replace('/\)(?=[^\(]*$)/', '', $title);
        $title = rtrim($title, ', ');
    }

    return trim($title);
}

/**
 * Search for AKA references and extract alternate titles.
 */
function findAlternateTitle(string $text, array &$lines): string
{
    if (! preg_match('/^AKA\b/i', $text)) {
        return $text;
    }

    $after = preg_replace('/^AKA\s*[\-]*\s*(?:and\s+)?(?:see\s+)?/i', '', $text);

    $akaPortion = $after;
    $remainder = '';
    if (preg_match('/^(.*?"[.\s,]*)\s+([A-Z][a-z].*)$/s', $after, $splitM)) {
        $akaPortion = $splitM[1];
        $remainder = $splitM[2];
    }

    if (preg_match_all('/"([^"]+)"/', $akaPortion, $quoted)) {
        foreach ($quoted[1] as $altName) {
            $altName = trim($altName, " .\t\n\r");
            $altName = preg_replace('/\s*\[\d+\]\s*$/', '', $altName);
            if (! empty($altName)) {
                $lines[] = 'T:'.cleanTitle($altName);
            }
        }

        return trim($remainder);
    }

    return trim($after);
}

/**
 * Load a text file from the fiddlers_companion directory.
 * Supports plain lists (one per line) and pipe-delimited maps (key|value).
 * Returns ['items' => [...], 'map' => [...]] sorted longest-key-first.
 */
function loadList(string $filename): array
{
    static $cache = [];
    if (isset($cache[$filename])) {
        return $cache[$filename];
    }

    $path = __DIR__.'/'.$filename;
    if (! file_exists($path)) {
        return ['items' => [], 'map' => []];
    }

    $items = [];
    $map = [];
    foreach (file($path) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        if (strpos($line, '|') !== false) {
            [$key, $value] = explode('|', $line, 2);
            $items[] = trim($key);
            $map[trim($key)] = trim($value);
        } else {
            $items[] = $line;
        }
    }
    // Sort longest first so "French-Canadian" matches before "French"
    usort($items, fn ($a, $b) => strlen($b) - strlen($a));

    $cache[$filename] = ['items' => $items, 'map' => $map];

    return $cache[$filename];
}

/**
 * Extract O: (origin), R: (tune type), K: (key) fields from annotation text.
 * The annotation typically starts with: "Irish, Jig. G Major. Standard tuning..."
 * Recognized values come from origins.txt, tune_types.txt, keys.txt.
 * Anything extracted is removed from the text. Remainder is returned.
 */
function extractFields(string $text, array &$lines): string
{
    if (empty($text)) {
        return '';
    }

    $originsData = loadList('origins.txt');
    $tuneTypesData = loadList('tune_types.txt');
    $keysData = loadList('keys.txt');

    $foundOrigins = [];
    $foundType = null;
    $foundKey = null;

    $remaining = $text;

    // Extract origins (there can be multiple: "Irish, Scottish; Jig")
    foreach ($originsData['items'] as $origin) {
        if (preg_match('/\b'.preg_quote($origin, '/').'\b/i', $remaining)) {
            $country = $originsData['map'][$origin] ?? $origin;
            if (! in_array($country, $foundOrigins)) {
                $foundOrigins[] = $country;
            }
            $remaining = preg_replace('/\b'.preg_quote($origin, '/').'\b[,;\s]*/i', '', $remaining, 1);
        }
    }

    // Extract tune type
    foreach ($tuneTypesData['items'] as $type) {
        if (preg_match('/\b'.preg_quote($type, '/').'\b/i', $remaining)) {
            $foundType = $type;
            $remaining = preg_replace('/\b'.preg_quote($type, '/').'\b[.,;\s]*/i', '', $remaining, 1);
            break;
        }
    }

    // Extract key
    foreach ($keysData['items'] as $key) {
        if (preg_match('/\b'.preg_quote($key, '/').'\b/i', $remaining)) {
            $foundKey = $key;
            $remaining = preg_replace('/\b'.preg_quote($key, '/').'\b[.,;\s]*/i', '', $remaining, 1);
            break;
        }
    }

    // Add found fields to output
    foreach ($foundOrigins as $o) {
        $lines[] = 'O:'.$o;
    }
    if ($foundType) {
        $lines[] = 'R:'.$foundType;
    }
    if ($foundKey) {
        // Convert key to ABC format: "B Flat Major" → "K:Bb Major", "F Sharp Minor" → "K:F# Minor"
        $abcKey = $foundKey;
        $abcKey = preg_replace('/\s*Flat\b/i', 'b', $abcKey);
        $abcKey = preg_replace('/\s*Sharp\b/i', '#', $abcKey);
        $lines[] = 'K:'.$abcKey;
    }

    // Clean up remaining text — remove leading punctuation and whitespace
    $remaining = preg_replace('/^[.,;\s]+/', '', $remaining);

    return trim($remaining);
}
