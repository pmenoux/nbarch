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
// Retourne true si valide, false sinon (évite les 403 qui déclenchent le WAF)
function csrf_check(?string $redirect_url = null): bool {
    $token = $_POST['_token'] ?? '';
    if (!hash_equals(csrf_token(), $token)) {
        if ($redirect_url) {
            flash('Session expirée. Veuillez réessayer.', 'err');
            redirect($redirect_url);
        }
        http_response_code(403);
        die('Session expirée. Rechargez la page.');
    }
    return true;
}
