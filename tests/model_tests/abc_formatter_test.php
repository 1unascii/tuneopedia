<?php
require_once __DIR__ . '/test_helper.php';
require_once __DIR__ . '/../../controllers/AbcBodyParser.php';

// ── countBeats tests ──────────────────────────────────────────────────────────
log_section('countBeats');

assert_equals('Eighth notes: Bc = 2', 2.0, countBeats('Bc', '1/8'));
assert_equals('Quarter notes: BC in L:1/4 = 4', 4.0, countBeats('BC', '1/4'));
assert_equals('Triplet: (3D/E/F/ in L:1/4 = 3', 3.0, countBeats('(3D/E/F/', '1/4'));
assert_equals('Full 4/4 bar: d3d dcBA = 8', 8.0, countBeats('d3d dcBA', '1/8'));
assert_equals('Full 6/8 bar: FGA DFA = 6', 6.0, countBeats('FGA DFA', '1/8'));

// ── formatAbcBody tests ───────────────────────────────────────────────────────
log_section('formatAbcBody');

$input = 'ABCD|EFGH|IJKL|MNOP|QRST|UVWX|';
$result = formatAbcBody($input, '4/4', '1/8');
$lines = explode("\n", $result);
assert_equals('Four bars per line produces 2 lines', 2, count($lines));

$input = 'Bc|d3d dcBA|G2 B2 D3 G|E2 GE D2 B2|A6 Bc|d3d dcBA|G2 B2 D3 G|E2 cA F3 D|G6:|';
$result = formatAbcBody($input, '4/4', '1/8');
$lines = explode("\n", $result);
assert_true('Anacrusis detected: first line starts with Bc|', str_starts_with($lines[0], 'Bc|'));
assert_equals('Anacrusis line has 5 bars', 5, substr_count($lines[0], '|'));

$input = 'ABCD|EFGH|IJKL|MNOP|:QRST|UVWX|YZAB|CDEF|';
$result = formatAbcBody($input, '4/4', '1/8');
$lines = explode("\n", $result);
$startsWithRepeat = false;
foreach ($lines as $line) {
    if (str_starts_with(trim($line), '|:')) { $startsWithRepeat = true; }
}
assert_true('Repeat barline starts new line', $startsWithRepeat);

$input = "F|:ABCD|EFGH|IJKL|MNOP|QRST|UVWX|";
$result = formatAbcBody($input, '4/4', '1/8');
$lines = explode("\n", $result);
assert_true('Opening repeat stays after pickup barline', str_starts_with($lines[0], 'F|:ABCD|'));

$input = "|:ABCD|EFGH|IJKL|MNOP|QRST|";
$result = formatAbcBody($input, '4/4', '1/8');
$lines = explode("\n", $result);
assert_true('Leading opening repeat preserved at line start', str_starts_with($lines[0], '|:ABCD|'));

$input = "z|EGB EGB|EGB c2 B|EBG EBG|=DFA B2 A|EGB EGB|EGB c2 a|g2 f Bcd|e3 e2::a|gbg efg|";
$result = formatAbcBody($input, '6/8', '1/8');
$lines = explode("\n", $result);
assert_true('Double repeat: line ends with :|', str_ends_with($lines[1], ':|'));
assert_true('Double repeat: next line starts with :a|', str_starts_with($lines[2], ':a|'));

print_results('ABC Formatter Tests');
