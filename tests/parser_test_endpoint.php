<?php
/**
 * Test endpoint for parser_test.html — accepts ABC text via POST,
 * parses it with ParserController, returns JSON.
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../controllers/ParserController.php';

$abc = $_POST['abc'] ?? '';
if (empty(trim($abc))) {
    echo json_encode(['error' => 'No ABC text provided']);
    exit;
}

echo json_encode(ParserController::parse($abc));
