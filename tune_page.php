<?php
include_once('connect.php');
include_once('functions.php');

$tune_id = intval($_GET['tune_id'] ?? 0);
if (!$tune_id) {
    echo '<p class="error-message">Tune not found.</p>';
    exit;
}

$pdo = connect();

$stmt = $pdo->prepare(file_get_contents('sql/get_tune_page.sql'));
$stmt->execute([':tune_id' => $tune_id]);
$tune = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tune) {
    echo '<p class="error-message">Tune not found.</p>';
    exit;
}

// Fetch tune-level notes (added via annotated collections)
$tuneNotes = [];
try {
    $stmt = $pdo->prepare("SELECT note FROM tune_note WHERE tune_id = :tune_id");
    $stmt->execute([':tune_id' => $tune_id]);
    $tuneNotes = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}
?>
<link href="css/tune_page.css?v=2" rel="stylesheet" type="text/css"/>

<div id="tune-page">

    <h2 class="tune-page-title"><?= htmlspecialchars($tune['tune_name']) ?></h2>
    <p class="tune-page-type"><?= htmlspecialchars($tune['tune_type_name']) ?></p>

    <?php if (!empty($tune['abc_transcription'])): ?>
    <?php
        $abcString =
            "X:" . intval($tune['setting_id']) . "\n" .
            "T:" . $tune['tune_name'] . "\n" .
            "M:" . $tune['time_signature'] . "\n" .
            "L:" . ($tune['default_note_length'] ?? '1/8') . "\n" .
            "K:" . $tune['key_signature'] . "\n" .
            $tune['abc_transcription'];
    ?>
    <script id="tune-abc-data" type="application/json"><?= json_encode($abcString) ?></script>
    <div id="tune-notation"></div>
    <?php else: ?>
    <p class="tune-page-no-notation">No notation available for this tune yet.</p>
    <?php endif; ?>

    <?php if (!empty($tuneNotes)): ?>
    <div class="tune-page-notes">
        <?php foreach ($tuneNotes as $note): ?>
        <p><?= nl2br(htmlspecialchars($note)) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($tune['setting_notes'])): ?>
    <div class="tune-page-notes">
        <p><?= nl2br(htmlspecialchars($tune['setting_notes'])) ?></p>
    </div>
    <?php endif; ?>

</div>
