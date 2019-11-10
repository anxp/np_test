<?php

class SyncRequestHandler extends RequestHandler {
    public function generateResponse(string $messageSubject, string $messageType) {
        $url = 'index.php';
        ob_clean();
        header('Location: ' . $url . '?' . $messageType . '=' . urlencode($messageSubject));
        exit();
    }
}
