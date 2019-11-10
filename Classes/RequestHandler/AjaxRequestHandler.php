<?php

class AjaxRequestHandler extends RequestHandler {
    public function generateResponse(string $messageSubject, string $messageType) {
        $response = [
            'subject' => $messageSubject,
            'type' => $messageType,
        ];

        echo json_encode($response);
        exit;
    }
}