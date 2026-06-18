<?php
/**
 * Master test runner — runs all model tests and reports aggregate results.
 *
 * Usage: php tests/model_tests/run_all.php
 */

echo "════════════════════════════════════════════════════════\n";
echo "  MODEL TESTS — Running all model test suites\n";
echo "════════════════════════════════════════════════════════\n";

$tests = [
    'user_test.php',
    'tune_test.php',
    'setting_test.php',
    'collection_test.php',
    'discussion_test.php',
];

$totalPassed = 0;
$totalFailed = 0;

foreach ($tests as $test) {
    $output = [];
    $exitCode = 0;
    exec('php ' . escapeshellarg(__DIR__ . '/' . $test) . ' 2>&1', $output, $exitCode);

    $text = implode("\n", $output);
    echo $text . "\n";

    // Parse pass/fail counts from output
    if (preg_match('/All (\d+) assertions passed/', $text, $m)) {
        $totalPassed += (int)$m[1];
    } elseif (preg_match('/(\d+)\/(\d+) passed, (\d+) FAILED/', $text, $m)) {
        $totalPassed += (int)$m[1];
        $totalFailed += (int)$m[3];
    }
}

echo "════════════════════════════════════════════════════════\n";
echo "  AGGREGATE RESULTS\n";
echo "════════════════════════════════════════════════════════\n\n";

$total = $totalPassed + $totalFailed;
if ($totalFailed === 0) {
    echo "  ✓ All $total assertions passed across all models.\n\n";
} else {
    echo "  ✗ $totalPassed/$total passed, $totalFailed FAILED.\n\n";
}
