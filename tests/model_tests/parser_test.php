<?php
require_once __DIR__ . '/test_helper.php';
require_once __DIR__ . '/../../controllers/ParserController.php';

// ── splitIntoBlocks ─────────────────────────────────────────────────────────
log_section('splitIntoBlocks');

$abc = "X:1\nT:Tune One\nK:G\nABCD|\n\nX:2\nT:Tune Two\nK:D\nEFGA|";
$blocks = ParserController::splitIntoBlocks($abc);
log_data('Block count', count($blocks));
assert_equals('Splits into 2 blocks', 2, count($blocks));
assert_true('Block 1 starts with X:1', str_starts_with($blocks[0], 'X:1'));
assert_true('Block 2 starts with X:2', str_starts_with($blocks[1], 'X:2'));

// ── parseBlock — basic tune ─────────────────────────────────────────────────
log_section('parseBlock — basic tune');

$block = "X:1\nT:The Kesh\nR:Jig\nM:6/8\nL:1/8\nQ:127\nK:Gmaj\nGAG GAB|ABA ABd|";
$parsed = ParserController::parseBlock($block);
log_data('Parsed', $parsed);
assert_not_null('Returns result', $parsed);
assert_equals('X number', 1, $parsed['x']);
assert_equals('Name', 'The Kesh', $parsed['name']);
assert_equals('Type', 'Jig', $parsed['type']);
assert_equals('Time signature', '6/8', $parsed['time_signature']);
assert_equals('Note length', '1/8', $parsed['default_note_length']);
assert_equals('Key', 'Gmaj', $parsed['key_signature']);
assert_equals('Tempo', '127', $parsed['tempo']);
assert_true('Body contains notes', str_contains($parsed['abc_transcription'], 'GAG GAB'));

// ── parseBlock — metadata fields ────────────────────────────────────────────
log_section('parseBlock — metadata');

$block = "X:1\nT:Test Tune\nS:O'Neill's\nO:Ireland\nH:Old tune\nB:Book 1\nD:CD 1\nZ:Tester\nA:Galway\nP:AABB\nK:D\nDEFG|";
$parsed = ParserController::parseBlock($block);
assert_equals('Source', "O'Neill's", $parsed['source']);
assert_equals('Origin', 'Ireland', $parsed['origin']);
assert_equals('History', 'Old tune', $parsed['history']);
assert_equals('Book', 'Book 1', $parsed['book']);
assert_equals('Discography', 'CD 1', $parsed['discography']);
assert_equals('Transcription credit', 'Tester', $parsed['transcription_credit']);
assert_equals('Area', 'Galway', $parsed['area']);
assert_equals('Parts', 'AABB', $parsed['parts']);

// ── parseBlock — C and C| time signatures ───────────────────────────────────
log_section('parseBlock — time sig normalization');

$block1 = "X:1\nT:Test\nM:C\nK:G\nABCD|";
$block2 = "X:1\nT:Test\nM:C|\nK:G\nABCD|";
assert_equals('C becomes 4/4', '4/4', ParserController::parseBlock($block1)['time_signature']);
assert_equals('C| becomes 2/2', '2/2', ParserController::parseBlock($block2)['time_signature']);

// ── parseBlock — empty/null ─────────────────────────────────────────────────
log_section('parseBlock — empty input');

assert_null('Empty string returns null', ParserController::parseBlock(''));
assert_null('Whitespace returns null', ParserController::parseBlock('   '));

// ── parseBlock — lyrics ─────────────────────────────────────────────────────
log_section('parseBlock — lyrics');

$block = "X:1\nT:Song\nK:G\nGABc|\nW:First line\nW:Second line";
$parsed = ParserController::parseBlock($block);
assert_equals('Lyrics joined', "First line\nSecond line", $parsed['lyrics']);

// ── parse — single tune ─────────────────────────────────────────────────────
log_section('parse — single tune');

$abc = "X:1\nT:The Kesh\nR:Jig\nM:6/8\nK:Gmaj\nGAG GAB|";
$groups = ParserController::parse($abc);
assert_equals('One tune group', 1, count($groups));
assert_equals('Tune name', 'The Kesh', $groups[0]['name']);
assert_equals('Tune type', 'Jig', $groups[0]['type']);
assert_equals('One setting', 1, count($groups[0]['settings']));

// ── parse — multiple tunes ──────────────────────────────────────────────────
log_section('parse — multiple tunes');

$abc = "X:1\nT:Tune A\nR:Reel\nK:D\nABCD|\n\nX:1\nT:Tune B\nR:Jig\nK:G\nEFGA|";
$groups = ParserController::parse($abc);
assert_equals('Two tune groups', 2, count($groups));
assert_equals('First tune', 'Tune A', $groups[0]['name']);
assert_equals('Second tune', 'Tune B', $groups[1]['name']);

// ── parse — multi-setting tune (X:1 + X:2) ─────────────────────────────────
log_section('parse — multi-setting');

$abc = "X:1\nT:Old Joe Clark\nR:Reel\nM:4/4\nK:Amix\nA2AA ABAG|\n\nX:2\nT:Old Joe Clark\nK:Amaj\nA2AB cBAG|";
$groups = ParserController::parse($abc);
assert_equals('One tune group', 1, count($groups));
assert_equals('Tune name', 'Old Joe Clark', $groups[0]['name']);
assert_equals('Two settings', 2, count($groups[0]['settings']));
assert_equals('Setting 1 key', 'Amix', $groups[0]['settings'][0]['key_signature']);
assert_equals('Setting 2 key', 'Amaj', $groups[0]['settings'][1]['key_signature']);

// ── parse — X:2 without title inherits name ─────────────────────────────────
log_section('parse — X:2 inherits name');

$abc = "X:1\nT:My Tune\nK:G\nGABc|\n\nX:2\nK:D\nDEFG|";
$groups = ParserController::parse($abc);
assert_equals('One group', 1, count($groups));
assert_equals('Setting 2 inherits name', 'My Tune', $groups[0]['settings'][1]['name']);

// ── parse — numbered collection (X:001, X:002) ─────────────────────────────
log_section('parse — numbered collection');

$abc = "X:001\nT:Tune One\nK:G\nGABc|\n\nX:002\nT:Tune Two\nK:D\nDEFG|\n\nX:003\nT:Tune Three\nK:A\nABcd|";
$groups = ParserController::parse($abc);
assert_equals('Three separate tunes', 3, count($groups));
assert_equals('First', 'Tune One', $groups[0]['name']);
assert_equals('Second', 'Tune Two', $groups[1]['name']);
assert_equals('Third', 'Tune Three', $groups[2]['name']);

// ── parse — comments and directives skipped ─────────────────────────────────
log_section('parse — comments skipped');

$abc = "X:1\nT:Test\n% this is a comment\n%%directive\nK:G\n% body comment\nGABc|";
$parsed = ParserController::parseBlock($abc);
assert_true('Body does not contain comments', !str_contains($parsed['abc_transcription'], '%'));

// ── parse — alternate titles ────────��────────────────────────────────────────
log_section('parseBlock — alternate titles');

$block = "X:1\nT:I Fee'd a Lad at Michaelmas\nT:O Can You Labour Lea\nT:Can Ye Labour Lea\nR:Reel\nK:G\nGABc|";
$parsed = ParserController::parseBlock($block);
assert_equals('Primary title', "I Fee'd a Lad at Michaelmas", $parsed['name']);
assert_equals('Two alternate titles', 2, count($parsed['alternate_titles']));
assert_equals('Alt title 1', 'O Can You Labour Lea', $parsed['alternate_titles'][0]);
assert_equals('Alt title 2', 'Can Ye Labour Lea', $parsed['alternate_titles'][1]);

$block2 = "X:1\nT:Single Title\nK:G\nGABc|";
$parsed2 = ParserController::parseBlock($block2);
assert_equals('No alternate titles', 0, count($parsed2['alternate_titles']));

// ── parse — notes (N: fields) ───────────────────────────────────────────────
log_section('parseBlock — notes');

$block = "X:1\nT:Test\nN:First note\nN:Second note\nN:Third note\nK:G\nGABc|";
$parsed = ParserController::parseBlock($block);
assert_equals('Multiple N: combined', "First note\nSecond note\nThird note", $parsed['notes']);

$block2 = "X:1\nT:Test\nN:Single note\nK:G\nGABc|";
$parsed2 = ParserController::parseBlock($block2);
assert_equals('Single N: field', 'Single note', $parsed2['notes']);

$block3 = "X:1\nT:Test\nK:G\nGABc|";
$parsed3 = ParserController::parseBlock($block3);
assert_null('No N: fields returns null', $parsed3['notes']);

print_results('Parser Controller Tests');
