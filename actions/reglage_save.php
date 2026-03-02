<?php
// POST — Sauvegarder un réglage (accueil_projet_id)

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../nbadmin/auth.php';

require_login();
csrf_check();

$projet_id = $_POST['accueil_projet_id'] ?? '';

if ($projet_id === '' || $projet_id === '0') {
    // Auto = premier projet publié
    set_reglage('accueil_projet_id', null);
} else {
    // Vérifier que le projet existe et est publié
    $projet = DB::fetchOne(
        "SELECT id FROM projets WHERE id = ? AND statut = 'publié'",
        [(int)$projet_id]
    );
    if (!$projet) {
        flash('Projet introuvable.', 'err');
        redirect(APP_URL . '/nbadmin/');
    }
    set_reglage('accueil_projet_id', (string)$projet['id']);
}

flash('Image d\'accueil mise à jour.');
redirect(APP_URL . '/nbadmin/');
