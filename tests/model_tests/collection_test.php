<?php
require_once __DIR__ . '/test_helper.php';

$pdo = connect();
$testUser = create_test_user($pdo, '_colltest');
$userId = $testUser['user_id'];

$tuneId1 = Tune::create($pdo, 'CollTestReel_' . $timestamp, 'Reel', 'Traditional', '4/4', 'D', 'ABCD|', $userId);
$tuneId2 = Tune::create($pdo, 'CollTestJig_' . $timestamp, 'Jig', 'Traditional', '6/8', 'G', 'GAB|', $userId);
$tuneId3 = Tune::create($pdo, 'CollTestHornpipe_' . $timestamp, 'Hornpipe', 'Traditional', '4/4', 'A', 'A2AB|', $userId);

// ── Create ──────────────────────────────────────────────────────────────────
log_section('Create');
$collName = 'TestCollection_' . $timestamp;
log_data('Collection data', ['name' => $collName, 'author' => 'Test Author', 'description' => 'Test description', 'is_shared' => true, 'user_id' => $userId]);
log_data('Test tunes', ['tune_id_1' => $tuneId1, 'tune_id_2' => $tuneId2, 'tune_id_3' => $tuneId3]);
$collId = Collection::create($pdo, $collName, 'Test Author', 'Test description', true, $userId);
log_data('Created collection_id', $collId);
assert_greater_than('Collection created with valid ID', 0, $collId);

// ── existsByName ────────────────────────────────────────────────────────────
log_section('existsByName');
assert_true('existsByName returns true for existing', Collection::existsByName($pdo, $collName));
assert_true('existsByName returns false for nonexistent', !Collection::existsByName($pdo, 'NonexistentColl_XYZ'));

// ── Private collection ──────────────────────────────────────────────────────
log_section('Create (private)');
$privateCollName = 'PrivateTestColl_' . $timestamp;
log_data('Private collection', ['name' => $privateCollName, 'is_shared' => false]);
$privateCollId = Collection::create($pdo, $privateCollName, '', '', false, $userId);
log_data('Created private collection_id', $privateCollId);
assert_greater_than('Private collection created', 0, $privateCollId);

// ── AddTunes ────────────────────────────────────────────────────────────────
log_section('addTunes');
log_data('Adding tunes to collection', ['collection_id' => $collId, 'tune_ids' => [$tuneId1, $tuneId2, $tuneId3]]);
Collection::addTunes($pdo, $collId, [$tuneId1, $tuneId2, $tuneId3]);

$stmt = $pdo->prepare("SELECT COUNT(*) FROM collection_tune WHERE collection_id = ?");
$stmt->execute([$collId]);
$tuneCount = (int)$stmt->fetchColumn();
assert_equals('3 tunes added to collection', 3, $tuneCount);

$stmt = $pdo->prepare("SELECT tune_id, position FROM collection_tune WHERE collection_id = ? ORDER BY position");
$stmt->execute([$collId]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
log_data('Positions', $rows);
assert_equals('First tune position is 1', 1, (int)$rows[0]['position']);
assert_equals('Second tune position is 2', 2, (int)$rows[1]['position']);
assert_equals('Third tune position is 3', 3, (int)$rows[2]['position']);

// ── Index (getAllWithTunes) ──────────────────────────────────────────────────
log_section('Index (getAllWithTunes)');
$allCollections = Collection::getAllWithTunes($pdo, $userId);
log_data('Total collections returned', count($allCollections));

$foundColl = false;
$foundTuneCount = 0;
$foundIsShared = null;
foreach ($allCollections as $coll) {
    if ((int)$coll['collection_id'] === $collId) {
        $foundColl = true;
        foreach ($coll['tunes'] as $type) { $foundTuneCount += count($type['items']); }
        $foundIsShared = (int)$coll['is_shared'];
        break;
    }
}
log_data('Our collection', ['found' => $foundColl, 'tune_count' => $foundTuneCount, 'is_shared' => $foundIsShared]);
assert_true('Our collection appears in getAllWithTunes', $foundColl);
assert_equals('Collection has 3 tunes', 3, $foundTuneCount);
assert_equals('Collection is_shared is 1', 1, $foundIsShared);

$foundPrivate = false;
foreach ($allCollections as $coll) {
    if ((int)$coll['collection_id'] === $privateCollId) {
        $foundPrivate = true;
        assert_equals('Private collection is_shared is 0', 0, (int)$coll['is_shared']);
        break;
    }
}
assert_true('Private collection appears for owner', $foundPrivate);

// ── is_favorited ────────────────────────────────────────────────────────────
log_section('is_favorited in getAllWithTunes');
User::addFavorite($pdo, $userId, $tuneId1);
log_data('Favorited tune', $tuneId1);
$withFav = Collection::getAllWithTunes($pdo, $userId);
$favStatus = null;
foreach ($withFav as $coll) {
    if ((int)$coll['collection_id'] === $collId) {
        foreach ($coll['tunes'] as $type) {
            foreach ($type['items'] as $item) {
                if ((int)$item['tune_id'] === $tuneId1) { $favStatus = (int)$item['is_favorited']; }
            }
        }
        break;
    }
}
assert_equals('Favorited tune shows is_favorited=1', 1, $favStatus);
User::removeFavorite($pdo, $userId, $tuneId1);

// ── Delete ──────────────────────────────────────────────────────────────────
log_section('Delete');
log_data('Deleting collections', [$collId, $privateCollId]);
$pdo->prepare("DELETE FROM collection_tune WHERE collection_id IN (?, ?)")->execute([$collId, $privateCollId]);
$pdo->prepare("DELETE FROM collection WHERE collection_id IN (?, ?)")->execute([$collId, $privateCollId]);

$stmt = $pdo->prepare("SELECT COUNT(*) FROM collection WHERE collection_id = ?");
$stmt->execute([$collId]);
assert_equals('Collection deleted', 0, (int)$stmt->fetchColumn());

Tune::delete($pdo, $tuneId1, $userId);
Tune::delete($pdo, $tuneId2, $userId);
Tune::delete($pdo, $tuneId3, $userId);
cleanup_test_user($pdo, $userId);
print_results('Collection Model Tests');
