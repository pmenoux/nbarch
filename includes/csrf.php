<?php

// Génère ou retourne le token CSRF de la session
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Champ hidden HTML pour les formulaires
function csrf_field(): string {
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

// Vérifie le token CSRF (à appeler dans chaque action POST)
function csrf_check(): void {
    $token = $_POST['_token'] ?? '';
    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        die('Session expirée. Rechargez la page.');
    }
}
