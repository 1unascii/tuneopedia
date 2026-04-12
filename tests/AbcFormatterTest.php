<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../functions_abc.php';

class AbcFormatterTest extends TestCase {

    // ── countBeats tests ──────────────────────────────────────────────

    public function testCountBeatsEighthNotes() {
        // 'Bc' = 2 eighth notes in L:1/8
        $this->assertEquals(2, countBeats('Bc', '1/8'));
    }

    public function testCountBeatsQuarterNotes() {
        // 'BC' in L:1/4 = 2 quarter notes = 4 eighth notes
        $this->assertEquals(4, countBeats('BC', '1/4'));
    }

    public function testCountBeatsTriplet() {
        // '(3D/E/F/' = 3 sixteenth notes in L:1/4
        $this->assertEquals(3, countBeats('(3D/E/F/', '1/4'));
    }

    public function testCountBeatsFullBar44() {
        // 'd3d dcBA' in L:1/8 = 8 eighth notes = full 4/4 bar
        $this->assertEquals(8, countBeats('d3d dcBA', '1/8'));
    }

    public function testCountBeatsFullBar68() {
        // 'FGA DFA' in L:1/8 = 6 eighth notes = full 6/8 bar
        $this->assertEquals(6, countBeats('FGA DFA', '1/8'));
    }

    // ── formatAbcBody tests ───────────────────────────────────────────

    public function testFourBarsPerLine() {
        $input = 'ABCD|EFGH|IJKL|MNOP|QRST|UVWX|';
        $result = formatAbcBody($input, '4/4', '1/8');
        $lines = explode("\n", $result);
        $this->assertCount(2, $lines);
    }

    public function testAnacrusisDetected() {
        // Bc is anacrusis in 4/4, so first line should have 5 bars
        $input = 'Bc|d3d dcBA|G2 B2 D3 G|E2 GE D2 B2|A6 Bc|d3d dcBA|G2 B2 D3 G|E2 cA F3 D|G6:|';
        $result = formatAbcBody($input, '4/4', '1/8');
        $lines = explode("\n", $result);
        // First line should start with Bc and contain 5 bars
        $this->assertStringStartsWith('Bc|', $lines[0]);
        $this->assertEquals(5, substr_count($lines[0], '|'));
    }

    public function testRepeatBarlineStartsNewLine() {
        $input = 'ABCD|EFGH|IJKL|MNOP|:QRST|UVWX|YZAB|CDEF|';
        $result = formatAbcBody($input, '4/4', '1/8');
        $lines = explode("\n", $result);
        // |: should be at the start of a line
        $startsWithRepeat = false;
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '|:')) {
                $startsWithRepeat = true;
            }
        }
        $this->assertTrue($startsWithRepeat);
    }

    public function testOpeningRepeatStaysAfterPickupBarline() {
        $input = "F|:ABCD|EFGH|IJKL|MNOP|QRST|UVWX|";
        $result = formatAbcBody($input, '4/4', '1/8');
        $lines = explode("\n", $result);

        $this->assertStringStartsWith('F|:ABCD|', $lines[0]);
    }

    public function testLeadingOpeningRepeatIsPreservedAtLineStart() {
        $input = "|:ABCD|EFGH|IJKL|MNOP|QRST|";
        $result = formatAbcBody($input, '4/4', '1/8');
        $lines = explode("\n", $result);

        $this->assertStringStartsWith('|:ABCD|', $lines[0]);
    }

    public function testDoubleRepeatCarriesOpeningRepeatToNextLine() {
        $input = "z|EGB EGB|EGB c2 B|EBG EBG|=DFA B2 A|EGB EGB|EGB c2 a|g2 f Bcd|e3 e2::a|gbg efg|";
        $result = formatAbcBody($input, '6/8', '1/8');
        $lines = explode("\n", $result);

        $this->assertStringEndsWith(':|', $lines[1]);
        $this->assertStringStartsWith(':a|', $lines[2]);
    }
}

?>
