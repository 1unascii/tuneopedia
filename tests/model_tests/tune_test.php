<?php
require_once __DIR__ . '/test_helper.php';

$pdo = connect();
$testUser = create_test_user($pdo, '_tunetest');
$userId = $testUser['user_id'];

// ── Create ──────────────────────────────────────────────────────────────────
log_section('Create');
$tuneData = ['name' => 'TestTune_' . $timestamp, 'type' => 'Reel', 'composer' => 'Traditional', 'metre' => '4/4', 'key' => 'Dmaj', 'abc' => 'ABCD EFGA|BAGF EDCB|'];
log_data('Tune data', $tuneData);
$tuneId = Tune::create($pdo, $tuneData['name'], $tuneData['type'], $tuneData['composer'], $tuneData['metre'], $tuneData['key'], $tuneData['abc'], $userId);
log_data('Created tune_id', $tuneId);
assert_greater_than('Tune created with valid ID', 0, $tuneId);

// ── Show (getName) ──────────────────────────────────────────────────────────
log_section('Show (getName)');
$name = Tune::getName($pdo, $tuneId);
log_data('getName(' . $tuneId . ')', $name);
assert_equals('getName returns correct name', 'TestTune_' . $timestamp, $name);

$noName = Tune::getName($pdo, 999999);
log_data('getName(999999)', $noName);
assert_null('getName returns null for nonexistent tune', $noName);

// ── Show (getSettings) ──────────────────────────────────────────────────────
log_section('Show (getSettings)');
$settings = Tune::getSettings($pdo, $tuneId, $userId);
log_data('Settings count', count($settings));
log_data('First setting', ['setting_id' => $settings[0]['setting_id'], 'key' => $settings[0]['key_signature'], 'time' => $settings[0]['time_signature']]);
assert_greater_than('getSettings returns at least 1 setting', 0, count($settings));
assert_equals('Setting has correct key', 'Dmaj', $settings[0]['key_signature']);
assert_equals('Setting has correct time sig', '4/4', $settings[0]['time_signature']);

// ── Show (getNotes) ─────────────────────────────────────────────────────────
log_section('Show (getNotes)');
$notes = Tune::getNotes($pdo, $tuneId);
log_data('Notes count', count($notes));
assert_true('getNotes returns array (may be empty)', is_array($notes));

// ── Index (getAllGroupedByType) ──────────────────────────────────────────────
log_section('Index (getAllGroupedByType)');
[$grouped, $typeNames] = Tune::getAllGroupedByType($pdo, $userId);
log_data('Number of type groups', count($grouped));
log_data('Type names', $typeNames);
assert_true('getAllGroupedByType returns groups', count($grouped) > 0);
assert_true('getAllGroupedByType returns type names', count($typeNames) > 0);

$found = false;
foreach ($grouped as $items) {
    foreach ($items as $item) {
        if ((int)$item['tune_id'] === $tuneId) { $found = true; break 2; }
    }
}
assert_true('Our test tune appears in getAllGroupedByType', $found);

// ── Lookups ─────────────────────────────────────────────────────────────────
log_section('Lookups');
$types = Tune::getAllTypes($pdo);
log_data('Tune types count', count($types));
assert_true('getAllTypes returns array', count($types) > 0);

$composers = Tune::getAllComposers($pdo);
log_data('Composers count', count($composers));
assert_true('getAllComposers returns array', is_array($composers));

$typeId = Tune::getOrCreateType($pdo, 'Reel');
log_data('getOrCreateType("Reel")', $typeId);
assert_greater_than('getOrCreateType returns valid ID for existing type', 0, $typeId);

$newTypeName = 'TestType_' . $timestamp;
$newTypeId = Tune::getOrCreateType($pdo, $newTypeName);
log_data('getOrCreateType("' . $newTypeName . '")', $newTypeId);
assert_greater_than('getOrCreateType creates new type', 0, $newTypeId);

$composerId = Tune::getOrCreateComposer($pdo, 'Traditional');
log_data('getOrCreateComposer("Traditional")', $composerId);
assert_greater_than('getOrCreateComposer returns valid ID', 0, $composerId);

// ── Favorites grouped ───────────────────────────────────────────────────────
log_section('Favorites (getFavoritesGroupedByType)');
User::addFavorite($pdo, $userId, $tuneId);
[$favGrouped, $favTypes] = Tune::getFavoritesGroupedByType($pdo, $userId);
log_data('Favorite groups count', count($favGrouped));
$favFound = false;
foreach ($favGrouped as $items) {
    foreach ($items as $item) {
        if ((int)$item['tune_id'] === $tuneId) { $favFound = true; break 2; }
    }
}
assert_true('getFavoritesGroupedByType includes favorited tune', $favFound);
User::removeFavorite($pdo, $userId, $tuneId);

// ── Delete ──────────────────────────────────────────────────────────────────
log_section('Delete');
log_data('Deleting tune_id', $tuneId);
$deleted = Tune::delete($pdo, $tuneId, $userId);
assert_true('Tune deleted successfully', $deleted);
assert_null('Tune no longer exists after delete', Tune::getName($pdo, $tuneId));

$pdo->prepare("DELETE FROM tune_type WHERE name = ?")->execute([$newTypeName]);
cleanup_test_user($pdo, $userId);
print_results('Tune Model Tests');
