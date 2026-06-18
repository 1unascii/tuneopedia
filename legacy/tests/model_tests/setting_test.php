<?php
require_once __DIR__ . '/test_helper.php';

$pdo = connect();
$testUser = create_test_user($pdo, '_settingtest');
$userId = $testUser['user_id'];

$tuneId = Tune::create($pdo, 'SettingTestTune_' . $timestamp, 'Jig', 'Traditional', '6/8', 'Gmaj', 'GAB dBG|AGA BGE|', $userId);

// ── Show (findById) ─────────────────────────────────────────────────────────
log_section('Show (findById)');
$settings = Tune::getSettings($pdo, $tuneId, $userId);
$settingId = (int)$settings[0]['setting_id'];
log_data('Test tune', ['tune_id' => $tuneId, 'name' => 'SettingTestTune_' . $timestamp]);
log_data('Test setting_id', $settingId);

$setting = Setting::findById($pdo, $settingId);
log_data('findById result', ['setting_id' => $setting['setting_id'], 'key' => $setting['key_signature'], 'time' => $setting['time_signature'], 'name' => $setting['name']]);
assert_not_null('findById returns setting', $setting);
assert_equals('findById has correct key', 'Gmaj', $setting['key_signature']);
assert_equals('findById has correct time sig', '6/8', $setting['time_signature']);
assert_equals('findById has tune name', 'SettingTestTune_' . $timestamp, $setting['name']);

// ── Show (getForEdit) ───────────────────────────────────────────────────────
log_section('Show (getForEdit)');
$editData = Setting::getForEdit($pdo, $settingId);
log_data('getForEdit result', ['tune_name' => $editData['tune_name'], 'abc' => $editData['abc_transcription'], 'source' => $editData['source'], 'origin' => $editData['origin']]);
assert_not_null('getForEdit returns data', $editData);
assert_equals('getForEdit has tune_name', 'SettingTestTune_' . $timestamp, $editData['tune_name']);
assert_equals('getForEdit has abc_transcription', 'GAB dBG|AGA BGE|', $editData['abc_transcription']);
assert_true('getForEdit includes source column', array_key_exists('source', $editData));
assert_true('getForEdit includes origin column', array_key_exists('origin', $editData));

// ── Update ──────────────────────────────────────────────────────────────────
log_section('Update');
$updateData = [
    'tune_name' => 'SettingTestTune_' . $timestamp, 'tune_type' => 'Jig',
    'time_signature' => '6/8', 'key_signature' => 'Amaj', 'default_note_length' => '1/8',
    'abc_transcription' => 'ABC DEF|GFE DCB|',
    'source' => 'Test source', 'origin' => 'Ireland', 'history' => '',
    'book' => 'Test Book', 'discography' => '', 'transcription_credit' => 'Tester',
    'area' => 'Galway', 'parts' => 'AABB', 'tempo' => '1/4=120', 'lyrics' => '',
];
log_data('Update data', $updateData);

$updated = Setting::update($pdo, $settingId, $updateData);
log_data('Update result', ['key' => $updated['key_signature'], 'abc' => $updated['abc_transcription'], 'source' => $updated['source'], 'origin' => $updated['origin'], 'area' => $updated['area'], 'parts' => $updated['parts']]);
assert_not_null('update returns refreshed data', $updated);
assert_equals('update changed key', 'Amaj', $updated['key_signature']);
assert_equals('update changed abc', 'ABC DEF|GFE DCB|', $updated['abc_transcription']);
assert_equals('update saved source', 'Test source', $updated['source']);
assert_equals('update saved origin', 'Ireland', $updated['origin']);
assert_equals('update saved transcription_credit', 'Tester', $updated['transcription_credit']);
assert_equals('update saved area', 'Galway', $updated['area']);
assert_equals('update saved parts', 'AABB', $updated['parts']);

$editAfter = Setting::getForEdit($pdo, $settingId);
assert_equals('getForEdit reflects updated key', 'Amaj', $editAfter['key_signature']);
assert_equals('getForEdit reflects updated source', 'Test source', $editAfter['source']);

// ── Update nonexistent ──────────────────────────────────────────────────────
log_section('Update nonexistent');
$noUpdate = Setting::update($pdo, 999999, ['time_signature' => '4/4', 'key_signature' => 'C', 'default_note_length' => '1/8', 'abc_transcription' => 'C']);
assert_null('update returns null for nonexistent setting', $noUpdate);

// ── Vote ────────────────────────────────────────────────────────────────────
log_section('Vote');
log_data('Voting on setting_id', $settingId);

$voteResult = Setting::vote($pdo, $settingId, $userId, 1);
log_data('Upvote result', $voteResult);
assert_equals('Upvote returns score of 1', 1, $voteResult['vote_score']);
assert_equals('Upvote returns user_vote of 1', 1, $voteResult['user_vote']);

$voteResult2 = Setting::vote($pdo, $settingId, $userId, 1);
log_data('Same vote again (retract)', $voteResult2);
assert_equals('Same vote again retracts — score 0', 0, $voteResult2['vote_score']);
assert_null('Retracted vote returns null user_vote', $voteResult2['user_vote']);

$voteResult3 = Setting::vote($pdo, $settingId, $userId, -1);
log_data('Downvote result', $voteResult3);
assert_equals('Downvote returns score of -1', -1, $voteResult3['vote_score']);
assert_equals('Downvote returns user_vote of -1', -1, $voteResult3['user_vote']);

// ── findById nonexistent ────────────────────────────────────────────────────
log_section('findById nonexistent');
$noSetting = Setting::findById($pdo, 999999);
assert_null('findById returns null for nonexistent', $noSetting);

// ── Cleanup ─────────────────────────────────────────────────────────────────
Tune::delete($pdo, $tuneId, $userId);
cleanup_test_user($pdo, $userId);
print_results('Setting Model Tests');
