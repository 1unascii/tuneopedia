<?php
require_once __DIR__ . '/test_helper.php';

$pdo = connect();
$testUser = create_test_user($pdo, '_usertest');
$userId = $testUser['user_id'];
$userName = $testUser['user_name'];

// ── Create (register) ───────────────────────────────────────────────────────
log_section('Create (register)');
log_data('Test user', ['user_name' => $userName, 'email' => 'test_' . $timestamp . '_usertest@test.com', 'first_name' => 'Test', 'last_name' => 'User', 'password' => 'TestPass123!']);
assert_greater_than('User registered with valid ID', 0, $userId);

// ── Show (findById) ─────────────────────────────────────────────────────────
log_section('Show (findById)');
$user = User::findById($pdo, $userId);
log_data('findById result', ['user_id' => $user['user_id'], 'user_name' => $user['user_name'], 'email' => $user['email']]);
assert_not_null('findById returns user', $user);
assert_equals('findById returns correct username', $userName, $user['user_name']);

// ── Index (existsByUsername / existsByEmail) ─────────────────────────────────
log_section('Index (exists checks)');
log_data('Checking username', $userName);
assert_true('existsByUsername returns true for existing user', User::existsByUsername($pdo, $userName));
log_data('Checking nonexistent username', 'nonexistent_xyz_999');
assert_true('existsByUsername returns false for nonexistent', !User::existsByUsername($pdo, 'nonexistent_xyz_999'));
assert_true('existsByEmail returns true for existing email', User::existsByEmail($pdo, 'test_' . $timestamp . '_usertest@test.com'));
assert_true('existsByEmail returns false for nonexistent', !User::existsByEmail($pdo, 'fake@nobody.com'));

// ── Authenticate ────────────────────────────────────────────────────────────
log_section('Authenticate');
$authed = User::authenticate($pdo, $userName, 'TestPass123!');
log_data('Correct password result', ['user_id' => $authed['user_id'], 'user_name' => $authed['user_name']]);
assert_not_null('authenticate succeeds with correct password', $authed);
assert_equals('authenticate returns correct user_id', $userId, (int)$authed['user_id']);

$badAuth = User::authenticate($pdo, $userName, 'WrongPassword');
log_data('Wrong password result', $badAuth);
assert_null('authenticate fails with wrong password', $badAuth);

// ── Duplicate registration ──────────────────────────────────────────────────
log_section('Duplicate registration');
$dupUser = User::register($pdo, 'Test', 'User', $userName, 'other@test.com', 'Pass123!');
log_data('Duplicate username result', $dupUser);
assert_equals('Duplicate username rejected', 'username_taken', $dupUser['error']);

$dupEmail = User::register($pdo, 'Test', 'User', 'other_user_xyz', 'test_' . $timestamp . '_usertest@test.com', 'Pass123!');
log_data('Duplicate email result', $dupEmail);
assert_equals('Duplicate email rejected', 'email_taken', $dupEmail['error']);

// ── Favorites ───────────────────────────────────────────────────────────────
log_section('Favorites');
$tuneId = Tune::create($pdo, 'FavTestTune_' . $timestamp, 'Reel', 'Traditional', '4/4', 'D', 'ABCD|', $userId);
log_data('Test tune for favorites', ['tune_id' => $tuneId, 'name' => 'FavTestTune_' . $timestamp]);

assert_true('addFavorite succeeds', User::addFavorite($pdo, $userId, $tuneId));
assert_true('hasFavorite returns true after adding', User::hasFavorite($pdo, $userId, $tuneId));
assert_true('addFavorite is idempotent (INSERT IGNORE)', User::addFavorite($pdo, $userId, $tuneId));
assert_true('removeFavorite succeeds', User::removeFavorite($pdo, $userId, $tuneId));
assert_true('hasFavorite returns false after removing', !User::hasFavorite($pdo, $userId, $tuneId));

// ── Delete (cleanup) ────────────────────────────────────────────────────────
log_section('Delete');
Tune::delete($pdo, $tuneId, $userId);
cleanup_test_user($pdo, $userId);
$deleted = User::findById($pdo, $userId);
assert_null('User deleted successfully', $deleted);

print_results('User Model Tests');
