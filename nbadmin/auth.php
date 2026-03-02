<?php

// Bloque l'accès si non connecté
function require_login(): void {
    if (empty($_SESSION['admin_id'])) {
        redirect(APP_URL . '/nbadmin/login');
    }
}

// Retourne l'admin connecté ou false
function current_admin(): array|false {
    if (empty($_SESSION['admin_id'])) return false;
    return DB::fetchOne(
        'SELECT id, login FROM utilisateurs WHERE id = ?',
        [$_SESSION['admin_id']]
    );
}
