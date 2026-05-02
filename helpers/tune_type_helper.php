<?php
/**
 * Detect the tune type from available data.
 *
 * Tries in order:
 *   1. The R: field value (e.g. "Reel", "Jig")
 *   2. Keyword matching in the tune name
 *   3. Time signature mapping (4/4 → reel, 6/8 → jig, etc.)
 *   4. Falls back to "other"
 *
 * @param string $rField       The R: field value from the ABC header
 * @param string $tuneName     The tune title
 * @param string $timeSig      The M: field value (time signature)
 * @return string              Lowercase tune type name
 */
function detectTuneType(string $rField, string $tuneName, string $timeSig): string {
    // 1. Use R: field if present
    $type = strtolower(trim($rField));
    if (!empty($type)) return $type;

    // 2. Keyword matching in tune name
    $nameLower = strtolower($tuneName);
    $keywords = [
        'reel'        => 'reel',
        'jig'         => 'jig',
        'slip jig'    => 'slip jig',
        'hornpipe'    => 'hornpipe',
        'polka'       => 'polka',
        'waltz'       => 'waltz',
        'march'       => 'march',
        'strathspey'  => 'strathspey',
        'air'         => 'air',
        'lament'      => 'air',
        'barndance'   => 'barndance',
        'schottische' => 'schottische',
        'mazurka'     => 'mazurka',
        'clog'        => 'clog',
        'slide'       => 'slide',
        'planxty'     => 'planxty',
    ];
    // Check longer keywords first to avoid "jig" matching before "slip jig"
    uksort($keywords, fn($a, $b) => strlen($b) - strlen($a));
    foreach ($keywords as $keyword => $typeName) {
        if (strpos($nameLower, $keyword) !== false) return $typeName;
    }

    // 3. Time signature mapping
    $timeSigMap = [
        '4/4'  => 'reel',
        '2/4'  => 'reel',
        '2/2'  => 'reel',
        '6/8'  => 'jig',
        '9/8'  => 'slip jig',
        '12/8' => 'slide',
        '3/4'  => 'waltz',
        '3/8'  => 'waltz',
    ];
    if (isset($timeSigMap[$timeSig])) return $timeSigMap[$timeSig];

    // 4. Fallback
    return 'other';
}
