<?php

require_once(__DIR__ . '/../tests/test_api.php');

class TestController {

    public function cleanup() {
        testCleanup();
    }

    public function resetIds() {
        resetAutoIncrements();
    }
}
