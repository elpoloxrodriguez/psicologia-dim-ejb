<?php
session_start();
header('Content-Type: application/json');

function checkSession() {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        return [
            'logged_in' => true,
            'user' => [
                'id' => $_SESSION['user_id'] ?? null,
                'username' => $_SESSION['username'] ?? null,
                'role' => $_SESSION['role'] ?? null
            ]
        ];
    } else {
        return [
            'logged_in' => false
        ];
    }
}

echo json_encode(checkSession());
?>