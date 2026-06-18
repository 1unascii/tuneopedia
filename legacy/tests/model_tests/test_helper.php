<?php
/**
 * Shared test helper — provides database connection, assertion functions,
 * and test user setup/teardown for model tests.
 */

require_once(__DIR__ . '/../../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

require_once(__DIR__ . '/../../config/database.php');
require_once(__DIR__ . '/../../models/User.php');
require_once(__DIR__ . '/../../models/Tune.php');
require_once(__DIR__ . '/../../models/Setting.php');
require_once(__DIR__ . '/../../models/Collection.php');
require_once(__DIR__ . '/../../models/Discussion.php');

$testPassed = 0;
$testFailed = 0;
$testResults = [];

function assert_true(string $label, bool $condition): void {
    global $testPassed, $testFailed, $testResults;
    if ($condition) {
        $testPassed++;
        $testResults[] = ['label' => $label, 'pass' => true, 'type' => 'assertion'];
    } else {
        $testFailed++;
        $testResults[] = ['label' => $label, 'pass' => false, 'type' => 'assertion'];
    }
}

function log_info(string $message): void {
    global $testResults;
    $testResults[] = ['label' => $message, 'type' => 'info'];
}

function log_data(string $label, $data): void {
    global $testResults;
    if (is_array($data)) {
        $testResults[] = ['label' => $label . ': ' . json_encode($data, JSON_PRETTY_PRINT), 'type' => 'data'];
    } else {
        $testResults[] = ['label' => $label . ': ' . var_export($data, true), 'type' => 'data'];
    }
}

function log_section(string $title): void {
    global $testResults;
    $testResults[] = ['label' => $title, 'type' => 'section'];
}

function assert_equals(string $label, $expected, $actual): void {
    assert_true("$label (expected: " . var_export($expected, true) . ", got: " . var_export($actual, true) . ")", $expected === $actual);
}

function assert_not_null(string $label, $value): void {
    assert_true($label, $value !== null);
}

function assert_null(string $label, $value): void {
    assert_true("$label is null", $value === null);
}

function assert_greater_than(string $label, int $expected, int $actual): void {
    assert_true("$label ($actual > $expected)", $actual > $expected);
}

function print_results(string $testName): void {
    global $testPassed, $testFailed, $testResults;

    // If called via HTTP, output JSON for the HTML test runner
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'name'    => $testName,
            'passed'  => $testPassed,
            'failed'  => $testFailed,
            'results' => $testResults,
        ]);
        return;
    }

    // CLI output
    echo "\n══ $testName ══\n\n";
    foreach ($testResults as $r) {
        if ($r['type'] === 'section') {
            echo "\n  ── {$r['label']} ──\n\n";
        } elseif ($r['type'] === 'info' || $r['type'] === 'data') {
            echo "  ℹ {$r['label']}\n";
        } else {
            $icon = $r['pass'] ? '✓' : '✗';
            $color = $r['pass'] ? 'PASS' : 'FAIL';
            echo "  $icon [$color] {$r['label']}\n";
        }
    }
    echo "\n";
    $total = $testPassed + $testFailed;
    if ($testFailed === 0) {
        echo "  All $total assertions passed.\n";
    } else {
        echo "  $testPassed/$total passed, $testFailed FAILED.\n";
    }
    echo "\n";
}

function reset_results(): void {
    global $testPassed, $testFailed, $testResults;
    $testPassed = 0;
    $testFailed = 0;
    $testResults = [];
}

$timestamp = time();

/**
 * Creates a test user and returns their user_id.
 */
function create_test_user(PDO $pdo, string $suffix = ''): array {
    global $timestamp;
    $username = 'testuser_' . $timestamp . $suffix;
    $email = 'test_' . $timestamp . $suffix . '@test.com';
    $result = User::register($pdo, 'Test', 'User', $username, $email, 'TestPass123!');
    return [
        'user_id'   => $result['user_id'],
        'user_name' => $username,
    ];
}

/**
 * Deletes a test user and all their related data.
 */
function cleanup_test_user(PDO $pdo, int $userId): void {
    $pdo->prepare("DELETE FROM post WHERE thread_id IN (SELECT discussion_thread_id FROM discussion_thread WHERE user_id = ?)")->execute([$userId]);
    $pdo->prepare("DELETE FROM post WHERE user_id = ?")->execute([$userId]);
    $pdo->prepare("DELETE FROM discussion_thread WHERE user_id = ?")->execute([$userId]);
    $collStmt = $pdo->prepare("SELECT collection_id FROM collection WHERE user_id = ?");
    $collStmt->execute([$userId]);
    $cids = $collStmt->fetchAll(PDO::FETCH_COLUMN);
    if ($cids) {
        $ph = implode(',', array_fill(0, count($cids), '?'));
        $pdo->prepare("DELETE FROM collection_tune WHERE collection_id IN ($ph)")->execute($cids);
        $pdo->prepare("DELETE FROM collection WHERE collection_id IN ($ph)")->execute($cids);
    }
    $pdo->prepare("DELETE FROM favorites WHERE user_id = ?")->execute([$userId]);
    $pdo->prepare("DELETE FROM setting_vote WHERE user_id = ?")->execute([$userId]);
    $pdo->prepare("DELETE FROM setting WHERE user_id = ?")->execute([$userId]);
    $pdo->prepare("DELETE FROM user WHERE user_id = ?")->execute([$userId]);
}
