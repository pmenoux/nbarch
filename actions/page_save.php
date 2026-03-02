<?php
// POST — Modifier une page fixe

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../admin/auth.php';

require_login();
csrf_check();

$id      = (int)($_POST['id'] ?? 0);
$titre   = trim($_POST['titre'] ?? '');
$contenu = trim($_POST['contenu'] ?? '');

if ($id === 0 || $titre === '') {
    flash('Données invalides.', 'err');
    redirect(APP_URL . '/admin/pages');
}

DB::query(
    'UPDATE pages SET titre = ?, contenu = ? WHERE id = ?',
    [$titre, $contenu ?: null, $id]
);

flash('Page mise à jour.');
redirect(APP_URL . '/admin/pages/edit?id=' . $id);
