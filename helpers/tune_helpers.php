<?php
function formatAbcBody($abcBody, $timeSignature, $default_note_length = '1/8', $debug_tune_name = '') {
    // Clean up input
    $abcBody = preg_replace('/\|\\\\\n/', '|', $abcBody);  // strip |\ continuations
    $abcBody = preg_replace('/\\\\\n/', ' ', $abcBody);     // strip \ continuations
    $abcBody = preg_replace('/\n\s*\n/', "\n", $abcBody);  // remove blank lines
    $abcBody = trim($abcBody);

    // Normalise all ending markers to [1 and [2
    $abcBody = str_replace('|1', '|[1', $abcBody);
    $abcBody = str_replace('|2', '|[2', $abcBody);

    // beatsPerMeasure in eighth notes — do NOT scale by unit length
    $beatsPerMeasure = 8;
    if (preg_match('/^(\d+)\/(\d+)$/', $timeSignature, $m)) {
        $beatsPerMeasure = (int)$m[1] * (8 / (int)$m[2]);
        // 3/4 = 3 * (8/4) = 6 eighth notes
        // 4/4 = 4 * (8/4) = 8 eighth notes
        // 6/8 = 6 * (8/8) = 6 eighth notes
    }

    // Protect [1 and [2 so they don't get eaten by the splitter
    $abcBody = str_replace('[2', 'SECONDENDING', $abcBody);
    $abcBody = str_replace('[1', 'FIRSTENDING', $abcBody);

    // Split on barlines, keeping the delimiters
    $parts = preg_split('/(\|\|:|\|\||:\|:|\|2|\|1|::|:\||\|\]|\|:|\|)/', $abcBody, -1, PREG_SPLIT_DELIM_CAPTURE);

    // Pair content with its following barline, restoring ending markers
    $bars = [];
    $pendingPrefixBarline = '';
    for ($i = 0; $i < count($parts); $i += 2) {
        $content = trim($parts[$i]);
        $barline = isset($parts[$i + 1]) ? $parts[$i + 1] : '';
        $content = str_replace('SECONDENDING', '[2', $content);
        $content = str_replace('FIRSTENDING', '[1', $content);

        if ($content === '' && in_array($barline, ['|:', '||:'], true)) {
            $pendingPrefixBarline = $barline;
            continue;
        }

        if ($pendingPrefixBarline !== '' && $content !== '') {
            $content = $pendingPrefixBarline . $content;
            $pendingPrefixBarline = '';
        }

        if ($content !== '') {
            $bars[] = ['content' => $content, 'barline' => $barline];
        }
    }

    if ($pendingPrefixBarline !== '') {
        $bars[] = ['content' => $pendingPrefixBarline, 'barline' => ''];
    }

    if (empty($bars)) return $abcBody;

    // Detect anacrusis
    $firstBarBeats = countBeats($bars[0]['content'], $default_note_length);
    $isAnacrusis   = ($firstBarBeats < $beatsPerMeasure * 0.75);

    $lines       = [];
    $currentLine = '';
    $barCount    = 0;

    $repeatBarlines = ['||:', ':|:', '::', ':|', '|:'];
    $startRepeatBarlines = ['||:', '|:'];
    $firstEndingBarlines = ['|1', '[1'];
    $secondEndingBarlines = ['[2', '|2'];

    $flushCurrentLine = function ($carryStartRepeatForward = true) use (&$lines, &$currentLine, &$barCount) {
        $line = trim($currentLine);

        if ($line === '') {
            $currentLine = '';
            $barCount = 0;
            return;
        }

        $carryForward = '';
        if ($carryStartRepeatForward) {
            if (preg_match('/^(.*?)(\|\|:|\|:)$/', $line, $matches)) {
                $line = trim($matches[1]);
                $carryForward = $matches[2];
            } elseif (preg_match('/^(.*?)(::|:\|:)$/', $line, $matches)) {
                $line = trim($matches[1]) . ':|';
                $carryForward = ':';
            }
        }

        if ($line === ':' && $carryForward === '') {
            $currentLine = ':';
            $barCount = 0;
            return;
        }

        if ($line !== '') {
            $lines[] = $line;
        }

        $currentLine = $carryForward;
        $barCount = 0;
    };
    
    $lines        = [];
    $currentLine  = '';
    $barCount     = 0;
    $inFirstEnding = false;
    $secondEndingLength = 0;
    
    // Pre-calculate second ending lengths
    // Find each [2 or |2 and count bars until the next || or |] or :|
    $secondEndingSizes = [];
    for ($i = 0; $i < count($bars); $i++) {
        if (in_array(trim($bars[$i]['barline']), $firstEndingBarlines) || str_starts_with($bars[$i]['content'], '[1')) {
            // Look ahead to find the second ending
            $size = 0;
            for ($j = $i + 1; $j < count($bars); $j++) {
                //if (in_array(trim($bars[$j]['barline']), $secondEndingBarlines) || str_starts_with($bars[$j]['content'], '[2')) {
                if (in_array(trim($bars[$j]['barline']), $secondEndingBarlines) || str_starts_with($bars[$j]['content'], '[2')) {    
                    // Now count bars in second ending — start from j (the second ending bar itself)
                    for ($k = $j; $k < count($bars); $k++) {
                        $size++;
                        $bl = trim($bars[$k]['barline']);
                        if (in_array($bl, ['||', '|]', ':|', ':|:']) || $bl === '') break;
                    }
                    break;
                }
            }
            $secondEndingSizes[$i] = $size;
        }
    }
    
    // DEBUG
    /*file_put_contents('C:/xampp/htdocs/tuneopedia/debug_abc.txt',
        "Tune: $debug_tune_name secondEndingSizes: " . print_r($secondEndingSizes, true) . "\n",
        FILE_APPEND
    );*/

    foreach ($bars as $index => $bar) {
        $nextBar   = isset($bars[$index + 1]) ? $bars[$index + 1] : null;
        $thisBeats = countBeats($bar['content'], $default_note_length);
        $nextBeats = $nextBar ? countBeats($nextBar['content'], $default_note_length) : 0;
    
        $isAnacrusisBar      = ($thisBeats < $beatsPerMeasure * 0.75) && ($nextBeats >= $beatsPerMeasure * 0.75);
        $isRepeatBarline     = in_array(trim($bar['barline']), $repeatBarlines);
        $isStartRepeatBarline = in_array(trim($bar['barline']), $startRepeatBarlines);
        $isFirstEndingStart  = in_array(trim($bar['barline']), ['|1']) || str_starts_with($bar['content'], '[1');
        $isSecondEndingStart = in_array(trim($bar['barline']), ['|2']) || str_starts_with($bar['content'], '[2');
    
        if ($isAnacrusisBar && trim($currentLine) !== '') {
            $flushCurrentLine();
        }

        
    
        $currentLine .= $bar['content'];
    
        if (!empty($bar['barline'])) {
            $currentLine .= $bar['barline'];
    
            if (!$isAnacrusisBar && !$isSecondEndingStart) {
                $barCount++;
            }
    
            if ($isFirstEndingStart) {
                // Look up how long the second ending is
                $secondEndingLength = $secondEndingSizes[$index] ?? 0;
                $inFirstEnding = true;
            }
    
            if ($isRepeatBarline && $inFirstEnding) {
                // Don't break here if second ending fits on this line
                if ($secondEndingLength >= 3) {
                    // Second ending too long — break here, it gets its own line
                    $flushCurrentLine();
                    $inFirstEnding = false;
                }
            // Otherwise don't break — let second ending bars append
            } elseif ($isStartRepeatBarline && $isAnacrusisBar) {
                // Keep a pickup bar and its opening repeat together on the first line.
            } elseif ($isRepeatBarline || $barCount === 4) {
                $flushCurrentLine();
                $inFirstEnding = false;
            }
    
            // After second ending ends, always break
            $bl = trim($bar['barline']);
            if ($inFirstEnding && in_array($bl, ['||', '|]', ':|:', ':|']) && ($isSecondEndingStart || !$isFirstEndingStart)) {
                $flushCurrentLine();
                $inFirstEnding = false;
            }
        }
    }
    
    if (trim($currentLine) !== '') {
        $flushCurrentLine();
        if (trim($currentLine) !== '') {
            $lines[] = trim($currentLine);
        }
    }
    
    return implode("\n", $lines);
}
// DETECT ANACRUSIS — count note units in first bar
function countBeats($content, $default_note_length = '1/8') {
    // How many eighth notes is one L: unit worth?
    $multiplier = 1; // default L:1/8 = 1 eighth note
    if (preg_match('/(\d+)\/(\d+)/', $default_note_length, $m)) {
        $multiplier = ((int)$m[1] / (int)$m[2]) / (1/8);
        // L:1/4 -> (1/4) / (1/8) = 2  (one quarter note = 2 eighth notes)
        // L:1/8 -> (1/8) / (1/8) = 1
    }

    $content = preg_replace('/\(\d+/', '', $content);   // strip tuplet markers
    $content = preg_replace('/\{[^}]*\}/', '', $content); // grace notes
    $content = preg_replace('/\[[^\]]*\]/', '', $content); // chords
    $content = preg_replace('/[!+~HLMOPSTuv]/', '', $content); // decorations

    preg_match_all('/[a-gA-GzZ][,\']*(\d*)(\/?(\d*))/', $content, $matches, PREG_SET_ORDER);
    $beats = 0;
    foreach ($matches as $note) {
        $num   = $note[1] !== '' ? (int)$note[1] : 1;
        $slash = $note[2];
        if ($slash === '/') {
            $denom = 2;
        } elseif (preg_match('/\/(\d+)/', $slash, $dm)) {
            $denom = (int)$dm[1];
        } else {
            $denom = 1;
        }
        $beats += ($num / $denom) * $multiplier;
    }
    return $beats;
}

//determines if a html select option is selected
function selectOption($array, $index, $value){
    
    if ($array[$index] == $value){
        echo "selected='selected'";
    }    
}
?>
