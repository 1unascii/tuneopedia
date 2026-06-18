<?php
/**
 * Pest Test Runner & Database Seeder Dashboard
 *
 * A browser-based tool for running Pest tests and Laravel seeders.
 * Accessed locally at: http://localhost/tuneopedia_dev/public/run-tests.php
 *
 * Features:
 *   - Run all tests with optional --coverage and --parallel flags
 *   - Run tests by group (Authentication, Tunes & Settings, Discussions)
 *   - Run individual test files
 *   - Run individual database seeders
 *   - "Run All Tests + Seed" runs the full suite then reseeds via DatabaseSeeder
 *
 * Note: Coverage requires PCOV or Xdebug to be enabled in php.ini.
 *       Parallel requires brianium/paratest to be installed.
 */

// Disable execution timeout — tests can take a while
set_time_limit(0);

/**
 * Run a shell command and return clean output.
 * Strips ANSI escape codes (colors, cursor movement) so output
 * renders as readable plain text in the browser.
 */
function run(string $command): string
{
    $output = shell_exec($command . ' 2>&1') ?? '';
    // Strip ANSI escape sequences (colors, bold, cursor moves, etc.)
    return preg_replace('/\x1B\[[0-9;]*[A-Za-z]/', '', $output);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pest Test Runner</title>
    <style>
        body { background: #1e1e1e; color: #d4d4d4; font-family: monospace; padding: 20px; }
        h1 { color: #569cd6; }
        h2 { color: #4ec9b0; margin-top: 30px; }
        h3 { color: #ce9178; margin-top: 16px; margin-bottom: 8px; }
        pre { background: #0d0d0d; padding: 15px; border-radius: 6px; overflow-x: auto; white-space: pre-wrap; }
        button { background: #569cd6; color: #1e1e1e; border: none; padding: 10px 24px; font-size: 16px; font-family: monospace; cursor: pointer; border-radius: 6px; }
        button:hover { background: #4a8abf; }
        .btn-sm { padding: 6px 14px; font-size: 13px; }
        label { cursor: pointer; margin-right: 16px; }
        .controls { display: flex; align-items: center; gap: 12px; margin-top: 16px; }
        .test-grid { display: flex; flex-wrap: wrap; gap: 8px; }
    </style>
</head>
<body>
    <h1>Pest Test Runner</h1>

    <?php
        // ---------------------------------------------------------------
        // Scan test files and group them into categories
        // ---------------------------------------------------------------
        // New test files are automatically discovered. They land in "Other"
        // unless their basename is added to one of the category arrays below.
        // ---------------------------------------------------------------

        $projectDir = dirname(__DIR__);

        // Define the category groups — order here determines display order
        $groups = [
            'Authentication' => [],
            'Tunes & Settings' => [],
            'Discussions' => [],
            'Other' => [],
        ];

        // Map test file basenames (without .php) to their category
        $authFiles = ['AuthenticationTest', 'EmailVerificationTest', 'PasswordConfirmationTest',
            'PasswordUpdateTest', 'PasswordResetTest', 'RegistrationTest', 'ProfileTest'];

        $tuneFiles = ['TuneControllerTest', 'SettingControllerTest'];

        $discussionFiles = ['DiscussionThreadControllerTest', 'DiscussionThreadPolicyTest'];

        // Recursively scan the tests/ directory for *Test.php files
        $testsDir = $projectDir . '/tests';
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($testsDir)) as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), 'Test.php')) {
                // Convert absolute path to a relative path pest can use
                $relative = str_replace($projectDir . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $relative = str_replace('\\', '/', $relative);
                $basename = basename($file->getFilename(), '.php');

                // Sort into the matching group, or fall back to "Other"
                if (in_array($basename, $authFiles)) {
                    $groups['Authentication'][] = $relative;
                } elseif (in_array($basename, $tuneFiles)) {
                    $groups['Tunes & Settings'][] = $relative;
                } elseif (in_array($basename, $discussionFiles)) {
                    $groups['Discussions'][] = $relative;
                } else {
                    $groups['Other'][] = $relative;
                }
            }
        }

        // Sort files alphabetically within each group
        foreach ($groups as &$g) sort($g);
        unset($g);
    ?>

    <!-- =============================================================== -->
    <!-- OUTPUT SECTION — only rendered on POST (after a button is clicked) -->
    <!-- =============================================================== -->
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <?php
            // Build CLI flags from checkboxes (only used by "Run All Tests + Seed")
            $flags = '';
            if (isset($_POST['coverage'])) $flags .= ' --coverage';
            if (isset($_POST['parallel'])) $flags .= ' --parallel';

            // Check if a specific test file was requested
            $testFile = $_POST['test_file'] ?? null;
        ?>

        <?php if (isset($_POST['group']) && isset($groups[$_POST['group']])): ?>
            <!-- Run a group of tests (e.g. all Authentication tests at once) -->
            <?php $groupFiles = implode(' ', $groups[$_POST['group']]); ?>
            <h2>Running: <?= htmlspecialchars($_POST['group']) ?></h2>
            <pre><?= htmlspecialchars(run("cd \"$projectDir\" && php vendor/bin/pest $groupFiles")) ?></pre>

        <?php elseif ($testFile): ?>
            <!-- Run a single test file -->
            <h2>Running: <?= htmlspecialchars($testFile) ?></h2>
            <pre><?= htmlspecialchars(run("cd \"$projectDir\" && php vendor/bin/pest $testFile")) ?></pre>

        <?php elseif (isset($_POST['seeder'])): ?>
            <!-- Run a single seeder via artisan -->
            <h2>Running: <?= htmlspecialchars($_POST['seeder']) ?></h2>
            <pre><?= htmlspecialchars(run("cd \"$projectDir\" && php artisan db:seed --class=" . escapeshellarg($_POST['seeder']) . " --no-interaction")) ?></pre>

        <?php elseif (isset($_POST['phpstan'])): ?>
            <!-- Run PHPStan with dead code detection -->
            <h2>Running: PHPStan + Dead Code Detector</h2>
            <pre><?= htmlspecialchars(run("cd \"$projectDir\" && php vendor/bin/phpstan analyse --memory-limit=512M")) ?></pre>

        <?php elseif (isset($_POST['artisan_cmd'])): ?>
            <!-- Run an artisan command -->
            <h2>Running: <?= htmlspecialchars($_POST['artisan_cmd']) ?></h2>
            <pre><?= htmlspecialchars(run("cd \"$projectDir\" && php artisan " . escapeshellarg($_POST['artisan_cmd']))) ?></pre>

        <?php else: ?>
            <!-- Run ALL tests (with optional coverage/parallel flags), then reseed -->
            <h2>Running All Tests<?= $flags ? " ($flags)" : '' ?></h2>
            <pre><?= htmlspecialchars(run("cd \"$projectDir\" && php vendor/bin/pest$flags")) ?></pre>

            <!-- Reseed via DatabaseSeeder which calls TuneSeeder, SettingVoteSeeder,
                 DiscussionThreadSeeder, and creates the test user -->
            <h2>Seeding Database</h2>
            <pre><?= htmlspecialchars(run("cd \"$projectDir\" && php artisan db:seed --no-interaction")) ?></pre>
        <?php endif; ?>
    <?php endif; ?>

    <!-- =============================================================== -->
    <!-- CONTROLS — checkboxes for coverage & parallel (Run All only)    -->
    <!-- =============================================================== -->
    <div class="controls">
        <label><input type="checkbox" id="cb-coverage"> Coverage</label>
        <label><input type="checkbox" id="cb-parallel"> Parallel</label>
    </div>

    <!-- Run All Tests + Seed button -->
    <!-- Hidden inputs are enabled/disabled by JavaScript based on checkbox state -->
    <form method="POST" class="test-form" style="margin-top: 12px;">
        <input type="hidden" name="coverage" class="flag-coverage" disabled>
        <input type="hidden" name="parallel" class="flag-parallel" disabled>
        <button type="submit">Run All Tests + Seed</button>
    </form>

    <!-- =============================================================== -->
    <!-- GROUP BUTTONS — run all tests in a category at once             -->
    <!-- =============================================================== -->
    <h3>Run by Group</h3>
    <div class="test-grid">
        <?php foreach ($groups as $groupName => $files): ?>
            <?php if (empty($files)) continue; ?>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="group" value="<?= htmlspecialchars($groupName) ?>">
                <button type="submit" class="btn-sm"><?= htmlspecialchars($groupName) ?></button>
            </form>
        <?php endforeach; ?>
    </div>

    <!-- =============================================================== -->
    <!-- INDIVIDUAL TEST BUTTONS — grouped by category for readability   -->
    <!-- =============================================================== -->
    <?php foreach ($groups as $groupName => $files): ?>
        <?php if (empty($files)) continue; ?>
        <h3><?= htmlspecialchars($groupName) ?></h3>
        <div class="test-grid">
            <?php foreach ($files as $file): ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="test_file" value="<?= htmlspecialchars($file) ?>">
                    <button type="submit" class="btn-sm"><?= htmlspecialchars(basename($file, '.php')) ?></button>
                </form>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <!-- =============================================================== -->
    <!-- SEEDER BUTTONS — dynamically scanned from database/seeders/     -->
    <!-- New seeder files appear automatically as buttons.               -->
    <!-- =============================================================== -->
    <h3>Run Seeder</h3>
    <div class="test-grid">
        <?php
            $seedersDir = $projectDir . '/database/seeders';
            foreach (new DirectoryIterator($seedersDir) as $file) {
                // Skip directories and non-PHP files
                if ($file->isDot() || !str_ends_with($file->getFilename(), '.php')) continue;
                $seederClass = basename($file->getFilename(), '.php');
        ?>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="seeder" value="<?= htmlspecialchars($seederClass) ?>">
                <button type="submit" class="btn-sm"><?= htmlspecialchars($seederClass) ?></button>
            </form>
        <?php } ?>
    </div>

    <!-- =============================================================== -->
    <!-- DEAD CODE SCANNER                                               -->
    <!-- =============================================================== -->
    <h3>Code Analysis</h3>
    <div class="test-grid">
        <form method="POST" style="display:inline;">
            <input type="hidden" name="artisan_cmd" value="mrkindy:deadcontroller">
            <button type="submit" class="btn-sm">Dead Controllers</button>
        </form>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="artisan_cmd" value="mrkindy:deadmethods">
            <button type="submit" class="btn-sm">Dead Methods</button>
        </form>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="phpstan" value="1">
            <button type="submit" class="btn-sm">PHPStan + Dead Code</button>
        </form>
    </div>

    <!-- =============================================================== -->
    <!-- JavaScript — syncs the coverage/parallel checkboxes with the    -->
    <!-- hidden inputs in the "Run All Tests + Seed" form.               -->
    <!-- Disabled hidden inputs are not submitted, so unchecked boxes    -->
    <!-- won't send their flags to the server.                           -->
    <!-- =============================================================== -->
    <script>
        const cbCoverage = document.getElementById('cb-coverage');
        const cbParallel = document.getElementById('cb-parallel');
        const coverageFields = document.querySelectorAll('.flag-coverage');
        const parallelFields = document.querySelectorAll('.flag-parallel');

        function syncFlags() {
            coverageFields.forEach(f => f.disabled = !cbCoverage.checked);
            parallelFields.forEach(f => f.disabled = !cbParallel.checked);
        }

        cbCoverage.addEventListener('change', syncFlags);
        cbParallel.addEventListener('change', syncFlags);
    </script>
</body>
</html>
